<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Payment_model', 'Expense_model', 'Client_model', 'Invoice_model'));
    }

    public function income()
    {
        $this->render('finance/income', array(
            'page_title' => 'Uang Masuk',
            'income_rows' => $this->Payment_model->all_income(),
            'clients' => $this->Client_model->all(),
            'invoices' => $this->Invoice_model->all(),
        ));
    }

    public function create_income()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('amount', 'Nominal', 'required|numeric');
            $this->form_validation->set_rules('payment_date', 'Tanggal', 'required');

            if ($this->form_validation->run()) {
                $payload = array(
                    'payment_code' => 'PAY-' . date('YmdHis'),
                    'invoice_id' => $this->input->post('invoice_id') ?: NULL,
                    'invoice_term_id' => $this->input->post('invoice_term_id') ?: NULL,
                    'client_id' => $this->input->post('client_id') ?: NULL,
                    'category_id' => NULL,
                    'amount' => (float) $this->input->post('amount'),
                    'payment_date' => $this->input->post('payment_date', TRUE),
                    'payment_method' => $this->input->post('payment_method', TRUE),
                    'reference_number' => $this->input->post('reference_number', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'attachment' => '',
                    'created_by' => $this->data['current_user']['id'],
                );

                $this->Payment_model->create_income($payload);
                if (!empty($payload['invoice_id'])) {
                    $this->Invoice_model->refresh_paid_amount((int) $payload['invoice_id']);
                }
                $this->session->set_flashdata('success', 'Pemasukan berhasil dicatat.');
            }
        }

        redirect('finance/income');
    }

    public function delete_income($id)
    {
        $payment = $this->Payment_model->delete_income($id);
        if ($payment && !empty($payment['invoice_id'])) {
            $this->Invoice_model->refresh_paid_amount((int) $payment['invoice_id']);
        }
        $this->session->set_flashdata('success', 'Data pemasukan dihapus.');
        redirect('finance/income');
    }

    public function expenses()
    {
        $this->render('finance/expenses', array(
            'page_title' => 'Uang Keluar',
            'expenses' => $this->Expense_model->all(),
        ));
    }

    public function create_expense()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('vendor_name', 'Vendor', 'required|trim');
            $this->form_validation->set_rules('amount', 'Nominal', 'required|numeric');
            $this->form_validation->set_rules('expense_date', 'Tanggal', 'required');

            if ($this->form_validation->run()) {
                $this->Expense_model->create(array(
                    'expense_code' => 'EXP-' . date('YmdHis'),
                    'category_id' => NULL,
                    'vendor_name' => $this->input->post('vendor_name', TRUE),
                    'amount' => (float) $this->input->post('amount'),
                    'expense_date' => $this->input->post('expense_date', TRUE),
                    'payment_method' => $this->input->post('payment_method', TRUE),
                    'reference_number' => $this->input->post('reference_number', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'attachment' => '',
                    'created_by' => $this->data['current_user']['id'],
                ));
                $this->session->set_flashdata('success', 'Pengeluaran berhasil dicatat.');
            }
        }

        redirect('finance/expenses');
    }

    public function delete_expense($id)
    {
        $this->Expense_model->delete($id);
        $this->session->set_flashdata('success', 'Data pengeluaran dihapus.');
        redirect('finance/expenses');
    }
}
