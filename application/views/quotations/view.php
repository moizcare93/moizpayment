<div class="panel-card">
    <div class="panel-header">
        <h2><?= html_escape($quotation['quotation_number']); ?></h2>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light" href="<?= site_url('quotations/edit/' . $quotation['id']); ?>">Edit</a>
            <a class="btn btn-primary" href="<?= site_url('quotations/convert/' . $quotation['id']); ?>">Konversi ke Invoice</a>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="detail-block">
                <div>Status: <span class="badge text-bg-<?= invoice_status_badge($quotation['status']); ?>"><?= ucfirst($quotation['status']); ?></span></div>
                <div>Tanggal: <?= app_date($quotation['quotation_date']); ?></div>
                <div>Valid sampai: <?= app_date($quotation['valid_until']); ?></div>
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <h3 class="h4"><?= app_currency($quotation['total']); ?></h3>
        </div>
    </div>
    <div class="table-responsive mt-4">
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    <th>Disc%</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotation['items'] as $item): ?>
                    <tr>
                        <td><?= html_escape($item['description']); ?></td>
                        <td><?= $item['qty']; ?></td>
                        <td><?= html_escape($item['unit']); ?></td>
                        <td><?= app_currency($item['price']); ?></td>
                        <td><?= $item['discount_percent']; ?></td>
                        <td><?= app_currency($item['total']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
