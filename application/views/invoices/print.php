<?php
$company_name = setting_value($app_settings, 'company_name', 'MoizPayment');
$company_logo = setting_value($app_settings, 'company_logo');
$company_address = trim(
    setting_value($app_settings, 'company_address', '') . "\n" .
    setting_value($app_settings, 'company_city', '') . ' ' .
    setting_value($app_settings, 'company_province', '') . ' ' .
    setting_value($app_settings, 'company_postal_code', '')
);
$balance_due = (float) $invoice['total'] - (float) $invoice['paid_amount'];
$collector_name = setting_value($app_settings, 'collector_name', $company_name);
$collector_title = setting_value($app_settings, 'collector_title', 'Finance & Billing Officer');
$billed_company = !empty($client['company_name']) ? $client['company_name'] : $client['name'];
$verification_payload = "Invoice Verification\n"
    . "Penerbit: {$company_name}\n"
    . "Ditagihkan ke: {$billed_company}\n"
    . "Nomor Invoice: {$invoice['invoice_number']}\n"
    . "Tanggal Terbit: " . app_date($invoice['invoice_date']) . "\n"
    . "Jatuh Tempo: " . app_date($invoice['due_date']) . "\n"
    . "Penagih: {$collector_name} ({$collector_title})";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= html_escape($invoice['invoice_number']); ?> | Print Invoice</title>
    <style>
        :root {
            --ink: #152033;
            --muted: #687489;
            --line: #d7deea;
            --line-strong: #b8c3d6;
            --accent: #1d4ed8;
            --accent-soft: #eef4ff;
            --paper: #ffffff;
            --bg: #eef2f8;
            --success: #0f9f6e;
            --warning: #b7791f;
            --danger: #c53030;
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
            gap: 10px;
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

        .invoice-paper {
            background: var(--paper);
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(22, 31, 50, 0.08);
            overflow: hidden;
        }

        .invoice-topbar {
            height: 7px;
            background: linear-gradient(90deg, #1d4ed8, #0f9f6e);
        }

        .invoice-body {
            padding: 18px 20px 20px;
        }

        .hero {
            display: flex;
            justify-content: flex-start;
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
            letter-spacing: 0.14em;
            margin-bottom: 4px;
            font-weight: 700;
        }

        .company-name,
        .invoice-title {
            margin: 0;
            font-size: 18px;
            line-height: 1;
            letter-spacing: -0.03em;
        }

        .company-meta,
        .invoice-meta,
        .small-meta {
            margin-top: 6px;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.4;
            white-space: pre-line;
        }

        .status-pill {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .status-draft { background: #eef2f7; color: #526077; }
        .status-sent { background: #e7f0ff; color: #1d4ed8; }
        .status-partial { background: #fff4dc; color: var(--warning); }
        .status-paid { background: #e8fbf2; color: var(--success); }
        .status-overdue { background: #ffebeb; color: var(--danger); }
        .status-cancelled { background: #ececec; color: #4b5563; }

        .info-grid {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 12px;
            margin-top: 12px;
        }

        .info-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
            min-height: 102px;
        }

        .info-card h3 {
            margin: 0 0 8px;
            font-size: 9px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .bill-name {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .meta-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 6px;
            margin-top: 4px;
        }

        .meta-row {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 7px 9px;
            background: #fbfcfe;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: baseline;
        }

        .meta-row span {
            color: var(--muted);
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .meta-row strong {
            font-size: 10px;
            line-height: 1.2;
            text-align: right;
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

        .terms-wrap {
            margin-top: 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
        }

        .terms-wrap thead th {
            background: #fdf7ec;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #74512a;
            text-align: left;
        }

        .terms-wrap tbody td {
            padding: 7px 10px;
            border-top: 1px solid #f2e6d2;
            font-size: 10px;
            line-height: 1.28;
        }

        .items-wrap thead th {
            background: #f6f9fd;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #4b5870;
            text-align: left;
        }

        .items-wrap tbody td {
            padding: 7px 10px;
            border-top: 1px solid #eef2f7;
            font-size: 10px;
            vertical-align: top;
            line-height: 1.28;
        }

        .items-wrap td.num,
        .items-wrap th.num {
            text-align: right;
            white-space: nowrap;
        }

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

        .notes-card,
        .totals-card,
        .bank-card,
        .trust-card {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px 12px;
        }

        .notes-card h3,
        .totals-card h3,
        .bank-card h3,
        .trust-card h3 {
            margin: 0 0 7px;
            font-size: 9px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .notes-card p,
        .bank-card p,
        .trust-card p {
            margin: 0;
            color: #344054;
            font-size: 10px;
            line-height: 1.45;
            white-space: pre-line;
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
            margin-bottom: 1px;
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

        .qr-box canvas,
        .qr-box img {
            width: 68px !important;
            height: 68px !important;
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
            body {
                background: #fff;
            }

            .print-shell {
                max-width: none;
                margin: 0;
                padding: 0;
            }

            .print-toolbar {
                display: none;
            }

            .invoice-paper {
                box-shadow: none;
                border-radius: 0;
            }

            .invoice-body {
                padding: 10px 12px 12px;
            }
        }
    </style>
</head>
<body>
<div class="print-shell">
    <div class="print-toolbar">
        <button type="button" onclick="window.print()">Print Invoice</button>
    </div>

    <div class="invoice-paper">
        <div class="invoice-topbar"></div>
        <div class="invoice-body">
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
                        <div class="company-meta"><?= html_escape(trim($company_address)); ?>
<?= html_escape(setting_value($app_settings, 'company_phone', '')); ?><?= setting_value($app_settings, 'company_email', '') ? '  |  ' . html_escape(setting_value($app_settings, 'company_email', '')) : ''; ?></div>
                    </div>
                </div>
            </section>

            <section class="info-grid">
                <div class="info-card">
                    <h3>Bill To</h3>
                    <div class="bill-name"><?= html_escape($billed_company); ?></div>
                    <div class="small-meta"><?= html_escape($client['name']); ?><br>
                        <?= html_escape($client['address'] ?? ''); ?><br>
                        <?= html_escape($client['city'] ?? ''); ?><br>
                        <?= html_escape($client['phone'] ?? ''); ?><?= !empty($client['email']) ? '  |  ' . html_escape($client['email']) : ''; ?>
                    </div>
                </div>
                <div class="info-card">
                    <h3>Invoice Reference</h3>
                    <div class="meta-list">
                        <div class="meta-row">
                            <span>Nomor Invoice</span>
                            <strong><?= html_escape($invoice['invoice_number']); ?></strong>
                        </div>
                        <div class="meta-row">
                            <span>Tanggal Terbit</span>
                            <strong><?= app_date($invoice['invoice_date']); ?></strong>
                        </div>
                        <div class="meta-row">
                            <span>Jatuh Tempo</span>
                            <strong><?= app_date($invoice['due_date']); ?></strong>
                        </div>
                        <div class="meta-row">
                            <span>Status</span>
                            <strong><?= strtoupper(html_escape($invoice['status'])); ?></strong>
                        </div>
                    </div>
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
                        <?php foreach ($invoice['items'] as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1; ?></td>
                                <td>
                                    <div class="desc-title"><?= html_escape($item['description']); ?></div>
                                    <?php if (!empty($item['unit'])): ?>
                                        <div class="small-meta">Satuan: <?= html_escape($item['unit']); ?></div>
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

            <?php if (!empty($invoice['terms'])): ?>
                <section class="terms-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Termin Penagihan</th>
                                <th>Jatuh Tempo</th>
                                <th class="num">Nominal</th>
                                <th class="num">Terbayar</th>
                                <th class="num">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoice['terms'] as $term): ?>
                                <tr>
                                    <td>
                                        <strong><?= html_escape($term['term_label']); ?></strong>
                                        <?php if (!empty($term['notes'])): ?>
                                            <div class="small-meta"><?= html_escape($term['notes']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= app_date($term['due_date']); ?></td>
                                    <td class="num"><?= app_currency($term['amount']); ?></td>
                                    <td class="num"><?= app_currency($term['paid_amount']); ?></td>
                                    <td class="num"><?= app_currency($term['remaining_amount']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php endif; ?>

            <section class="totals-grid">
                <div>
                    <div class="notes-card">
                        <h3>Notes</h3>
                        <p><?= html_escape($invoice['notes'] ?: 'Terima kasih atas kepercayaan Anda.'); ?></p>
                    </div>
                    <div class="bank-card" style="margin-top:18px;">
                        <h3>Payment Instruction</h3>
                        <p>Bank: <?= html_escape(setting_value($app_settings, 'bank_name', '-')); ?><br>
No. Rekening: <?= html_escape(setting_value($app_settings, 'bank_account_number', '-')); ?><br>
Atas Nama: <?= html_escape(setting_value($app_settings, 'bank_account_name', '-')); ?><br><br>
Mohon cantumkan nomor invoice <?= html_escape($invoice['invoice_number']); ?> pada saat pembayaran.</p>
                    </div>
                </div>

                <div class="totals-card">
                    <h3>Invoice Summary</h3>
                    <table class="totals-table">
                        <tr>
                            <td>Subtotal</td>
                            <td><?= app_currency($invoice['subtotal']); ?></td>
                        </tr>
                        <tr>
                            <td>Discount (<?= number_format((float) $invoice['discount_percent'], 0); ?>%)</td>
                            <td><?= app_currency($invoice['discount_amount']); ?></td>
                        </tr>
                        <tr>
                            <td>Tax (<?= number_format((float) $invoice['tax_percent'], 0); ?>%)</td>
                            <td><?= app_currency($invoice['tax_amount']); ?></td>
                        </tr>
                        <tr>
                            <td>Paid</td>
                            <td><?= app_currency($invoice['paid_amount']); ?></td>
                        </tr>
                        <tr>
                            <td>Balance Due</td>
                            <td><?= app_currency($balance_due); ?></td>
                        </tr>
                        <tr>
                            <td>Grand Total</td>
                            <td><?= app_currency($invoice['total']); ?></td>
                        </tr>
                    </table>

                    <div class="trust-card" style="margin-top:14px;">
                        <h3>Authorized Billing</h3>
                        <p>Invoice ini diterbitkan resmi oleh <?= html_escape($company_name); ?> dan ditagihkan kepada <?= html_escape($billed_company); ?> pada tanggal <?= app_date($invoice['invoice_date']); ?>.</p>
                        <div class="signatory">
                            <div>
                                <div class="sign-name"><?= html_escape($collector_name); ?></div>
                                <div class="sign-role"><?= html_escape($collector_title); ?></div>
                            </div>
                            <div class="qr-box"><canvas id="invoice-qrcode"></canvas></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="footer-band">
                <div><?= html_escape($invoice['terms'] ?: 'Pembayaran dilakukan sesuai termin yang tercantum pada invoice ini.'); ?></div>
                <div>Generated by <?= html_escape($company_name); ?></div>
            </section>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    (function () {
        var target = document.getElementById('invoice-qrcode');
        if (!target || typeof QRCode === 'undefined') {
            return;
        }

        QRCode.toCanvas(target, <?= json_encode($verification_payload); ?>, {
            width: 80,
            margin: 0,
            color: {
                dark: '#152033',
                light: '#ffffff'
            }
        });
    })();
</script>
</body>
</html>
