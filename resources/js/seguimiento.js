document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-seguimiento-page]');

    if (!page) {
        return;
    }

    const searchInput = document.querySelector('[data-pedidos-search]');
    const pedidoRows = document.querySelectorAll('[data-pedido-row]');
    const monthHeadings = document.querySelectorAll('[data-month-heading]');
    const emptySearchRow = document.querySelector('[data-empty-search-row]');
    const totalPares = document.querySelector('[data-total-pares]');
    const totalPedidos = document.querySelector('[data-total-pedidos]');

    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN',
        }).format(amount);
    }

    function updateMonthHeadings() {
        monthHeadings.forEach((heading) => {
            const monthGroup = heading.dataset.monthGroup;
            const hasVisibleRows = [...pedidoRows].some((row) => {
                return row.dataset.monthGroup === monthGroup && !row.classList.contains('hidden');
            });

            heading.classList.toggle('hidden', !hasVisibleRows);
        });
    }

    function updateVisibleTotal() {
        if (!totalPedidos && !totalPares) {
            return;
        }

        const totals = [...pedidoRows].reduce((currentTotals, row) => {
            if (row.classList.contains('hidden')) {
                return currentTotals;
            }

            return {
                pares: currentTotals.pares + Number(row.dataset.paresAmount || 0),
                total: currentTotals.total + Number(row.dataset.totalAmount || 0),
            };
        }, {
            pares: 0,
            total: 0,
        });

        if (totalPares) {
            totalPares.textContent = new Intl.NumberFormat('es-MX').format(totals.pares);
        }

        if (totalPedidos) {
            totalPedidos.textContent = formatCurrency(totals.total);
        }
    }

    function hideDetailRow(pedidoId) {
        const detailRow = document.querySelector(`[data-detalle-row="${pedidoId}"]`);
        const toggleButton = document.querySelector(`[data-toggle-detail="${pedidoId}"]`);

        if (!detailRow || !toggleButton) {
            return;
        }

        detailRow.classList.add('hidden');
        toggleButton.textContent = 'Ver detalles';
        toggleButton.setAttribute('aria-expanded', 'false');
    }

    function toggleDetailRow(pedidoId) {
        const detailRow = document.querySelector(`[data-detalle-row="${pedidoId}"]`);
        const toggleButton = document.querySelector(`[data-toggle-detail="${pedidoId}"]`);

        if (!detailRow || !toggleButton) {
            return;
        }

        const isHidden = detailRow.classList.toggle('hidden');

        toggleButton.textContent = isHidden ? 'Ver detalles' : 'Ocultar detalles';
        toggleButton.setAttribute('aria-expanded', String(!isHidden));
    }

    function filterRows() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleRows = 0;

        pedidoRows.forEach((row) => {
            const searchableText = row.dataset.searchableText.toLowerCase();
            const pedidoId = row.dataset.pedidoId;
            const isVisible = searchableText.includes(query);

            row.classList.toggle('hidden', !isVisible);

            if (!isVisible) {
                hideDetailRow(pedidoId);
            } else {
                visibleRows++;
            }
        });

        if (emptySearchRow) {
            emptySearchRow.classList.toggle('hidden', visibleRows > 0);
        }

        updateMonthHeadings();
        updateVisibleTotal();
    }

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-toggle-detail]');

        if (!button) {
            return;
        }

        toggleDetailRow(button.dataset.toggleDetail);
    });

    if (searchInput) {
        searchInput.addEventListener('input', filterRows);
    }

    updateMonthHeadings();
    updateVisibleTotal();
});
