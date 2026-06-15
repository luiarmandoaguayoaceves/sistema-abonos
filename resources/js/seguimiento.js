document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-seguimiento-page]');

    if (!page) {
        return;
    }

    const searchInput = document.querySelector('[data-pedidos-search]');
    const pedidoRows = document.querySelectorAll('[data-pedido-row]');
    const detailRows = document.querySelectorAll('[data-detalle-row]');
    const emptySearchRow = document.querySelector('[data-empty-search-row]');

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
});