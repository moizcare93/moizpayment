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
            ->select('p.*, t.term_label')
            ->order_by('payment_date', 'DESC')
            ->join('mp_invoice_terms t', 't.id = p.invoice_term_id', 'left')
            ->get_where('mp_payments p', array('p.invoice_id' => $id))
            ->result_array();

        $invoice['terms'] = $this->get_terms($id);
        $invoice['balance_due'] = (float) $invoice['total'] - (float) $invoice['paid_amount'];

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

    public function save($header, $items, $terms, $id = NULL)
    {
        $this->db->trans_start();

        if ($id) {
            $this->db->where('id', $id)->update('mp_invoices', $header);
            $this->db->delete('mp_invoice_items', array('invoice_id' => $id));
            $this->db->delete('mp_invoice_terms', array('invoice_id' => $id));
        } else {
            $this->db->insert('mp_invoices', $header);
            $id = $this->db->insert_id();
        }

        foreach ($items as $item) {
            $item['invoice_id'] = $id;
            $this->db->insert('mp_invoice_items', $item);
        }

        $order = 1;
        foreach ($terms as $term) {
            $term['invoice_id'] = $id;
            $term['sort_order'] = $order++;
            $this->db->insert('mp_invoice_terms', $term);
        }

        $this->db->trans_complete();
        $this->refresh_paid_amount($id);
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
        $this->db->delete('mp_invoice_terms', array('invoice_id' => $id));
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

        $status = $this->derive_invoice_status($invoice, $paid_amount);

        $this->db->where('id', $invoice_id)->update('mp_invoices', array(
            'paid_amount' => $paid_amount,
            'status' => $status,
        ));

        $this->refresh_term_statuses($invoice_id);
    }

    private function normalize_status($invoice)
    {
        if (!empty($invoice['status']) && $invoice['status'] === 'cancelled') {
            return 'cancelled';
        }

        if (!empty($invoice['status']) && $invoice['status'] === 'draft') {
            return 'draft';
        }

        return $this->derive_invoice_status($invoice, (float) $invoice['paid_amount']);
    }

    public function normalize_terms($terms, $invoice_total, $fallback_due_date)
    {
        $normalized = array();
        foreach ((array) $terms as $index => $term) {
            if (empty(trim($term['term_label'] ?? '')) && empty($term['amount'])) {
                continue;
            }

            $normalized[] = array(
                'term_label' => trim($term['term_label'] ?? ('Termin ' . ($index + 1))),
                'amount' => (float) ($term['amount'] ?? 0),
                'due_date' => !empty($term['due_date']) ? $term['due_date'] : $fallback_due_date,
                'status' => 'pending',
                'notes' => trim($term['notes'] ?? ''),
            );
        }

        if (empty($normalized)) {
            $normalized[] = array(
                'term_label' => 'Pelunasan',
                'amount' => (float) $invoice_total,
                'due_date' => $fallback_due_date,
                'status' => 'pending',
                'notes' => '',
            );
        }

        return $normalized;
    }

    public function validate_terms($terms, $invoice_total)
    {
        if (empty($terms)) {
            return 'Minimal satu termin pembayaran harus diisi.';
        }

        $sum = 0;
        foreach ($terms as $term) {
            if ($term['amount'] <= 0) {
                return 'Nominal setiap termin harus lebih besar dari 0.';
            }
            if (empty($term['due_date'])) {
                return 'Setiap termin harus memiliki tanggal jatuh tempo.';
            }
            if (empty($term['term_label'])) {
                return 'Setiap termin harus memiliki label penagihan.';
            }
            $sum += (float) $term['amount'];
        }

        if (abs($sum - (float) $invoice_total) > 0.01) {
            return 'Total seluruh termin harus sama dengan grand total invoice.';
        }

        return NULL;
    }

    public function get_terms($invoice_id)
    {
        $terms = $this->db
            ->order_by('sort_order', 'ASC')
            ->get_where('mp_invoice_terms', array('invoice_id' => $invoice_id))
            ->result_array();

        foreach ($terms as &$term) {
            $sum = $this->db
                ->select_sum('amount')
                ->where('invoice_term_id', $term['id'])
                ->get('mp_payments')
                ->row_array();
            $term['paid_amount'] = (float) ($sum['amount'] ?? 0);
            $term['remaining_amount'] = (float) $term['amount'] - $term['paid_amount'];
            $term['status'] = $this->derive_term_status($term);
        }

        return $terms;
    }

    private function refresh_term_statuses($invoice_id)
    {
        $terms = $this->get_terms($invoice_id);
        foreach ($terms as $term) {
            $this->db->where('id', $term['id'])->update('mp_invoice_terms', array(
                'status' => $term['status'],
            ));
        }
    }

    private function derive_term_status($term)
    {
        $paid = (float) ($term['paid_amount'] ?? 0);
        $amount = (float) ($term['amount'] ?? 0);
        $due_date = $term['due_date'] ?? NULL;

        if ($paid >= $amount && $amount > 0) {
            return 'paid';
        }

        if ($paid > 0) {
            return 'partial';
        }

        if (!empty($due_date) && strtotime($due_date) < strtotime(date('Y-m-d'))) {
            return 'overdue';
        }

        return 'pending';
    }

    private function derive_invoice_status($invoice, $paid_amount)
    {
        if ($paid_amount >= (float) $invoice['total'] && (float) $invoice['total'] > 0) {
            return 'paid';
        }

        if ($paid_amount > 0) {
            return 'partial';
        }

        $terms = $this->get_terms($invoice['id']);
        foreach ($terms as $term) {
            if ($term['status'] === 'overdue') {
                return 'overdue';
            }
        }

        if (!empty($invoice['due_date']) && strtotime($invoice['due_date']) < strtotime(date('Y-m-d'))) {
            return 'overdue';
        }

        return 'sent';
    }
}
