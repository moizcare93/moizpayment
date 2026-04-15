<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Report_model');
    }

    public function index()
    {
        $start = $this->input->get('start') ?: date('Y-m-01');
        $end = $this->input->get('end') ?: date('Y-m-t');
        $report = $this->Report_model->get_report($start, $end);

        $income_total = array_sum(array_column($report['income'], 'amount'));
        $expense_total = array_sum(array_column($report['expenses'], 'amount'));
        $receivable_total = 0;
        foreach ($report['receivables'] as $row) {
            $receivable_total += (float) $row['total'] - (float) $row['paid_amount'];
        }

        $this->render('reports/index', array(
            'page_title' => 'Laporan',
            'report' => $report,
            'start' => $start,
            'end' => $end,
            'income_total' => $income_total,
            'expense_total' => $expense_total,
            'receivable_total' => $receivable_total,
        ));
    }
}
