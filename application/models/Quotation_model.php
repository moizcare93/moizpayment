<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation_model extends CI_Model
{
    public function all()
    {
        return $this->db
            ->select('q.*, c.name AS client_name')
            ->from('mp_quotations q')
            ->join('mp_clients c', 'c.id = q.client_id', 'left')
            ->order_by('q.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function find($id)
    {
        $quotation = $this->db->get_where('mp_quotations', array('id' => $id))->row_array();
        if (!$quotation) {
            return NULL;
        }

        $quotation['items'] = $this->db
            ->order_by('id', 'ASC')
            ->get_where('mp_quotation_items', array('quotation_id' => $id))
            ->result_array();

        return $quotation;
    }

    public function next_number($prefix)
    {
        $last = $this->db->select('quotation_number')->order_by('id', 'DESC')->limit(1)->get('mp_quotations')->row_array();
        preg_match('/(\d{4})$/', $last['quotation_number'] ?? '', $matches);
        $last_number = isset($matches[1]) ? (int) $matches[1] : 0;
        return document_number($prefix, $last_number);
    }

    public function save($header, $items, $id = NULL)
    {
        $this->db->trans_start();

        if ($id) {
            $this->db->where('id', $id)->update('mp_quotations', $header);
            $this->db->delete('mp_quotation_items', array('quotation_id' => $id));
        } else {
            $this->db->insert('mp_quotations', $header);
            $id = $this->db->insert_id();
        }

        foreach ($items as $item) {
            $item['quotation_id'] = $id;
            $this->db->insert('mp_quotation_items', $item);
        }

        $this->db->trans_complete();
        return $id;
    }

    public function delete($id)
    {
        $this->db->delete('mp_quotation_items', array('quotation_id' => $id));
        return $this->db->delete('mp_quotations', array('id' => $id));
    }
}
