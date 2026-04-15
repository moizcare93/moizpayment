<?php
$company_name = setting_value($app_settings, 'company_name', 'MoizPayment');
$company_logo = setting_value($app_settings, 'company_logo');
$company_phone = setting_value($app_settings, 'company_phone', '');
$company_email = setting_value($app_settings, 'company_email', '');
$company_website = setting_value($app_settings, 'company_website', '');
$company_npwp = setting_value($app_settings, 'company_npwp', '');
$company_city = setting_value($app_settings, 'company_city', '');
$company_address = trim(
    setting_value($app_settings, 'company_address', '') . "\n" .
    setting_value($app_settings, 'company_city', '') . ' ' .
    setting_value($app_settings, 'company_province', '') . ' ' .
    setting_value($app_settings, 'company_postal_code', '')
);
$signatory_name = setting_value($app_settings, 'collector_name', $company_name);
$signatory_title = setting_value($app_settings, 'collector_title', 'Pimpinan / Authorized Signatory');
$client_company = !empty($client['company_name']) ? $client['company_name'] : ($client['name'] ?? '-');
$client_address = trim(($client['address'] ?? '') . "\n" . ($client['city'] ?? ''));
$opening_text = $quotation['notes'] ?: 'Dengan hormat, bersama surat ini kami sampaikan penawaran harga sesuai kebutuhan pekerjaan/pengadaan yang telah dibahas sebelumnya. Adapun rincian penawaran kami sampaikan sebagai berikut:';
$terms_text = $quotation['terms'] ?: 'Penawaran ini berlaku sesuai masa berlaku yang tercantum dan dapat ditinjau kembali apabila terdapat perubahan ruang lingkup pekerjaan.';
$verification_payload = "Surat Penawaran\n"
    . "Penerbit: {$company_name}\n"
    . "Kepada: {$client_company}\n"
    . "Nomor Penawaran: {$quotation['quotation_number']}\n"
    . "Tanggal: " . app_date($quotation['quotation_date']) . "\n"
    . "Berlaku Sampai: " . app_date($quotation['valid_until']) . "\n"
    . "Penandatangan: {$signatory_name} ({$signatory_title})";
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape($quotation['quotation_number']); ?> | Surat Penawaran</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #e9edf2;
            color: #111827;
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.45;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .shell {
            max-width: 920px;
            margin: 14px auto;
            padding: 0 10px;
        }
        .toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .toolbar button {
            border: 0;
            border-radius: 999px;
            background: #1d4ed8;
            color: #fff;
            padding: 8px 16px;
            font: 700 12px Arial, Helvetica, sans-serif;
            cursor: pointer;
        }
        .paper {
            background: #fff;
            padding: 22mm 18mm 20mm;
            box-shadow: 0 10px 28px rgba(17, 24, 39, .08);
        }
        .letterhead {
            display: grid;
            grid-template-columns: 78px 1fr;
            gap: 14px;
            align-items: start;
            padding-bottom: 10px;
            border-bottom: 3px solid #111827;
            margin-bottom: 12px;
        }
        .logo-box {
            width: 78px;
            height: 78px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .logo-fallback {
            width: 78px;
            height: 78px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #9ca3af;
            font-size: 26pt;
            font-weight: 700;
        }
        .head-center {
            text-align: center;
        }
        .head-center h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .02em;
        }
        .head-center .sub {
            margin-top: 4px;
            font-size: 10pt;
            line-height: 1.35;
            white-space: pre-line;
        }
        .doc-title {
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 6px 0 14px;
            font-size: 13pt;
            letter-spacing: .04em;
        }
        .top-meta {
            display: grid;
            grid-template-columns: 1fr 240px;
            gap: 18px;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 1px 0;
            vertical-align: top;
        }
        .meta-table td:first-child {
            width: 80px;
        }
        .date-block {
            text-align: right;
        }
        .recipient {
            margin-bottom: 12px;
        }
        .recipient strong {
            display: block;
        }
        p {
            margin: 0 0 10px;
            text-align: justify;
        }
        .offer-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 12px;
        }
        .offer-table th,
        .offer-table td {
            border: 1px solid #111827;
            padding: 6px 7px;
            vertical-align: top;
        }
        .offer-table th {
            text-align: center;
            font-weight: 700;
        }
        .offer-table td.num {
            text-align: right;
            white-space: nowrap;
        }
        .offer-table td.center {
            text-align: center;
        }
        .totals {
            margin-left: auto;
            width: 300px;
            border-collapse: collapse;
        }
        .totals td {
            border: 1px solid #111827;
            padding: 6px 8px;
        }
        .totals td:last-child {
            text-align: right;
            white-space: nowrap;
        }
        .totals tr:last-child td {
            font-weight: 700;
        }
        .notes-box {
            margin-top: 12px;
        }
        .notes-title {
            font-weight: 700;
            margin-bottom: 4px;
        }
        .closing {
            margin-top: 18px;
        }
        .signature-wrap {
            display: grid;
            grid-template-columns: 1fr 120px;
            gap: 20px;
            align-items: end;
            margin-top: 12px;
        }
        .signature-block {
            width: 290px;
            margin-left: auto;
            text-align: center;
        }
        .signature-space {
            height: 58px;
        }
        .signature-name {
            font-weight: 700;
            text-decoration: underline;
        }
        .qr-box {
            width: 108px;
            height: 108px;
            border: 1px solid #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .qr-box canvas {
            width: 96px !important;
            height: 96px !important;
        }
        .foot-note {
            margin-top: 12px;
            font-size: 9pt;
            color: #4b5563;
        }
        @page {
            size: A4;
            margin: 10mm;
        }
        @media print {
            body { background: #fff; }
            .shell { max-width: none; margin: 0; padding: 0; }
            .toolbar { display: none; }
            .paper { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print Penawaran</button>
    </div>
    <div class="paper">
        <section class="letterhead">
            <div class="logo-box">
                <?php if (!empty($company_logo)): ?>
                    <img src="<?= base_url($company_logo); ?>" alt="<?= html_escape($company_name); ?>">
                <?php else: ?>
                    <div class="logo-fallback"><?= html_escape(substr($company_name, 0, 1)); ?></div>
                <?php endif; ?>
            </div>
            <div class="head-center">
                <h1><?= html_escape($company_name); ?></h1>
                <div class="sub"><?= html_escape($company_address); ?>
Telp: <?= html_escape($company_phone ?: '-'); ?><?= $company_email ? ' | Email: ' . html_escape($company_email) : ''; ?><?= $company_website ? ' | Website: ' . html_escape($company_website) : ''; ?><?= $company_npwp ? "\nNPWP: " . html_escape($company_npwp) : ''; ?></div>
            </div>
        </section>

        <div class="doc-title">Surat Penawaran Harga</div>

        <section class="top-meta">
            <table class="meta-table">
                <tr>
                    <td>Nomor</td>
                    <td>: <?= html_escape($quotation['quotation_number']); ?></td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>: 1 (satu) berkas</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>: Penawaran Harga</td>
                </tr>
            </table>
            <div class="date-block">
                <?= html_escape($company_city ?: ''); ?>, <?= app_date($quotation['quotation_date']); ?>
            </div>
        </section>

        <section class="recipient">
            <div>Yth.</div>
            <strong><?= html_escape($client_company); ?></strong>
            <div><?= nl2br(html_escape($client_address ?: '-')); ?></div>
            <?php if (!empty($client['name'])): ?>
                <div>Up. <?= html_escape($client['name']); ?></div>
            <?php endif; ?>
            <div>di Tempat</div>
        </section>

        <p>Dengan hormat,</p>
        <p><?= nl2br(html_escape($opening_text)); ?></p>

        <table class="offer-table">
            <thead>
                <tr>
                    <th style="width:42px;">No</th>
                    <th>Uraian Penawaran</th>
                    <th style="width:66px;">Qty</th>
                    <th style="width:92px;">Satuan</th>
                    <th style="width:110px;">Harga Satuan</th>
                    <th style="width:70px;">Disc</th>
                    <th style="width:118px;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotation['items'] as $index => $item): ?>
                    <tr>
                        <td class="center"><?= $index + 1; ?></td>
                        <td><?= html_escape($item['description']); ?></td>
                        <td class="center"><?= rtrim(rtrim(number_format((float) $item['qty'], 2, '.', ','), '0'), '.'); ?></td>
                        <td class="center"><?= html_escape($item['unit']); ?></td>
                        <td class="num"><?= app_currency($item['price']); ?></td>
                        <td class="center"><?= number_format((float) $item['discount_percent'], 0); ?>%</td>
                        <td class="num"><?= app_currency($item['total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td><?= app_currency($quotation['subtotal']); ?></td>
            </tr>
            <tr>
                <td>Diskon</td>
                <td><?= app_currency($quotation['discount_amount']); ?></td>
            </tr>
            <tr>
                <td>Pajak</td>
                <td><?= app_currency($quotation['tax_amount']); ?></td>
            </tr>
            <tr>
                <td>Total Penawaran</td>
                <td><?= app_currency($quotation['total']); ?></td>
            </tr>
        </table>

        <div class="notes-box">
            <div class="notes-title">Ketentuan Penawaran:</div>
            <p><?= nl2br(html_escape($terms_text)); ?></p>
            <p>Penawaran ini berlaku sampai dengan tanggal <?= app_date($quotation['valid_until']); ?>.</p>
        </div>

        <div class="closing">
            <p>Demikian surat penawaran ini kami sampaikan. Besar harapan kami agar penawaran ini dapat menjadi bahan pertimbangan. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>
        </div>

        <div class="signature-wrap">
            <div class="signature-block">
                <div>Hormat kami,</div>
                <div><strong><?= html_escape($company_name); ?></strong></div>
                <div class="signature-space"></div>
                <div class="signature-name"><?= html_escape($signatory_name); ?></div>
                <div><?= html_escape($signatory_title); ?></div>
            </div>
            <div class="qr-box"><canvas id="quotation-qrcode"></canvas></div>
        </div>

        <div class="foot-note">
            Dokumen ini diterbitkan secara resmi oleh <?= html_escape($company_name); ?> dan dapat diverifikasi melalui QR code.
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    (function () {
        var target = document.getElementById('quotation-qrcode');
        if (!target || typeof QRCode === 'undefined') return;
        QRCode.toCanvas(target, <?= json_encode($verification_payload); ?>, {
            width: 96,
            margin: 0,
            color: { dark: '#111827', light: '#ffffff' }
        });
    })();
</script>
</body>
</html>
