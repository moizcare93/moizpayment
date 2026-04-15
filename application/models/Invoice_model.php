<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model
{
    public function all()
    {
        $rows = $this->db
            ->select('i.*, c.name AS client_name')
            ->from('mp_invoices i')
            ->join('mp_clients c', 'c.id = i.client_id', 'left')
            ->order_by('i.id', 'DESC')
            ->get()
            ->result_array();

        foreach ($rows as &$row) {
            $row['status'] = $this->normalize_status($row);
        }

        return $rows;
    }

    public function recent($limit = 5)
    {
        return array_slice($this->all(), 0, $limit);
    }

    public function find($id)
    {
        $invoice = $this->db->get_where('mp_invoices', array('id' => $id))->row_array();
        if (!$invoice) {
            return NULL;
        }

        $invoice['items'] = $this->db
            ->order_by('id', 'ASC')
            ->get_where('mp_invoice_items', array('invoice_id' => $id))
            ->result_array();

        $invoice['payments'] = $this->db
            ->order_by('payment_date', 'DESC')
            ->get_where('mp_payments', array('invoice_id' => $id))
            ->result_array();

        $invoice['status'] = $this->normalize_status($invoice);

        return $invoice;
    }

    public function next_number($prefix)
    {
        $last = $this->db->select('invoice_number')->order_by('id', 'DESC')->limit(1)->get('mp_invoices')->row_array();
        preg_match('/(\d{4})$/', $last['invoice_number'] ?? '', $matches);
        $last_number = isset($matches[1]) ? (int) $matches[1] : 0;
        return document_number($prefix, $last_number);
    }

    public function save($header, $items, $id = NULL)
    {
        $this->db->trans_start();

        if ($id) {
            $this->db->where('id', $id)->update('mp_invoices', $header);
            $this->db->delete('mp_invoice_items', array('invoice_id' => $id));
        } else {
            $this->db->insert('mp_invoices', $header);
            $id = $this->db->insert_id();
        }

        foreach ($items as $item) {
            $item['invoice_id'] = $id;
            $this->db->insert('mp_invoice_items', $item);
        }

        $this->db->trans_complete();
        return $id;
    }

    public function add_payment($data)
    {
        $this->db->insert('mp_payments', $data);
        $invoice_id = (int) $data['invoice_id'];
        $this->refresh_paid_amount($invoice_id);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        $this->db->delete('mp_invoice_items', array('invoice_id' => $id));
        $this->db->delete('mp_payments', array('invoice_id' => $id));
        return $this->db->delete('mp_invoices', array('id' => $id));
    }

    public function refresh_paid_amount($invoice_id)
    {
        $sum = $this->db
            ->select_sum('amount')
            ->where('invoice_id', $invoice_id)
            ->get('mp_payments')
            ->row_array();

        $paid_amount = (float) ($sum['amount'] ?? 0);
        $invoice = $this->db->get_where('mp_invoices', array('id' => $invoice_id))->row_array();
        if (!$invoice) {
            return;
        }

        $status = 'sent';
        if ($paid_amount >= (float) $invoice['total'] && $invoice['total'] > 0) {
            $status = 'paid';
        } elseif ($paid_amount > 0) {
            $status = 'partial';
        } elseif (!empty($invoice['due_date']) && strtotime($invoice['due_date']) < strtotime(date('Y-m-d'))) {
            $status = 'overdue';
        } elseif (!empty($invoice['status']) && $invoice['status'] === 'draft') {
            $status = 'draft';
        }

        $this->db->where('id', $invoice_id)->update('mp_invoices', array(
            'paid_amount' => $paid_amount,
            'status' => $status,
        ));
    }

    private function normalize_status($invoice)
    {
        if ((float) $invoice['paid_amount'] >= (float) $invoice['total'] && (float) $invoice['total'] > 0) {
            return 'paid';
        }

        if ((float) $invoice['paid_amount'] > 0) {
            return 'partial';
        }

        if (!empty($invoice['status']) && $invoice['status'] === 'cancelled') {
            return 'cancelled';
        }

        if (!empty($invoice['status']) && $invoice['status'] === 'draft') {
            return 'draft';
        }

        if (!empty($invoice['due_date']) && strtotime($invoice['due_date']) < strtotime(date('Y-m-d'))) {
            return 'overdue';
        }

        return 'sent';
    }
}
