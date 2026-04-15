<div class="panel-card">
    <div class="panel-header">
        <h2>Data Klien</h2>
        <a class="btn btn-primary" href="<?= site_url('clients/create'); ?>">Tambah Klien</a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Perusahaan</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Kota</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $row): ?>
                    <tr>
                        <td><?= html_escape($row['name']); ?></td>
                        <td><?= html_escape($row['company_name']); ?></td>
                        <td><?= html_escape($row['email']); ?></td>
                        <td><?= html_escape($row['phone']); ?></td>
                        <td><?= html_escape($row['city']); ?></td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-light" href="<?= site_url('clients/edit/' . $row['id']); ?>">Edit</a>
                            <a class="btn btn-sm btn-outline-danger" href="<?= site_url('clients/delete/' . $row['id']); ?>" onclick="return confirm('Hapus klien ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
