<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotations extends Authenticated_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Quotation_model', 'Client_model', 'Invoice_model'));
    }

    public function index()
    {
        $this->render('quotations/index', array(
            'page_title' => 'Penawaran',
            'quotations' => $this->Quotation_model->all(),
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
        $quotation = $this->Quotation_model->find($id);
        if (!$quotation) {
            show_404();
        }

        $this->render('quotations/view', array(
            'page_title' => 'Detail Penawaran',
            'quotation' => $quotation,
        ));
    }

    private function save($id = NULL)
    {
        $quotation = $id ? $this->Quotation_model->find($id) : NULL;
        if ($id && !$quotation) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('client_id', 'Klien', 'required|integer');
            $this->form_validation->set_rules('quotation_date', 'Tanggal Penawaran', 'required');
            $this->form_validation->set_rules('valid_until', 'Berlaku Sampai', 'required');

            if ($this->form_validation->run()) {
                list($header, $items) = $this->build_document_payload($id ? $quotation['quotation_number'] : NULL);
                $quotation_id = $this->Quotation_model->save($header, $items, $id);
                $this->session->set_flashdata('success', 'Penawaran berhasil disimpan.');
                redirect('quotations/view/' . $quotation_id);
            }
        }

        $this->render('quotations/form', array(
            'page_title' => $id ? 'Edit Penawaran' : 'Buat Penawaran',
            'quotation' => $quotation,
            'clients' => $this->Client_model->all(),
            'next_number' => $quotation['quotation_number'] ?? $this->Quotation_model->next_number(setting_value($this->data['app_settings'], 'quotation_prefix', 'QUO/')),
        ));
    }

    public function convert_to_invoice($id)
    {
        $quotation = $this->Quotation_model->find($id);
        if (!$quotation) {
            show_404();
        }

        $invoice_header = array(
            'invoice_number' => $this->Invoice_model->next_number(setting_value($this->data['app_settings'], 'invoice_prefix', 'INV/')),
            'client_id' => $quotation['client_id'],
            'quotation_id' => $quotation['id'],
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+' . ((int) setting_value($this->data['app_settings'], 'default_payment_terms', 14)) . ' days')),
            'subtotal' => $quotation['subtotal'],
            'discount_percent' => $quotation['discount_percent'],
            'discount_amount' => $quotation['discount_amount'],
            'tax_percent' => $quotation['tax_percent'],
            'tax_amount' => $quotation['tax_amount'],
            'total' => $quotation['total'],
            'paid_amount' => 0,
            'status' => 'draft',
            'notes' => $quotation['notes'],
            'terms' => $quotation['terms'],
            'created_by' => $this->data['current_user']['id'],
        );

        $items = array();
        foreach ($quotation['items'] as $item) {
            $items[] = array(
                'description' => $item['description'],
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'discount_percent' => $item['discount_percent'],
                'total' => $item['total'],
            );
        }

        $invoice_id = $this->Invoice_model->save($invoice_header, $items);
        $this->db->where('id', $id)->update('mp_quotations', array('status' => 'approved'));
        $this->session->set_flashdata('success', 'Penawaran berhasil dikonversi menjadi invoice.');
        redirect('invoices/view/' . $invoice_id);
    }

    public function delete($id)
    {
        $this->Quotation_model->delete($id);
        $this->session->set_flashdata('success', 'Penawaran dihapus.');
        redirect('quotations');
    }

    private function build_document_payload($existing_number = NULL)
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

        return array(
            array(
                'quotation_number' => $existing_number ?: $this->Quotation_model->next_number(setting_value($this->data['app_settings'], 'quotation_prefix', 'QUO/')),
                'client_id' => (int) $this->input->post('client_id'),
                'quotation_date' => $this->input->post('quotation_date', TRUE),
                'valid_until' => $this->input->post('valid_until', TRUE),
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
        );
    }
}
