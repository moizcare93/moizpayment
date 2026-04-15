<?php $items = $invoice['items'] ?? array(array('description' => '', 'qty' => 1, 'unit' => '', 'price' => 0, 'discount_percent' => 0)); ?>
<div class="panel-card">
    <div class="panel-header">
        <h2><?= html_escape($page_title ?? (isset($invoice) ? 'Edit Invoice' : 'Buat Invoice')); ?></h2>
        <a class="btn btn-outline-light" href="<?= site_url('invoices'); ?>">Kembali</a>
    </div>
    <?= form_open(); ?>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Nomor Invoice</label>
                <input type="text" class="form-control" value="<?= html_escape($next_number); ?>" disabled>
            </div>
            <div class="col-md-3">
                <label class="form-label">Klien</label>
                <select name="client_id" class="form-select" required>
                    <option value="">Pilih klien</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id']; ?>" <?= set_select('client_id', $client['id'], ($invoice['client_id'] ?? '') == $client['id']); ?>><?= html_escape($client['name'] . ' - ' . $client['company_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quotation</label>
                <select name="quotation_id" class="form-select">
                    <option value="">Tanpa quotation</option>
                    <?php foreach ($quotations as $quotation): ?>
                        <option value="<?= $quotation['id']; ?>" <?= set_select('quotation_id', $quotation['id'], ($invoice['quotation_id'] ?? '') == $quotation['id']); ?>><?= html_escape($quotation['quotation_number']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" name="invoice_date" class="form-control" value="<?= set_value('invoice_date', $invoice['invoice_date'] ?? date('Y-m-d')); ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jatuh Tempo</label>
                <input type="date" name="due_date" class="form-control" value="<?= set_value('due_date', $invoice['due_date'] ?? date('Y-m-d', strtotime('+14 days'))); ?>" required>
            </div>
        </div>

        <?php $this->load->view('templates/item_table', array('items' => $items)); ?>

        <div class="row g-3 mt-1">
            <div class="col-md-2">
                <label class="form-label">Diskon (%)</label>
                <input type="number" step="0.01" name="discount_percent" class="form-control calc-trigger" value="<?= set_value('discount_percent', $invoice['discount_percent'] ?? 0); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Pajak (%)</label>
                <input type="number" step="0.01" name="tax_percent" class="form-control calc-trigger" value="<?= set_value('tax_percent', $invoice['tax_percent'] ?? 11); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (array('draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled') as $status): ?>
                        <option value="<?= $status; ?>" <?= set_select('status', $status, ($invoice['status'] ?? 'draft') === $status); ?>><?= ucfirst($status); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Terms</label>
                <textarea name="terms" class="form-control" rows="2"><?= set_value('terms', $invoice['terms'] ?? 'Silakan lakukan pembayaran sebelum jatuh tempo.'); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3"><?= set_value('notes', $invoice['notes'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="summary-box mt-4">
            <div>Subtotal: <strong data-role="subtotal">Rp 0</strong></div>
            <div>Grand Total: <strong data-role="grand-total">Rp 0</strong></div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan Invoice</button>
        </div>
    <?= form_close(); ?>
</div>
