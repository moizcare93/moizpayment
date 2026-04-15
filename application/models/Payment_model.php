<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model
{
    public function all_income()
    {
        return $this->db
            ->select('p.*, c.name AS client_name, i.invoice_number')
            ->from('mp_payments p')
            ->join('mp_clients c', 'c.id = p.client_id', 'left')
            ->join('mp_invoices i', 'i.id = p.invoice_id', 'left')
            ->order_by('p.payment_date', 'DESC')
            ->get()
            ->result_array();
    }

    public function create_income($data)
    {
        $this->db->insert('mp_payments', $data);
        return $this->db->insert_id();
    }

    public function delete_income($id)
    {
        $payment = $this->db->get_where('mp_payments', array('id' => $id))->row_array();
        if ($payment) {
            $this->db->delete('mp_payments', array('id' => $id));
        }
        return $payment;
    }
}
