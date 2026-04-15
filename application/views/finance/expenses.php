<div class="row g-4">
    <div class="col-lg-4">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Catat Pengeluaran</h2>
            </div>
            <?= form_open('finance/expenses/create'); ?>
                <div class="mb-3">
                    <label class="form-label">Vendor</label>
                    <input type="text" name="vendor_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nominal</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode</label>
                    <input type="text" name="payment_method" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Referensi</label>
                    <input type="text" name="reference_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            <?= form_close(); ?>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Riwayat Pengeluaran</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Vendor</th>
                            <th>Metode</th>
                            <th>Nominal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $row): ?>
                            <tr>
                                <td><?= html_escape($row['expense_code']); ?></td>
                                <td><?= app_date($row['expense_date']); ?></td>
                                <td><?= html_escape($row['vendor_name']); ?></td>
                                <td><?= html_escape($row['payment_method']); ?></td>
                                <td><?= app_currency($row['amount']); ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-danger" href="<?= site_url('finance/expenses/delete/' . $row['id']); ?>" onclick="return confirm('Hapus data pengeluaran ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
