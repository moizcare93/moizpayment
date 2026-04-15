<div class="panel-card mt-4">
    <div class="panel-header">
        <h2>Skema Termin Pembayaran</h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-light btn-sm" id="fill-single-term">Isi Full Total</button>
            <button type="button" class="btn btn-outline-light btn-sm" id="add-term-row">Tambah Termin</button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-dark align-middle" id="term-table">
            <thead>
                <tr>
                    <th>Label Termin</th>
                    <th>Jatuh Tempo</th>
                    <th>Nominal Tagihan</th>
                    <th>Catatan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($terms as $index => $term): ?>
                    <tr>
                        <td><input type="text" name="terms_schedule[<?= $index; ?>][term_label]" class="form-control" value="<?= html_escape($term['term_label'] ?? ''); ?>" placeholder="DP 1 / Termin 2 / Pelunasan"></td>
                        <td><input type="date" name="terms_schedule[<?= $index; ?>][due_date]" class="form-control" value="<?= html_escape($term['due_date'] ?? ''); ?>"></td>
                        <td><input type="number" step="0.01" name="terms_schedule[<?= $index; ?>][amount]" class="form-control term-amount" value="<?= html_escape($term['amount'] ?? 0); ?>"></td>
                        <td><input type="text" name="terms_schedule[<?= $index; ?>][notes]" class="form-control" value="<?= html_escape($term['notes'] ?? ''); ?>" placeholder="Mis. DP saat PO terbit"></td>
                        <td><button type="button" class="btn btn-outline-danger btn-sm remove-term-row">X</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="summary-box mt-3">
        <div>Total Termin: <strong data-role="term-total">Rp 0</strong></div>
        <div>Target Grand Total: <strong data-role="grand-total-inline">Rp 0</strong></div>
    </div>
</div>
