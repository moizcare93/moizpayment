<div class="panel-card">
    <div class="panel-header">
        <h2>Laporan Keuangan</h2>
    </div>
    <?= form_open('reports', array('method' => 'get', 'class' => 'row g-3 align-items-end')); ?>
        <div class="col-md-3">
            <label class="form-label">Mulai</label>
            <input type="date" name="start" class="form-control" value="<?= html_escape($start); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Sampai</label>
            <input type="date" name="end" class="form-control" value="<?= html_escape($end); ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    <?= form_close(); ?>

    <div class="row g-4 mt-1">
        <div class="col-md-4">
            <div class="stat-card"><span>Total Pemasukan</span><strong><?= app_currency($income_total); ?></strong></div>
        </div>
        <div class="col-md-4">
            <div class="stat-card"><span>Total Pengeluaran</span><strong><?= app_currency($expense_total); ?></strong></div>
        </div>
        <div class="col-md-4">
            <div class="stat-card"><span>Piutang</span><strong><?= app_currency($receivable_total); ?></strong></div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <h3 class="h5">Pemasukan</h3>
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Nominal</th></tr></thead>
                    <tbody>
                    <?php foreach ($report['income'] as $row): ?>
                        <tr>
                            <td><?= app_date($row['payment_date']); ?></td>
                            <td><?= html_escape($row['description']); ?></td>
                            <td><?= app_currency($row['amount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-6">
            <h3 class="h5">Pengeluaran</h3>
            <div class="table-responsive">
                <table class="table table-dark">
                    <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Nominal</th></tr></thead>
                    <tbody>
                    <?php foreach ($report['expenses'] as $row): ?>
                        <tr>
                            <td><?= app_date($row['expense_date']); ?></td>
                            <td><?= html_escape($row['description']); ?></td>
                            <td><?= app_currency($row['amount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
