<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoices extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Invoice_model', 'Client_model', 'Quotation_model'));
    }

    public function index()
    {
        $this->render('invoices/index', array(
            'page_title' => 'Invoice',
            'invoices' => $this->Invoice_model->all(),
        ));
    }

    public function create()
    {
        $this->save();
    }

    public function edit($id)
    {
        $this->save($id);
    }

    public function view($id)
    {
        $invoice = $this->Invoice_model->find($id);
        if (!$invoice) {
            show_404();
        }

        $this->render('invoices/view', array(
            'page_title' => 'Detail Invoice',
            'invoice' => $invoice,
        ));
    }

    public function printable($id)
    {
        $invoice = $this->Invoice_model->find($id);
        if (!$invoice) {
            show_404();
        }

        $client = $this->Client_model->find($invoice['client_id']);
        $this->load->view('invoices/print', array(
            'invoice' => $invoice,
            'client' => $client,
            'app_settings' => $this->data['app_settings'],
        ));
    }

    private function save($id = NULL)
    {
        $invoice = $id ? $this->Invoice_model->find($id) : NULL;
        $term_error = NULL;
        if ($id && !$invoice) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('client_id', 'Klien', 'required|integer');
            $this->form_validation->set_rules('invoice_date', 'Tanggal Invoice', 'required');
            $this->form_validation->set_rules('due_date', 'Jatuh Tempo', 'required');

            if ($this->form_validation->run()) {
                list($header, $items, $terms) = $this->build_payload($invoice['invoice_number'] ?? NULL);
                $term_error = $this->Invoice_model->validate_terms($terms, $header['total']);
                if ($term_error === NULL) {
                    $invoice_id = $this->Invoice_model->save($header, $items, $terms, $id);
                    $this->session->set_flashdata('success', 'Invoice berhasil disimpan.');
                    redirect('invoices/view/' . $invoice_id);
                }
            }
        }

        $this->render('invoices/form', array(
            'page_title' => $id ? 'Edit Invoice' : 'Buat Invoice',
            'invoice' => $invoice,
            'term_error' => $term_error,
            'clients' => $this->Client_model->all(),
            'quotations' => $this->Quotation_model->all(),
            'next_number' => $invoice['invoice_number'] ?? $this->Invoice_model->next_number(setting_value($this->data['app_settings'], 'invoice_prefix', 'INV/')),
        ));
    }

    public function add_payment($id)
    {
        $invoice = $this->Invoice_model->find($id);
        if (!$invoice) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('amount', 'Nominal', 'required|numeric');
            $this->form_validation->set_rules('payment_date', 'Tanggal Pembayaran', 'required');

            if ($this->form_validation->run()) {
                $this->Invoice_model->add_payment(array(
                    'payment_code' => 'PAY-' . date('YmdHis'),
                    'invoice_id' => $id,
                    'invoice_term_id' => $this->input->post('invoice_term_id') ?: NULL,
                    'client_id' => $invoice['client_id'],
                    'category_id' => NULL,
                    'amount' => (float) $this->input->post('amount'),
                    'payment_date' => $this->input->post('payment_date', TRUE),
                    'payment_method' => $this->input->post('payment_method', TRUE),
                    'reference_number' => $this->input->post('reference_number', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'attachment' => '',
                    'created_by' => $this->data['current_user']['id'],
                ));
                $this->session->set_flashdata('success', 'Pembayaran berhasil ditambahkan.');
            }
        }

        redirect('invoices/view/' . $id);
    }

    public function delete($id)
    {
        $this->Invoice_model->delete($id);
        $this->session->set_flashdata('success', 'Invoice dihapus.');
        redirect('invoices');
    }

    private function build_payload($existing_number = NULL)
    {
        $subtotal = 0;
        $items = array();
        foreach ((array) $this->input->post('items') as $item) {
            if (empty(trim($item['description'] ?? ''))) {
                continue;
            }

            $qty = (float) ($item['qty'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            $discount_percent = (float) ($item['discount_percent'] ?? 0);
            $line_total = ($qty * $price) - (($qty * $price) * $discount_percent / 100);
            $subtotal += $line_total;
            $items[] = array(
                'description' => $item['description'],
                'qty' => $qty,
                'unit' => $item['unit'] ?? '',
                'price' => $price,
                'discount_percent' => $discount_percent,
                'total' => $line_total,
            );
        }

        $discount_percent = (float) $this->input->post('discount_percent');
        $discount_amount = $subtotal * $discount_percent / 100;
        $tax_percent = (float) $this->input->post('tax_percent');
        $tax_base = $subtotal - $discount_amount;
        $tax_amount = $tax_base * $tax_percent / 100;
        $total = $tax_base + $tax_amount;

        $terms = $this->Invoice_model->normalize_terms(
            $this->input->post('terms_schedule'),
            $total,
            $this->input->post('due_date', TRUE)
        );

        return array(
            array(
                'invoice_number' => $existing_number ?: $this->Invoice_model->next_number(setting_value($this->data['app_settings'], 'invoice_prefix', 'INV/')),
                'client_id' => (int) $this->input->post('client_id'),
                'quotation_id' => $this->input->post('quotation_id') ?: NULL,
                'invoice_date' => $this->input->post('invoice_date', TRUE),
                'due_date' => $this->input->post('due_date', TRUE),
                'subtotal' => $subtotal,
                'discount_percent' => $discount_percent,
                'discount_amount' => $discount_amount,
                'tax_percent' => $tax_percent,
                'tax_amount' => $tax_amount,
                'total' => $total,
                'status' => $this->input->post('status', TRUE) ?: 'draft',
                'notes' => $this->input->post('notes', TRUE),
                'terms' => $this->input->post('terms', TRUE),
                'created_by' => $this->data['current_user']['id'],
            ),
            $items,
            $terms,
        );
    }
}
