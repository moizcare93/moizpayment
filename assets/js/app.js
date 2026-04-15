(function () {
    function calculateDocumentTotals() {
        let subtotal = 0;
        document.querySelectorAll('#item-table tbody tr').forEach(function (row) {
            const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
            const price = parseFloat(row.querySelector('.item-price')?.value || 0);
            const discount = parseFloat(row.querySelector('.item-discount')?.value || 0);
            const total = (qty * price) - ((qty * price) * discount / 100);
            subtotal += total;
            const totalField = row.querySelector('.item-total');
            if (totalField) {
                totalField.value = total.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
            }
        });

        const discountPercent = parseFloat(document.querySelector('[name="discount_percent"]')?.value || 0);
        const taxPercent = parseFloat(document.querySelector('[name="tax_percent"]')?.value || 0);
        const discountAmount = subtotal * discountPercent / 100;
        const grandTotal = (subtotal - discountAmount) + ((subtotal - discountAmount) * taxPercent / 100);

        const subtotalNode = document.querySelector('[data-role="subtotal"]');
        const grandNode = document.querySelector('[data-role="grand-total"]');
        if (subtotalNode) {
            subtotalNode.textContent = subtotal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
        }
        if (grandNode) {
            grandNode.textContent = grandTotal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
        }

        const inlineGrandNode = document.querySelector('[data-role="grand-total-inline"]');
        if (inlineGrandNode) {
            inlineGrandNode.textContent = grandTotal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
        }

        calculateTermTotals();
    }

    function calculateTermTotals() {
        let termTotal = 0;
        document.querySelectorAll('.term-amount').forEach(function (field) {
            termTotal += parseFloat(field.value || 0);
        });

        const termNode = document.querySelector('[data-role="term-total"]');
        if (termNode) {
            termNode.textContent = termTotal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
        }
    }

    function getGrandTotalNumeric() {
        let subtotal = 0;
        document.querySelectorAll('#item-table tbody tr').forEach(function (row) {
            const qty = parseFloat(row.querySelector('.item-qty')?.value || 0);
            const price = parseFloat(row.querySelector('.item-price')?.value || 0);
            const discount = parseFloat(row.querySelector('.item-discount')?.value || 0);
            subtotal += (qty * price) - ((qty * price) * discount / 100);
        });

        const discountPercent = parseFloat(document.querySelector('[name="discount_percent"]')?.value || 0);
        const taxPercent = parseFloat(document.querySelector('[name="tax_percent"]')?.value || 0);
        const discountAmount = subtotal * discountPercent / 100;
        return (subtotal - discountAmount) + ((subtotal - discountAmount) * taxPercent / 100);
    }

    function fillSingleTermWithGrandTotal(force = false) {
        const fields = document.querySelectorAll('.term-amount');
        if (fields.length !== 1) {
            return;
        }

        const currentValue = parseFloat(fields[0].value || 0);
        if (!force && currentValue > 0) {
            return;
        }

        fields[0].value = getGrandTotalNumeric().toFixed(2);
        calculateTermTotals();
    }

    document.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'add-item-row') {
            const tbody = document.querySelector('#item-table tbody');
            if (!tbody) {
                return;
            }

            const index = tbody.querySelectorAll('tr').length;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="items[${index}][description]" class="form-control item-description"></td>
                <td><input type="number" step="0.01" name="items[${index}][qty]" class="form-control item-qty calc-trigger" value="1"></td>
                <td><input type="text" name="items[${index}][unit]" class="form-control item-unit"></td>
                <td><input type="number" step="0.01" name="items[${index}][price]" class="form-control item-price calc-trigger" value="0"></td>
                <td><input type="number" step="0.01" name="items[${index}][discount_percent]" class="form-control item-discount calc-trigger" value="0"></td>
                <td><input type="text" class="form-control item-total" readonly></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm remove-item-row">X</button></td>
            `;
            tbody.appendChild(row);
            calculateDocumentTotals();
        }

        if (event.target && event.target.classList.contains('remove-item-row')) {
            event.target.closest('tr')?.remove();
            calculateDocumentTotals();
        }

        if (event.target && event.target.id === 'add-term-row') {
            const tbody = document.querySelector('#term-table tbody');
            if (!tbody) {
                return;
            }

            const index = tbody.querySelectorAll('tr').length;
            const dueDate = document.querySelector('[name="due_date"]')?.value || '';
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="terms_schedule[${index}][term_label]" class="form-control" placeholder="DP 1 / Termin 2 / Pelunasan"></td>
                <td><input type="date" name="terms_schedule[${index}][due_date]" class="form-control" value="${dueDate}"></td>
                <td><input type="number" step="0.01" name="terms_schedule[${index}][amount]" class="form-control term-amount" value="0"></td>
                <td><input type="text" name="terms_schedule[${index}][notes]" class="form-control" placeholder="Catatan termin"></td>
                <td><button type="button" class="btn btn-outline-danger btn-sm remove-term-row">X</button></td>
            `;
            tbody.appendChild(row);
            calculateTermTotals();
        }

        if (event.target && event.target.classList.contains('remove-term-row')) {
            event.target.closest('tr')?.remove();
            calculateTermTotals();
        }

        if (event.target && event.target.id === 'fill-single-term') {
            fillSingleTermWithGrandTotal(true);
        }
    });

    document.addEventListener('input', function (event) {
        if (event.target && event.target.classList.contains('calc-trigger')) {
            calculateDocumentTotals();
        }

        if (event.target && event.target.classList.contains('term-amount')) {
            calculateTermTotals();
        }
    });

    if (window.moizFinanceChart) {
        const ctx = document.getElementById('financeChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: window.moizFinanceChart.labels,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: window.moizFinanceChart.income,
                            borderColor: '#12b886',
                            backgroundColor: 'rgba(18,184,134,0.1)',
                            tension: 0.25
                        },
                        {
                            label: 'Pengeluaran',
                            data: window.moizFinanceChart.expense,
                            borderColor: '#ff6b6b',
                            backgroundColor: 'rgba(255,107,107,0.1)',
                            tension: 0.25
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { labels: { color: '#ecf2ff' } }
                    },
                    scales: {
                        x: { ticks: { color: '#95a0b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                        y: { ticks: { color: '#95a0b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                    }
                }
            });
        }
    }

    calculateDocumentTotals();
    calculateTermTotals();
    fillSingleTermWithGrandTotal(false);
})();
