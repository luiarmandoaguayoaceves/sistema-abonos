document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-abonos-page]');

    if (!page) {
        return;
    }

    const toggleButton = document.querySelector('[data-toggle-abonos-view]');
    const title = document.querySelector('[data-abonos-title]');
    const pendientesList = document.querySelector('[data-pendientes-list]');
    const pagadosList = document.querySelector('[data-pagados-list]');
    const clienteFilter = document.querySelector('[data-cliente-filter]');
    const pedidoSearch = document.querySelector('[data-pedido-search]');
    const pedidoSelect = document.querySelector('[data-pedido-select]');

    function toggleViews() {
        const showingPagados = !pagadosList.classList.contains('hidden');

        if (showingPagados) {
            pagadosList.classList.add('hidden');
            pendientesList.classList.remove('hidden');
            title.textContent = 'Pendientes de pago';
            toggleButton.textContent = 'Ver historial pagados';
            toggleButton.className = 'rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-200';
            return;
        }

        pendientesList.classList.add('hidden');
        pagadosList.classList.remove('hidden');
        title.textContent = 'Historial de pagados';
        toggleButton.textContent = 'Ver pendientes';
        toggleButton.className = 'rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-100';
    }

    function filterCards() {
        const selectedClient = clienteFilter.value.toLowerCase();
        const cards = document.querySelectorAll('[data-abono-card]');

        cards.forEach((card) => {
            const client = card.dataset.client.toLowerCase();
            const visible = !selectedClient || client === selectedClient;

            card.classList.toggle('hidden', !visible);
        });
    }

    function filterPedidoOptions() {
        const query = pedidoSearch.value.trim().toLowerCase();
        const options = pedidoSelect.querySelectorAll('option');

        options.forEach((option, index) => {
            if (index === 0) {
                option.hidden = false;
                return;
            }

            option.hidden = query.length > 0 && !option.textContent.toLowerCase().includes(query);
        });
    }

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-confirm-liquidar]');

        if (!form) {
            return;
        }

        const confirmed = window.confirm('¿Marcar este pedido como pagado totalmente?');

        if (!confirmed) {
            event.preventDefault();
        }
    });

    if (toggleButton) {
        toggleButton.addEventListener('click', toggleViews);
    }

    if (clienteFilter) {
        clienteFilter.addEventListener('change', filterCards);
    }

    if (pedidoSearch && pedidoSelect) {
        pedidoSearch.addEventListener('input', filterPedidoOptions);
    }
});