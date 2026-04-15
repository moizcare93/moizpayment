<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function summary()
    {
        $month_start = date('Y-m-01');
        $month_end = date('Y-m-t');

        $invoice_month = $this->sum('mp_invoices', 'total', 'invoice_date', $month_start, $month_end);
        $income_month = $this->sum('mp_payments', 'amount', 'payment_date', $month_start, $month_end);
        $expense_month = $this->sum('mp_expenses', 'amount', 'expense_date', $month_start, $month_end);
        $outstanding = $this->db
            ->select('COALESCE(SUM(total - paid_amount), 0) AS balance', FALSE)
            ->where('status !=', 'cancelled')
            ->get('mp_invoices')
            ->row_array();
        $overdue = $this->db->where('due_date <', date('Y-m-d'))->where('total > paid_amount')->count_all_results('mp_invoices');

        return array(
            'invoice_month' => (float) $invoice_month,
            'income_month' => (float) $income_month,
            'expense_month' => (float) $expense_month,
            'outstanding' => (float) ($outstanding['balance'] ?? 0),
            'overdue_count' => (int) $overdue,
            'net_cashflow' => (float) $income_month - (float) $expense_month,
        );
    }

    public function income_vs_expense()
    {
        $labels = array();
        $income = array();
        $expense = array();

        for ($i = 11; $i >= 0; $i--) {
            $start = date('Y-m-01', strtotime("-{$i} months"));
            $end = date('Y-m-t', strtotime($start));
            $labels[] = date('M Y', strtotime($start));
            $income[] = (float) $this->sum('mp_payments', 'amount', 'payment_date', $start, $end);
            $expense[] = (float) $this->sum('mp_expenses', 'amount', 'expense_date', $start, $end);
        }

        return compact('labels', 'income', 'expense');
    }

    private function sum($table, $field, $date_field, $start, $end)
    {
        $row = $this->db
            ->select_sum($field)
            ->where($date_field . ' >=', $start)
            ->where($date_field . ' <=', $end)
            ->get($table)
            ->row_array();

        return $row[$field] ?? 0;
    }
}
