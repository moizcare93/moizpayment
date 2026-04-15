<?php $items = $quotation['items'] ?? array(array('description' => '', 'qty' => 1, 'unit' => '', 'price' => 0, 'discount_percent' => 0)); ?>
<div class="panel-card">
    <div class="panel-header">
        <h2><?= html_escape($page_title ?? (isset($quotation) ? 'Edit Penawaran' : 'Buat Penawaran')); ?></h2>
        <a class="btn btn-outline-light" href="<?= site_url('quotations'); ?>">Kembali</a>
    </div>
    <?= form_open(); ?>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nomor Penawaran</label>
                <input type="text" class="form-control" value="<?= html_escape($next_number); ?>" disabled>
            </div>
            <div class="col-md-4">
                <label class="form-label">Klien</label>
                <select name="client_id" class="form-select" required>
                    <option value="">Pilih klien</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id']; ?>" <?= set_select('client_id', $client['id'], ($quotation['client_id'] ?? '') == $client['id']); ?>><?= html_escape($client['name'] . ' - ' . $client['company_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal</label>
                <input type="date" name="quotation_date" class="form-control" value="<?= set_value('quotation_date', $quotation['quotation_date'] ?? date('Y-m-d')); ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Valid Sampai</label>
                <input type="date" name="valid_until" class="form-control" value="<?= set_value('valid_until', $quotation['valid_until'] ?? date('Y-m-d', strtotime('+14 days'))); ?>" required>
            </div>
        </div>

        <?php $this->load->view('templates/item_table', array('items' => $items)); ?>

        <div class="row g-3 mt-1">
            <div class="col-md-2">
                <label class="form-label">Diskon (%)</label>
                <input type="number" step="0.01" name="discount_percent" class="form-control calc-trigger" value="<?= set_value('discount_percent', $quotation['discount_percent'] ?? 0); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Pajak (%)</label>
                <input type="number" step="0.01" name="tax_percent" class="form-control calc-trigger" value="<?= set_value('tax_percent', $quotation['tax_percent'] ?? 11); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (array('draft', 'sent', 'approved', 'rejected', 'expired') as $status): ?>
                        <option value="<?= $status; ?>" <?= set_select('status', $status, ($quotation['status'] ?? 'draft') === $status); ?>><?= ucfirst($status); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Terms</label>
                <textarea name="terms" class="form-control" rows="2"><?= set_value('terms', $quotation['terms'] ?? 'Pembayaran sesuai kesepakatan penawaran.'); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3"><?= set_value('notes', $quotation['notes'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="summary-box mt-4">
            <div>Subtotal: <strong data-role="subtotal">Rp 0</strong></div>
            <div>Grand Total: <strong data-role="grand-total">Rp 0</strong></div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan Penawaran</button>
        </div>
    <?= form_close(); ?>
</div>
