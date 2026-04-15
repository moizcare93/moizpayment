<div class="panel-card mt-4">
    <div class="panel-header">
        <div>
            <div class="section-kicker">Billing Lines</div>
            <h2>Item Tagihan</h2>
        </div>
        <button type="button" class="btn btn-outline-light btn-sm" id="add-item-row">Tambah Baris</button>
    </div>
    <div class="table-responsive">
        <table class="table table-dark align-middle" id="item-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    <th>Disc%</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                    <tr>
                        <td><input type="text" name="items[<?= $index; ?>][description]" class="form-control item-description" value="<?= html_escape($item['description'] ?? ''); ?>"></td>
                        <td><input type="number" step="0.01" name="items[<?= $index; ?>][qty]" class="form-control item-qty calc-trigger" value="<?= html_escape($item['qty'] ?? 1); ?>"></td>
                        <td><input type="text" name="items[<?= $index; ?>][unit]" class="form-control item-unit" value="<?= html_escape($item['unit'] ?? ''); ?>"></td>
                        <td><input type="number" step="0.01" name="items[<?= $index; ?>][price]" class="form-control item-price calc-trigger" value="<?= html_escape($item['price'] ?? 0); ?>"></td>
                        <td><input type="number" step="0.01" name="items[<?= $index; ?>][discount_percent]" class="form-control item-discount calc-trigger" value="<?= html_escape($item['discount_percent'] ?? 0); ?>"></td>
                        <td><input type="text" class="form-control item-total" readonly></td>
                        <td><button type="button" class="btn btn-outline-danger btn-sm remove-item-row">X</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
