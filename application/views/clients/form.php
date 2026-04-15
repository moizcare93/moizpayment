<div class="panel-card">
    <div class="panel-header">
        <h2><?= html_escape($page_title ?? (isset($client) ? 'Edit Klien' : 'Tambah Klien')); ?></h2>
        <a class="btn btn-outline-light" href="<?= site_url('clients'); ?>">Kembali</a>
    </div>
    <?= form_open(); ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Klien</label>
                <input type="text" name="name" class="form-control" value="<?= set_value('name', $client['name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Perusahaan</label>
                <input type="text" name="company_name" class="form-control" value="<?= set_value('company_name', $client['company_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= set_value('email', $client['email'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Telepon</label>
                <input type="text" name="phone" class="form-control" value="<?= set_value('phone', $client['phone'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Kota</label>
                <input type="text" name="city" class="form-control" value="<?= set_value('city', $client['city'] ?? ''); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="3"><?= set_value('address', $client['address'] ?? ''); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3"><?= set_value('notes', $client['notes'] ?? ''); ?></textarea>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    <?= form_close(); ?>
</div>
