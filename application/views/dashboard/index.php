<section class="row g-4">
    <div class="col-md-4 col-xl-2">
        <div class="stat-card">
            <span>Invoice Bulan Ini</span>
            <strong><?= app_currency($summary['invoice_month']); ?></strong>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="stat-card">
            <span>Pembayaran Masuk</span>
            <strong><?= app_currency($summary['income_month']); ?></strong>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="stat-card">
            <span>Pengeluaran</span>
            <strong><?= app_currency($summary['expense_month']); ?></strong>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="stat-card">
            <span>Outstanding</span>
            <strong><?= app_currency($summary['outstanding']); ?></strong>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="stat-card">
            <span>Overdue</span>
            <strong><?= (int) $summary['overdue_count']; ?> Invoice</strong>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="stat-card">
            <span>Net Cashflow</span>
            <strong><?= app_currency($summary['net_cashflow']); ?></strong>
        </div>
    </div>
</section>

<section class="row g-4 mt-1">
    <div class="col-lg-8">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Tren Pemasukan vs Pengeluaran</h2>
            </div>
            <canvas id="financeChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel-card">
            <div class="panel-header">
                <h2>Aksi Cepat</h2>
            </div>
            <div class="quick-grid">
                <a class="btn btn-primary" href="<?= site_url('invoices/create'); ?>">Buat Invoice</a>
                <a class="btn btn-outline-light" href="<?= site_url('quotations/create'); ?>">Buat Penawaran</a>
                <a class="btn btn-outline-light" href="<?= site_url('clients/create'); ?>">Tambah Klien</a>
                <a class="btn btn-outline-light" href="<?= site_url('finance/income'); ?>">Catat Pembayaran</a>
            </div>
        </div>
    </div>
</section>

<section class="panel-card mt-4">
    <div class="panel-header">
        <h2>Invoice Terbaru</h2>
        <a class="btn btn-sm btn-outline-light" href="<?= site_url('invoices'); ?>">Lihat semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>Klien</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_invoices as $row): ?>
                    <tr>
                        <td><a href="<?= site_url('invoices/view/' . $row['id']); ?>"><?= html_escape($row['invoice_number']); ?></a></td>
                        <td><?= html_escape($row['client_name']); ?></td>
                        <td><?= app_date($row['invoice_date']); ?></td>
                        <td><?= app_currency($row['total']); ?></td>
                        <td><span class="badge text-bg-<?= invoice_status_badge($row['status']); ?>"><?= ucfirst($row['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
window.moizFinanceChart = <?= json_encode($trend); ?>;
</script>
