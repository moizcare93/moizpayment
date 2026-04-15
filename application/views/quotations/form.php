<?php $items = $quotation['items'] ?? array(array('description' => '', 'qty' => 1, 'unit' => '', 'price' => 0, 'discount_percent' => 0)); ?>
<div class="panel-card">
    <div class="panel-header">
        <h2><?= html_escape($page_title ?? (isset($quotation) ? 'Edit Penawaran' : 'Buat Penawaran')); ?></h2>
        <a class="btn btn-outline-light" href="<?= site_url('quotations'); ?>">Kembali</a>
    </div>
    <?= form_open(); ?>
        <div class="invoice-form-shell">
            <section class="form-section">
                <div class="section-head">
                    <div>
                        <div class="section-kicker">Proposal Setup</div>
                        <h3>Informasi Penawaran</h3>
                    </div>
                    <div class="section-note">Siapkan dokumen penawaran resmi yang siap dikirim langsung ke perusahaan tujuan.</div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label class="form-label">Nomor Penawaran</label>
                        <input type="text" class="form-control form-control-strong" value="<?= html_escape($next_number); ?>" disabled>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Klien / Perusahaan Tujuan</label>
                        <select name="client_id" class="form-select form-control-strong" required>
                            <option value="">Pilih klien</option>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?= $client['id']; ?>" <?= set_select('client_id', $client['id'], ($quotation['client_id'] ?? '') == $client['id']); ?>><?= html_escape($client['name'] . ' - ' . $client['company_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">Tanggal Penawaran</label>
                        <input type="date" name="quotation_date" class="form-control form-control-strong" value="<?= set_value('quotation_date', $quotation['quotation_date'] ?? date('Y-m-d')); ?>" required>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label">Berlaku Sampai</label>
                        <input type="date" name="valid_until" class="form-control form-control-strong" value="<?= set_value('valid_until', $quotation['valid_until'] ?? date('Y-m-d', strtotime('+14 days'))); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Diskon (%)</label>
                        <input type="number" step="0.01" name="discount_percent" class="form-control calc-trigger" value="<?= set_value('discount_percent', $quotation['discount_percent'] ?? 0); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Pajak (%)</label>
                        <input type="number" step="0.01" name="tax_percent" class="form-control calc-trigger" value="<?= set_value('tax_percent', $quotation['tax_percent'] ?? 11); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (array('draft', 'sent', 'approved', 'rejected', 'expired') as $status): ?>
                                <option value="<?= $status; ?>" <?= set_select('status', $status, ($quotation['status'] ?? 'draft') === $status); ?>><?= ucfirst($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </section>
        </div>

        <?php $this->load->view('templates/item_table', array('items' => $items)); ?>

        <section class="form-section mt-4">
            <div class="section-head">
                <div>
                    <div class="section-kicker">Commercial Notes</div>
                    <h3>Catatan & Ketentuan Penawaran</h3>
                </div>
                <div class="section-note">Isi ruang lingkup singkat, catatan penting, dan syarat komersial agar dokumen penawaran siap cetak.</div>
            </div>
            <div class="row g-3">
                <div class="col-lg-5">
                    <label class="form-label">Terms & Ketentuan</label>
                    <textarea name="terms" class="form-control" rows="5"><?= set_value('terms', $quotation['terms'] ?? 'Pembayaran dan pelaksanaan pekerjaan mengikuti kesepakatan final setelah penawaran disetujui.'); ?></textarea>
                </div>
                <div class="col-lg-7">
                    <label class="form-label">Catatan Penawaran</label>
                    <textarea name="notes" class="form-control" rows="5"><?= set_value('notes', $quotation['notes'] ?? 'Dengan hormat, berikut kami sampaikan penawaran resmi sesuai kebutuhan yang telah dibahas.'); ?></textarea>
                </div>
            </div>
        </section>

        <div class="invoice-submit-bar mt-4">
            <div class="summary-box">
                <div>Subtotal: <strong data-role="subtotal">Rp 0</strong></div>
                <div>Grand Total: <strong data-role="grand-total">Rp 0</strong></div>
            </div>
            <div class="submit-actions">
                <button type="submit" class="btn btn-primary btn-lg">Simpan Penawaran</button>
            </div>
        </div>
    <?= form_close(); ?>
</div>
