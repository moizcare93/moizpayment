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
            background: var(--bg);
            color: var(--ink);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-shell {
            max-width: 960px;
            margin: 32px auto;
            padding: 0 16px;
        }

        .print-toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 16px;
            gap: 10px;
        }

        .print-toolbar button {
            border: 0;
            border-radius: 999px;
            padding: 10px 18px;
            background: var(--accent);
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .invoice-paper {
            background: var(--paper);
            border-radius: 24px;
            box-shadow: 0 28px 80px rgba(22, 31, 50, 0.12);
            overflow: hidden;
        }

        .invoice-topbar {
            height: 10px;
            background: linear-gradient(90deg, #1d4ed8, #0f9f6e);
        }

        .invoice-body {
            padding: 42px 46px 48px;
        }

        .hero {
            display: flex;
            justify-content: space-between;
            gap: 28px;
            align-items: flex-start;
            padding-bottom: 28px;
            border-bottom: 1px solid var(--line);
        }

        .identity {
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }

        .logo-box {
            width: 76px;
            height: 76px;
            border-radius: 20px;
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
            font-size: 28px;
            font-weight: 700;
            color: var(--accent);
        }

        .eyebrow {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .company-name,
        .invoice-title {
            margin: 0;
            font-size: 30px;
            line-height: 1;
            letter-spacing: -0.03em;
        }

        .company-meta,
        .invoice-meta,
        .small-meta {
            margin-top: 14px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
            white-space: pre-line;
        }

        .invoice-meta-card {
            min-width: 280px;
            padding: 24px 26px;
            background: linear-gradient(180deg, #f8fbff, #eef4ff);
            border: 1px solid #d8e4ff;
            border-radius: 22px;
        }

        .invoice-number {
            margin-top: 12px;
            font-size: 18px;
            font-weight: 700;
        }

        .status-pill {
            display: inline-block;
            margin-top: 14px;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
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
            gap: 24px;
            margin-top: 28px;
        }

        .info-card {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 22px 24px;
            min-height: 168px;
        }

        .info-card h3 {
            margin: 0 0 16px;
            font-size: 13px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .bill-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .summary-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 22px;
        }

        .summary-item {
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 16px 18px;
            background: #fbfcfe;
        }

        .summary-item span {
            display: block;
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 8px;
        }

        .summary-item strong {
            font-size: 22px;
            line-height: 1.2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-wrap {
            margin-top: 28px;
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden;
        }

        .items-wrap thead th {
            background: #f6f9fd;
            padding: 14px 16px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #4b5870;
            text-align: left;
        }

        .items-wrap tbody td {
            padding: 16px;
            border-top: 1px solid #eef2f7;
            font-size: 14px;
            vertical-align: top;
        }

        .items-wrap td.num,
        .items-wrap th.num {
            text-align: right;
            white-space: nowrap;
        }

        .desc-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .totals-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            margin-top: 28px;
            align-items: start;
        }

        .notes-card,
        .totals-card,
        .bank-card {
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 22px 24px;
        }

        .notes-card h3,
        .totals-card h3,
        .bank-card h3 {
            margin: 0 0 14px;
            font-size: 13px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .notes-card p,
        .bank-card p {
            margin: 0;
            color: #344054;
            line-height: 1.75;
            white-space: pre-line;
        }

        .totals-table td {
            padding: 10px 0;
            border-bottom: 1px solid #edf1f6;
            font-size: 14px;
        }

        .totals-table tr:last-child td {
            border-bottom: 0;
            padding-top: 16px;
            font-size: 19px;
            font-weight: 700;
        }

        .totals-table td:last-child {
            text-align: right;
        }

        .footer-band {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 12px;
            display: flex;
            justify-content: space-between;
            gap: 18px;
        }

        @page {
            size: A4;
            margin: 12mm;
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

                <div class="invoice-meta-card">
                    <div class="eyebrow">Tax Invoice</div>
                    <h2 class="invoice-title">Invoice</h2>
                    <div class="invoice-number"><?= html_escape($invoice['invoice_number']); ?></div>
                    <div class="small-meta">
                        Tanggal terbit: <?= app_date($invoice['invoice_date']); ?><br>
                        Jatuh tempo: <?= app_date($invoice['due_date']); ?><br>
                        Status pembayaran: <?= ucfirst($invoice['status']); ?>
                    </div>
                    <div class="status-pill status-<?= html_escape($invoice['status']); ?>"><?= strtoupper(html_escape($invoice['status'])); ?></div>
                </div>
            </section>

            <section class="info-grid">
                <div class="info-card">
                    <h3>Bill To</h3>
                    <div class="bill-name"><?= html_escape($client['company_name'] ?: $client['name']); ?></div>
                    <div class="small-meta"><?= html_escape($client['name']); ?><br>
                        <?= html_escape($client['address'] ?? ''); ?><br>
                        <?= html_escape($client['city'] ?? ''); ?><br>
                        <?= html_escape($client['phone'] ?? ''); ?><?= !empty($client['email']) ? '  |  ' . html_escape($client['email']) : ''; ?>
                    </div>
                </div>
                <div class="info-card">
                    <h3>Payment Snapshot</h3>
                    <div class="summary-strip">
                        <div class="summary-item">
                            <span>Total Invoice</span>
                            <strong><?= app_currency($invoice['total']); ?></strong>
                        </div>
                        <div class="summary-item">
                            <span>Paid</span>
                            <strong><?= app_currency($invoice['paid_amount']); ?></strong>
                        </div>
                        <div class="summary-item">
                            <span>Balance</span>
                            <strong><?= app_currency($balance_due); ?></strong>
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
                            <td>Grand Total</td>
                            <td><?= app_currency($invoice['total']); ?></td>
                        </tr>
                    </table>
                </div>
            </section>

            <section class="footer-band">
                <div><?= html_escape($invoice['terms'] ?: 'Pembayaran dilakukan sesuai termin yang tercantum pada invoice ini.'); ?></div>
                <div>Generated by <?= html_escape($company_name); ?></div>
            </section>
        </div>
    </div>
</div>
</body>
</html>
