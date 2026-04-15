<div class="row g-4">
    <div class="col-lg-8">
        <div class="panel-card">
            <div class="panel-header">
                <h2><?= html_escape($invoice['invoice_number']); ?></h2>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-light" href="<?= site_url('invoices/edit/' . $invoice['id']); ?>">Edit</a>
                    <a class="btn btn-outline-light" href="<?= site_url('invoices/print/' . $invoice['id']); ?>" target="_blank">Print</a>
                </div>
            </div>
            <div class="detail-block mb-4">
                <div>Status: <span class="badge text-bg-<?= invoice_status_badge($invoice['status']); ?>"><?= ucfirst($invoice['status']); ?></span></div>
                <div>Tanggal: <?= app_date($invoice['invoice_date']); ?></div>
                <div>Jatuh tempo: <?= app_date($invoice['due_date']); ?></div>
                <div>Total: <?= app_currency($invoice['total']); ?></div>
                <div>Terbayar: <?= app_currency($invoice['paid_amount']); ?></div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Deskripsi</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Harga</th>
                            <th>Disc%</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoice['items'] as $item): ?>
                            <tr>
                                <td><?= html_escape($item['description']); ?></td>
                                <td><?= $item['qty']; ?></td>
                                <td><?= html_escape($item['unit']); ?></td>
                                <td><?= app_currency($item['price']); ?></td>
                                <td><?= $item['discount_percent']; ?></td>
                                <td><?= app_currency($item['total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Input Pembayaran</h2>
            </div>
            <?= form_open('invoices/payment/' . $invoice['id']); ?>
                <div class="mb-3">
                    <label class="form-label">Nominal</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode</label>
                    <input type="text" name="payment_method" class="form-control" placeholder="Transfer / Tunai / QRIS">
                </div>
                <div class="mb-3">
                    <label class="form-label">Referensi</label>
                    <input type="text" name="reference_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan Pembayaran</button>
            <?= form_close(); ?>
        </div>

        <div class="panel-card mt-4">
            <div class="panel-header">
                <h2>Riwayat Pembayaran</h2>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($invoice['payments'] as $payment): ?>
                    <div class="list-group-item bg-transparent text-white border-secondary">
                        <strong><?= app_currency($payment['amount']); ?></strong>
                        <div><?= app_date($payment['payment_date']); ?> · <?= html_escape($payment['payment_method']); ?></div>
                        <small><?= html_escape($payment['reference_number']); ?></small>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($invoice['payments'])): ?>
                    <div class="text-secondary">Belum ada pembayaran.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
