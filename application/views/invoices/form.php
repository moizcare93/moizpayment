<?php
$items = $invoice['items'] ?? array(array('description' => '', 'qty' => 1, 'unit' => '', 'price' => 0, 'discount_percent' => 0));
$posted_terms = $this->input->post('terms_schedule');
$terms = !empty($posted_terms)
    ? $posted_terms
    : ($invoice['terms'] ?? array(array(
        'term_label' => 'Pelunasan',
        'due_date' => $invoice['due_date'] ?? date('Y-m-d', strtotime('+14 days')),
        'percent' => '',
        'amount' => $invoice['total'] ?? 0,
        'notes' => '',
    )));
?>
<div class="panel-card">
    <div class="panel-header">
        <h2><?= html_escape($page_title ?? (isset($invoice) ? 'Edit Invoice' : 'Buat Invoice')); ?></h2>
        <a class="btn btn-outline-light" href="<?= site_url('invoices'); ?>">Kembali</a>
    </div>
    <?php if (!empty($term_error)): ?>
        <div class="alert alert-danger mt-3"><?= html_escape($term_error); ?></div>
    <?php endif; ?>
    <?= form_open(); ?>
        <div class="invoice-form-shell">
            <section class="form-section">
                <div class="section-head">
                    <div>
                        <div class="section-kicker">Document Setup</div>
                        <h3>Informasi Invoice</h3>
                    </div>
                    <div class="section-note">Tentukan nomor dokumen, klien, referensi quotation, dan tanggal penagihan.</div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label">Nomor Invoice</label>
                        <input type="text" class="form-control form-control-strong" value="<?= html_escape($next_number); ?>" disabled>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Klien</label>
                        <select name="client_id" class="form-select form-control-strong" required>
                            <option value="">Pilih klien</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client['id']; ?>" <?= set_select('client_id', $client['id'], ($invoice['client_id'] ?? '') == $client['id']); ?>><?= html_escape($client['name'] . ' - ' . $client['company_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Quotation Referensi</label>
                        <select name="quotation_id" class="form-select form-control-strong">
                            <option value="">Tanpa quotation</option>
                            <?php foreach ($quotations as $quotation): ?>
                                <option value="<?= $quotation['id']; ?>" <?= set_select('quotation_id', $quotation['id'], ($invoice['quotation_id'] ?? '') == $quotation['id']); ?>><?= html_escape($quotation['quotation_number']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Tanggal Invoice</label>
                        <input type="date" name="invoice_date" class="form-control form-control-strong" value="<?= set_value('invoice_date', $invoice['invoice_date'] ?? date('Y-m-d')); ?>" required>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Jatuh Tempo</label>
                        <input type="date" name="due_date" class="form-control form-control-strong" value="<?= set_value('due_date', $invoice['due_date'] ?? date('Y-m-d', strtotime('+14 days'))); ?>" required>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">Diskon (%)</label>
                        <input type="number" step="0.01" name="discount_percent" class="form-control calc-trigger" value="<?= set_value('discount_percent', $invoice['discount_percent'] ?? 0); ?>">
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">Pajak (%)</label>
                        <input type="number" step="0.01" name="tax_percent" class="form-control calc-trigger" value="<?= set_value('tax_percent', $invoice['tax_percent'] ?? 11); ?>">
                    </div>
                    <div class="col-md-12 col-lg-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (array('draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled') as $status): ?>
                                <option value="<?= $status; ?>" <?= set_select('status', $status, ($invoice['status'] ?? 'draft') === $status); ?>><?= ucfirst($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </section>
        </div>

        <?php $this->load->view('templates/item_table', array('items' => $items)); ?>
        <?php $this->load->view('templates/term_table', array('terms' => $terms)); ?>

        <section class="form-section mt-4">
            <div class="section-head">
                <div>
                    <div class="section-kicker">Narrative</div>
                    <h3>Catatan Penagihan</h3>
                </div>
                <div class="section-note">Isi syarat pembayaran dan catatan tambahan yang akan muncul pada invoice.</div>
            </div>
            <div class="row g-3">
                <div class="col-lg-5">
                    <label class="form-label">Terms Pembayaran</label>
                    <textarea name="terms" class="form-control" rows="5"><?= set_value('terms', $invoice['terms'] ?? 'Silakan lakukan pembayaran sebelum jatuh tempo.'); ?></textarea>
                </div>
                <div class="col-lg-7">
                    <label class="form-label">Catatan Internal / Klien</label>
                    <textarea name="notes" class="form-control" rows="5"><?= set_value('notes', $invoice['notes'] ?? ''); ?></textarea>
                </div>
            </div>
        </section>

        <div class="invoice-submit-bar mt-4">
            <div class="summary-box">
                <div>Subtotal: <strong data-role="subtotal">Rp 0</strong></div>
                <div>Grand Total: <strong data-role="grand-total">Rp 0</strong></div>
            </div>
            <div class="submit-actions">
                <button type="submit" class="btn btn-primary btn-lg">Simpan Invoice</button>
            </div>
        </div>
    <?= form_close(); ?>
</div>
