<div class="panel-card">
    <div class="panel-header">
        <h2>Profil Perusahaan & Integrasi</h2>
    </div>
    <?= form_open_multipart('settings'); ?>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Perusahaan</label>
                <input type="text" name="company_name" class="form-control" value="<?= set_value('company_name', $app_settings['company_name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Tagline</label>
                <input type="text" name="company_tagline" class="form-control" value="<?= set_value('company_tagline', $app_settings['company_tagline'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="company_email" class="form-control" value="<?= set_value('company_email', $app_settings['company_email'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Telepon</label>
                <input type="text" name="company_phone" class="form-control" value="<?= set_value('company_phone', $app_settings['company_phone'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Website</label>
                <input type="text" name="company_website" class="form-control" value="<?= set_value('company_website', $app_settings['company_website'] ?? ''); ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Alamat</label>
                <textarea name="company_address" class="form-control" rows="3"><?= set_value('company_address', $app_settings['company_address'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Kota</label>
                <input type="text" name="company_city" class="form-control" value="<?= set_value('company_city', $app_settings['company_city'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Provinsi</label>
                <input type="text" name="company_province" class="form-control" value="<?= set_value('company_province', $app_settings['company_province'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kode Pos</label>
                <input type="text" name="company_postal_code" class="form-control" value="<?= set_value('company_postal_code', $app_settings['company_postal_code'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">NPWP</label>
                <input type="text" name="company_npwp" class="form-control" value="<?= set_value('company_npwp', $app_settings['company_npwp'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Prefix Invoice</label>
                <input type="text" name="invoice_prefix" class="form-control" value="<?= set_value('invoice_prefix', $app_settings['invoice_prefix'] ?? 'INV/'); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Prefix Penawaran</label>
                <input type="text" name="quotation_prefix" class="form-control" value="<?= set_value('quotation_prefix', $app_settings['quotation_prefix'] ?? 'QUO/'); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Termin Default (hari)</label>
                <input type="number" name="default_payment_terms" class="form-control" value="<?= set_value('default_payment_terms', $app_settings['default_payment_terms'] ?? 14); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Logo</label>
                <input type="file" name="company_logo" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Bank</label>
                <input type="text" name="bank_name" class="form-control" value="<?= set_value('bank_name', $app_settings['bank_name'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Nomor Rekening</label>
                <input type="text" name="bank_account_number" class="form-control" value="<?= set_value('bank_account_number', $app_settings['bank_account_number'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Atas Nama</label>
                <input type="text" name="bank_account_name" class="form-control" value="<?= set_value('bank_account_name', $app_settings['bank_account_name'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama Penagih / Penandatangan</label>
                <input type="text" name="collector_name" class="form-control" value="<?= set_value('collector_name', $app_settings['collector_name'] ?? ''); ?>" placeholder="Mis. Muhammad Iqbal">
            </div>
            <div class="col-md-6">
                <label class="form-label">Jabatan Penagih</label>
                <input type="text" name="collector_title" class="form-control" value="<?= set_value('collector_title', $app_settings['collector_title'] ?? ''); ?>" placeholder="Mis. Finance & Billing Officer">
            </div>
            <div class="col-md-3">
                <label class="form-label">SMTP Host</label>
                <input type="text" name="smtp_host" class="form-control" value="<?= set_value('smtp_host', $app_settings['smtp_host'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">SMTP Port</label>
                <input type="number" name="smtp_port" class="form-control" value="<?= set_value('smtp_port', $app_settings['smtp_port'] ?? 587); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">SMTP Username</label>
                <input type="text" name="smtp_username" class="form-control" value="<?= set_value('smtp_username', $app_settings['smtp_username'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">SMTP Encryption</label>
                <input type="text" name="smtp_encryption" class="form-control" value="<?= set_value('smtp_encryption', $app_settings['smtp_encryption'] ?? 'tls'); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">SMTP Password</label>
                <input type="text" name="smtp_password" class="form-control" value="<?= set_value('smtp_password', $app_settings['smtp_password'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">WA API URL</label>
                <input type="text" name="wa_api_url" class="form-control" value="<?= set_value('wa_api_url', $app_settings['wa_api_url'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">WA API Key</label>
                <input type="text" name="wa_api_key" class="form-control" value="<?= set_value('wa_api_key', $app_settings['wa_api_key'] ?? ''); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">WA Sender Number</label>
                <input type="text" name="wa_sender_number" class="form-control" value="<?= set_value('wa_sender_number', $app_settings['wa_sender_number'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Simbol Mata Uang</label>
                <input type="text" name="currency_symbol" class="form-control" value="<?= set_value('currency_symbol', $app_settings['currency_symbol'] ?? 'Rp'); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Kode Mata Uang</label>
                <input type="text" name="currency_code" class="form-control" value="<?= set_value('currency_code', $app_settings['currency_code'] ?? 'IDR'); ?>">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
        </div>
    <?= form_close(); ?>
</div>
