document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('[data-pedidos-page]');

    if (!page) {
        return;
    }

    const state = {
        items: [],
    };

    const elements = {
        formProducto: document.getElementById('formProducto'),
        formFinal: document.getElementById('formFinal'),
        cuerpoTabla: document.getElementById('cuerpoTabla'),
        clienteSearch: document.getElementById('clienteSearch'),
        clienteGlobal: document.getElementById('cliente_global'),
        pedidoInput: document.getElementById('pedido'),
        aplicarIvaGlobal: document.getElementById('aplicarIvaGlobal'),

        hiddenCliente: document.getElementById('hiddenCliente'),
        hiddenPedido: document.getElementById('hiddenPedido'),
        inputHiddenDatos: document.getElementById('inputHiddenDatos'),
        hiddenIvaStatus: document.getElementById('hiddenIvaStatus'),

        resumenSubtotal: document.getElementById('resumenSubtotal'),
        resumenIva: document.getElementById('resumenIva'),
        resumenTotal: document.getElementById('resumenTotal'),
        contadorItems: document.getElementById('contadorItems'),

        modelo: document.getElementById('modelo'),
        color: document.getElementById('color'),
        pares: document.getElementById('pares'),
        precio: document.getElementById('precio'),
    };

    const currencyFormatter = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
    });

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function getSubtotal() {
        return state.items.reduce((total, item) => total + item.subtotalItem, 0);
    }

    function getIva(subtotal) {
        return elements.aplicarIvaGlobal.checked
            ? Number((subtotal * 0.16).toFixed(2))
            : 0;
    }

    function syncHiddenInputs(subtotal = 0) {
        const iva = getIva(subtotal);
        const total = Number((subtotal + iva).toFixed(2));

        elements.hiddenCliente.value = elements.clienteGlobal.value;
        elements.hiddenPedido.value = elements.pedidoInput.value.trim();
        elements.inputHiddenDatos.value = JSON.stringify(state.items);
        elements.hiddenIvaStatus.value = elements.aplicarIvaGlobal.checked ? 1 : 0;

        elements.resumenSubtotal.textContent = currencyFormatter.format(subtotal);
        elements.resumenIva.textContent = currencyFormatter.format(iva);
        elements.resumenTotal.textContent = currencyFormatter.format(total);

        elements.contadorItems.textContent = `${state.items.length} ${state.items.length === 1 ? 'producto' : 'productos'}`;
    }

    function renderEmptyTable() {
        elements.cuerpoTabla.innerHTML = `
            <tr id="filaVacia">
                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                    Agrega al menos un producto para iniciar el pedido.
                </td>
            </tr>
        `;
    }

    function renderItems() {
        elements.cuerpoTabla.innerHTML = '';

        state.items.forEach((item, index) => {
            const row = document.createElement('tr');

            row.className = 'transition hover:bg-slate-50';

            row.innerHTML = `
                <td class="px-6 py-4 text-sm font-medium text-slate-900">${escapeHtml(item.modelo)}</td>
                <td class="px-6 py-4 text-sm text-slate-700">${escapeHtml(item.color)}</td>
                <td class="px-6 py-4 text-center text-sm text-slate-700">${item.pares}</td>
                <td class="px-6 py-4 text-right text-sm text-slate-700">${currencyFormatter.format(item.precio)}</td>
                <td class="px-6 py-4 text-right text-sm font-semibold text-slate-900">${currencyFormatter.format(item.subtotalItem)}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700"
                        data-remove-item="${index}"
                    >
                        Quitar
                    </button>
                </td>
            `;

            elements.cuerpoTabla.appendChild(row);
        });
    }

    function updateUi() {
        const subtotal = getSubtotal();

        if (state.items.length === 0) {
            renderEmptyTable();
            syncHiddenInputs(0);
            return;
        }

        renderItems();
        syncHiddenInputs(subtotal);
    }

    function resetProductForm() {
        elements.formProducto.reset();
        elements.modelo.focus();
    }

    function addProduct() {
        const modelo = elements.modelo.value.trim();
        const color = elements.color.value.trim();
        const pares = Number.parseInt(elements.pares.value, 10);
        const precio = Number.parseFloat(elements.precio.value);

        if (!modelo || !color || Number.isNaN(pares) || Number.isNaN(precio) || pares <= 0 || precio <= 0) {
            alert('Completa modelo, color, pares y precio con valores válidos.');
            return;
        }

        const subtotalItem = Number((pares * precio).toFixed(2));

        state.items.push({
            modelo,
            color,
            pares,
            precio,
            subtotalItem,
        });

        resetProductForm();
        updateUi();
    }

    function removeProduct(index) {
        state.items.splice(index, 1);
        updateUi();
    }

    function filterClients() {
        const query = elements.clienteSearch.value.trim().toLowerCase();
        const options = elements.clienteGlobal.querySelectorAll('option');

        options.forEach((option, index) => {
            if (index === 0) {
                option.hidden = false;
                return;
            }

            const text = option.textContent.toLowerCase();
            option.hidden = query.length > 0 && !text.includes(query);
        });
    }

    elements.formProducto.addEventListener('submit', (event) => {
        event.preventDefault();
        addProduct();
    });

    elements.cuerpoTabla.addEventListener('click', (event) => {
        const button = event.target.closest('[data-remove-item]');

        if (!button) {
            return;
        }

        removeProduct(Number(button.dataset.removeItem));
    });

    elements.clienteSearch.addEventListener('input', filterClients);
    elements.clienteGlobal.addEventListener('change', updateUi);
    elements.pedidoInput.addEventListener('input', updateUi);
    elements.aplicarIvaGlobal.addEventListener('change', updateUi);

    elements.formFinal.addEventListener('submit', (event) => {
        updateUi();

        if (!elements.clienteGlobal.value.trim()) {
            event.preventDefault();
            alert('Selecciona un cliente antes de guardar.');
            return;
        }

        if (state.items.length === 0) {
            event.preventDefault();
            alert('El pedido está vacío. Agrega al menos un producto.');
        }
    });

    updateUi();
});