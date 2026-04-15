<?php
$company_name = setting_value($app_settings, 'company_name', 'MoizPayment');
$company_logo = setting_value($app_settings, 'company_logo');
$company_address = trim(
    setting_value($app_settings, 'company_address', '') . "\n" .
    setting_value($app_settings, 'company_city', '') . ' ' .
    setting_value($app_settings, 'company_province', '') . ' ' .
    setting_value($app_settings, 'company_postal_code', '')
);
$collector_name = setting_value($app_settings, 'collector_name', $company_name);
$collector_title = setting_value($app_settings, 'collector_title', 'Sales & Business Development');
$client_company = !empty($client['company_name']) ? $client['company_name'] : ($client['name'] ?? '-');
$verification_payload = "Quotation Verification\n"
    . "Penerbit: {$company_name}\n"
    . "Klien: {$client_company}\n"
    . "Nomor Penawaran: {$quotation['quotation_number']}\n"
    . "Tanggal Penawaran: " . app_date($quotation['quotation_date']) . "\n"
    . "Berlaku Sampai: " . app_date($quotation['valid_until']) . "\n"
    . "Penanggung Jawab: {$collector_name} ({$collector_title})";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape($quotation['quotation_number']); ?> | Print Penawaran</title>
    <style>
        :root {
            --ink: #132238;
            --muted: #66758c;
            --line: #dbe3ee;
            --line-strong: #bcc9d8;
            --accent: #1d4ed8;
            --accent-soft: #eef4ff;
            --paper: #fff;
            --bg: #edf2f8;
            --warm: #f7f3ea;
            --warm-line: #e7dcc3;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            background: var(--bg);
            color: var(--ink);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .print-shell {
            max-width: 820px;
            margin: 12px auto;
            padding: 0 8px;
        }
        .print-toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .print-toolbar button {
            border: 0;
            border-radius: 999px;
            padding: 8px 16px;
            background: var(--accent);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }
        .paper {
            background: var(--paper);
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(19,34,56,.08);
            overflow: hidden;
        }
        .topbar {
            height: 7px;
            background: linear-gradient(90deg, #1d4ed8, #12b886);
        }
        .body {
            padding: 18px 20px 20px;
        }
        .hero {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }
        .identity {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .logo-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbe6ff;
            overflow: hidden;
        }
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .logo-fallback {
            font-size: 18px;
            font-weight: 700;
            color: var(--accent);
        }
        .eyebrow {
            color: var(--muted);
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .14em;
            margin-bottom: 4px;
            font-weight: 700;
        }
        .company-name, .doc-title {
            margin: 0;
            font-size: 18px;
            line-height: 1;
            letter-spacing: -.03em;
        }
        .meta {
            margin-top: 6px;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.4;
            white-space: pre-line;
        }
        .doc-card {
            min-width: 220px;
            padding: 12px 14px;
            background: linear-gradient(180deg, #f8fbff, #eef4ff);
            border: 1px solid #d8e4ff;
            border-radius: 12px;
        }
        .doc-number {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        .status-pill {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            background: #e7f0ff;
            color: #1d4ed8;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 12px;
        }
        .info-card, .notes-card, .totals-card, .auth-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
        }
        .info-card h3, .notes-card h3, .totals-card h3, .auth-card h3 {
            margin: 0 0 8px;
            font-size: 9px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .client-name {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-wrap {
            margin-top: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
        }
        .items-wrap thead th {
            background: #f6f9fd;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #4b5870;
            text-align: left;
        }
        .items-wrap tbody td {
            padding: 7px 10px;
            border-top: 1px solid #eef2f7;
            font-size: 10px;
            line-height: 1.28;
            vertical-align: top;
        }
        .num { text-align: right; white-space: nowrap; }
        .desc-title {
            font-weight: 700;
            margin-bottom: 1px;
        }
        .totals-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 12px;
            margin-top: 12px;
            align-items: start;
        }
        .totals-table td {
            padding: 6px 0;
            border-bottom: 1px solid #edf1f6;
            font-size: 10px;
        }
        .totals-table tr:last-child td {
            border-bottom: 0;
            padding-top: 8px;
            font-size: 12px;
            font-weight: 700;
        }
        .totals-table td:last-child {
            text-align: right;
        }
        .auth-card {
            background: var(--warm);
            border-color: var(--warm-line);
        }
        .signatory {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed var(--line-strong);
            display: grid;
            grid-template-columns: 1fr 78px;
            gap: 10px;
            align-items: center;
        }
        .sign-name {
            font-size: 11px;
            font-weight: 700;
        }
        .sign-role {
            color: var(--muted);
            font-size: 9px;
        }
        .qr-box {
            width: 78px;
            height: 78px;
            border: 1px solid var(--line-strong);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            overflow: hidden;
        }
        .qr-box canvas { width: 68px !important; height: 68px !important; }
        .footer-band {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 9px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }
        @page {
            size: A4;
            margin: 6mm;
        }
        @media print {
            body { background: #fff; }
            .print-shell { max-width: none; margin: 0; padding: 0; }
            .print-toolbar { display: none; }
            .paper { box-shadow: none; border-radius: 0; }
            .body { padding: 10px 12px 12px; }
        }
    </style>
</head>
<body>
<div class="print-shell">
    <div class="print-toolbar">
        <button type="button" onclick="window.print()">Print Penawaran</button>
    </div>
    <div class="paper">
        <div class="topbar"></div>
        <div class="body">
            <section class="hero">
                <div class="identity">
                    <div class="logo-box">
                        <?php if (!empty($company_logo)): ?>
                            <img src="<?= base_url($company_logo); ?>" alt="<?= html_escape($company_name); ?>">
                        <?php else: ?>
                            <div class="logo-fallback"><?= html_escape(substr($company_name, 0, 1)); ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="eyebrow">Issued By</div>
                        <h1 class="company-name"><?= html_escape($company_name); ?></h1>
                        <div class="meta"><?= html_escape(trim($company_address)); ?>
<?= html_escape(setting_value($app_settings, 'company_phone', '')); ?><?= setting_value($app_settings, 'company_email', '') ? '  |  ' . html_escape(setting_value($app_settings, 'company_email', '')) : ''; ?></div>
                    </div>
                </div>
                <div class="doc-card">
                    <div class="eyebrow">Business Proposal</div>
                    <h2 class="doc-title">Penawaran Harga</h2>
                    <div class="doc-number"><?= html_escape($quotation['quotation_number']); ?></div>
                    <div class="meta">Tanggal penawaran: <?= app_date($quotation['quotation_date']); ?><br>Berlaku sampai: <?= app_date($quotation['valid_until']); ?></div>
                    <div class="status-pill"><?= strtoupper(html_escape($quotation['status'])); ?></div>
                </div>
            </section>

            <section class="info-grid">
                <div class="info-card">
                    <h3>Ditujukan Kepada</h3>
                    <div class="client-name"><?= html_escape($client_company); ?></div>
                    <div class="meta"><?= html_escape($client['name'] ?? ''); ?><br><?= html_escape($client['address'] ?? ''); ?><br><?= html_escape($client['city'] ?? ''); ?><br><?= html_escape($client['phone'] ?? ''); ?><?= !empty($client['email']) ? '  |  ' . html_escape($client['email']) : ''; ?></div>
                </div>
                <div class="info-card">
                    <h3>Ringkasan Penawaran</h3>
                    <div class="meta">Dokumen ini merupakan penawaran resmi dari <?= html_escape($company_name); ?> untuk kebutuhan <?= html_escape($client_company); ?>. Rincian item dan nilai penawaran tercantum pada tabel di bawah.</div>
                </div>
            </section>

            <section class="items-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:52px">No</th>
                            <th>Deskripsi</th>
                            <th class="num">Qty</th>
                            <th class="num">Harga</th>
                            <th class="num">Disc</th>
                            <th class="num">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quotation['items'] as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td>
                                    <div class="desc-title"><?= html_escape($item['description']); ?></div>
                                    <?php if (!empty($item['unit'])): ?>
                                        <div class="meta">Satuan: <?= html_escape($item['unit']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="num"><?= rtrim(rtrim(number_format((float) $item['qty'], 2, '.', ','), '0'), '.'); ?></td>
                                <td class="num"><?= app_currency($item['price']); ?></td>
                                <td class="num"><?= number_format((float) $item['discount_percent'], 0); ?>%</td>
                                <td class="num"><?= app_currency($item['total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section class="totals-grid">
                <div>
                    <div class="notes-card">
                        <h3>Catatan Penawaran</h3>
                        <div class="meta"><?= nl2br(html_escape($quotation['notes'] ?: 'Penawaran ini dapat disesuaikan kembali sesuai kebutuhan dan ruang lingkup pekerjaan.')); ?></div>
                    </div>
                    <div class="notes-card" style="margin-top:12px;">
                        <h3>Syarat & Ketentuan</h3>
                        <div class="meta"><?= nl2br(html_escape($quotation['terms'] ?: 'Pembayaran dan pelaksanaan pekerjaan mengikuti kesepakatan final setelah penawaran disetujui.')); ?></div>
                    </div>
                </div>
                <div>
                    <div class="totals-card">
                        <h3>Ringkasan Nilai</h3>
                        <table class="totals-table">
                            <tr>
                                <td>Subtotal</td>
                                <td><?= app_currency($quotation['subtotal']); ?></td>
                            </tr>
                            <tr>
                                <td>Discount (<?= number_format((float) $quotation['discount_percent'], 0); ?>%)</td>
                                <td><?= app_currency($quotation['discount_amount']); ?></td>
                            </tr>
                            <tr>
                                <td>Pajak (<?= number_format((float) $quotation['tax_percent'], 0); ?>%)</td>
                                <td><?= app_currency($quotation['tax_amount']); ?></td>
                            </tr>
                            <tr>
                                <td>Grand Total</td>
                                <td><?= app_currency($quotation['total']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="auth-card" style="margin-top:12px;">
                        <h3>Authorized Quotation</h3>
                        <div class="meta">Penawaran ini diterbitkan resmi oleh <?= html_escape($company_name); ?> pada tanggal <?= app_date($quotation['quotation_date']); ?> dan berlaku sampai <?= app_date($quotation['valid_until']); ?>.</div>
                        <div class="signatory">
                            <div>
                                <div class="sign-name"><?= html_escape($collector_name); ?></div>
                                <div class="sign-role"><?= html_escape($collector_title); ?></div>
                            </div>
                            <div class="qr-box"><canvas id="quotation-qrcode"></canvas></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="footer-band">
                <div>Dokumen ini dibuat secara sistem dan dapat diverifikasi melalui QR di sisi kanan.</div>
                <div><?= html_escape($company_name); ?></div>
            </section>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    (function () {
        var target = document.getElementById('quotation-qrcode');
        if (!target || typeof QRCode === 'undefined') {
            return;
        }
        QRCode.toCanvas(target, <?= json_encode($verification_payload); ?>, {
            width: 80,
            margin: 0,
            color: { dark: '#132238', light: '#ffffff' }
        });
    })();
</script>
</body>
</html>
