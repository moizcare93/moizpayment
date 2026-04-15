<div class="panel-card">
    <div class="panel-header">
        <h2>Daftar Penawaran</h2>
        <a class="btn btn-primary" href="<?= site_url('quotations/create'); ?>">Buat Penawaran</a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Klien</th>
                    <th>Tanggal</th>
                    <th>Berlaku</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotations as $row): ?>
                    <tr>
                        <td><?= html_escape($row['quotation_number']); ?></td>
                        <td><?= html_escape($row['client_name']); ?></td>
                        <td><?= app_date($row['quotation_date']); ?></td>
                        <td><?= app_date($row['valid_until']); ?></td>
                        <td><?= app_currency($row['total']); ?></td>
                        <td><span class="badge text-bg-<?= invoice_status_badge($row['status']); ?>"><?= ucfirst($row['status']); ?></span></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-light" href="<?= site_url('quotations/view/' . $row['id']); ?>">Lihat</a>
                            <a class="btn btn-sm btn-outline-light" href="<?= site_url('quotations/edit/' . $row['id']); ?>">Edit</a>
                            <a class="btn btn-sm btn-outline-danger" href="<?= site_url('quotations/delete/' . $row['id']); ?>" onclick="return confirm('Hapus penawaran ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
