<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Dashboard_model', 'Invoice_model'));
    }

    public function index()
    {
        $this->render('dashboard/index', array(
            'page_title' => 'Dashboard',
            'summary' => $this->Dashboard_model->summary(),
            'trend' => $this->Dashboard_model->income_vs_expense(),
            'recent_invoices' => $this->Invoice_model->recent(),
        ));
    }
}
