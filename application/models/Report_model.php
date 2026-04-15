<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model
{
    public function get_report($start, $end)
    {
        $income = $this->db
            ->where('payment_date >=', $start)
            ->where('payment_date <=', $end)
            ->get('mp_payments')
            ->result_array();

        $expenses = $this->db
            ->where('expense_date >=', $start)
            ->where('expense_date <=', $end)
            ->get('mp_expenses')
            ->result_array();

        $receivables = $this->db
            ->select('i.*, c.name AS client_name')
            ->from('mp_invoices i')
            ->join('mp_clients c', 'c.id = i.client_id', 'left')
            ->where('i.invoice_date >=', $start)
            ->where('i.invoice_date <=', $end)
            ->where('i.total > i.paid_amount')
            ->order_by('i.due_date', 'ASC')
            ->get()
            ->result_array();

        return array(
            'income' => $income,
            'expenses' => $expenses,
            'receivables' => $receivables,
        );
    }
}
