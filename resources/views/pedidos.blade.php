@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- Encabezado --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Elaboración de pedidos
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Captura productos, calcula subtotal, IVA y total antes de guardar el pedido.
            </p>
        </div>

        <a
            href="{{ route('seguimiento') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
            Ver seguimiento
        </a>
    </div>

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Revisa la información del pedido:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Formulario producto --}}
        <section class="h-fit overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">
                    Agregar producto
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Añade modelos al resumen antes de guardar.
                </p>
            </div>

            <form id="formProducto" class="space-y-5 p-6">
                <div>
                    <label for="modelo" class="block text-sm font-medium text-slate-700">
                        Modelo <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="modelo"
                        placeholder="Ej. Bota-01"
                        required
                        autocomplete="off"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                </div>

                <div>
                    <label for="color" class="block text-sm font-medium text-slate-700">
                        Color <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="color"
                        placeholder="Ej. Negro"
                        required
                        autocomplete="off"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="pares" class="block text-sm font-medium text-slate-700">
                            Pares <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="pares"
                            min="1"
                            step="1"
                            required
                            placeholder="10"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        >
                    </div>

                    <div>
                        <label for="precio" class="block text-sm font-medium text-slate-700">
                            Precio unitario <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="precio"
                            step="0.01"
                            min="0.01"
                            required
                            placeholder="250.00"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
                >
                    Añadir al pedido
                </button>
            </form>
        </section>

        {{-- Resumen pedido --}}
        <section class="space-y-6 lg:col-span-2">

            {{-- Datos generales --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Datos del pedido
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Selecciona el cliente y captura el número de pedido si aplica.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-5">
                    <div class="md:col-span-4">
                        <label for="clienteSearch" class="block text-sm font-medium text-slate-700">
                            Buscar cliente
                        </label>
                        <input
                            type="text"
                            id="clienteSearch"
                            placeholder="Escribe nombre o número de cliente..."
                            autocomplete="off"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        >

                        <label for="cliente_global" class="mt-3 block text-sm font-medium text-slate-700">
                            Cliente seleccionado <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="cliente_global"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        >
                            <option value="">Selecciona un cliente...</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->nombre }}">
                                    {{ $cliente->cve_cliente ?? 'S/N' }} - {{ $cliente->nombre }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            El buscador filtra las opciones del listado sin usar librerías externas.
                        </p>
                    </div>

                    <div>
                        <label for="pedido" class="block text-sm font-medium text-slate-700">
                            N. Pedido
                        </label>
                        <input
                            type="text"
                            id="pedido"
                            placeholder="Opcional"
                            autocomplete="off"
                            class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        >
                    </div>
                </div>
            </div>

            {{-- Tabla resumen --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">
                            Resumen del pedido
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Productos agregados antes de confirmar.
                        </p>
                    </div>

                    <span
                        id="contadorItems"
                        class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                    >
                        0 productos
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Modelo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Color</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Pares</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Precio</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Subtotal</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Acción</th>
                            </tr>
                        </thead>

                        <tbody id="cuerpoTabla" class="divide-y divide-slate-200 bg-white">
                            <tr id="filaVacia">
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                    Agrega al menos un producto para iniciar el pedido.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Totales --}}
                <div class="border-t border-slate-200 bg-slate-50 px-6 py-5">
                    <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                        <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm">
                            <input
                                type="checkbox"
                                id="aplicarIvaGlobal"
                                class="h-5 w-5 rounded border-slate-300 text-slate-900 focus:ring-slate-400"
                            >
                            Aplicar IVA 16%
                        </label>

                        <div class="w-full max-w-sm space-y-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="flex justify-between text-sm text-slate-600">
                                <span>Subtotal</span>
                                <span id="resumenSubtotal" class="font-semibold text-slate-900">$0.00</span>
                            </div>

                            <div class="flex justify-between text-sm text-slate-600">
                                <span>IVA 16%</span>
                                <span id="resumenIva" class="font-semibold text-slate-900">$0.00</span>
                            </div>

                            <div class="border-t border-slate-200 pt-2">
                                <div class="flex justify-between text-lg font-bold text-slate-900">
                                    <span>Total</span>
                                    <span id="resumenTotal">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form final --}}
            <form action="{{ route('pedidos.store') }}" method="POST" id="formFinal">
                @csrf
                <input type="hidden" name="cliente" id="hiddenCliente">
                <input type="hidden" name="datos_pedido" id="inputHiddenDatos">
                <input type="hidden" name="iva_aplicado" id="hiddenIvaStatus" value="0">
                <input type="hidden" name="pedido" id="hiddenPedido">

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-6 py-4 text-base font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
                >
                    Guardar pedido completo
                </button>
            </form>
        </section>
    </div>
</div>

<script>
    const itemsPedido = [];

    const formProducto = document.getElementById('formProducto');
    const formFinal = document.getElementById('formFinal');
    const cuerpoTabla = document.getElementById('cuerpoTabla');
    const clienteSearch = document.getElementById('clienteSearch');
    const clienteGlobal = document.getElementById('cliente_global');
    const pedidoInput = document.getElementById('pedido');
    const aplicarIvaGlobal = document.getElementById('aplicarIvaGlobal');

    const hiddenCliente = document.getElementById('hiddenCliente');
    const hiddenPedido = document.getElementById('hiddenPedido');
    const inputHiddenDatos = document.getElementById('inputHiddenDatos');
    const hiddenIvaStatus = document.getElementById('hiddenIvaStatus');

    const resumenSubtotal = document.getElementById('resumenSubtotal');
    const resumenIva = document.getElementById('resumenIva');
    const resumenTotal = document.getElementById('resumenTotal');
    const contadorItems = document.getElementById('contadorItems');

    const formatoMoneda = new Intl.NumberFormat('es-MX', {
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

    function limpiarFormularioProducto() {
        formProducto.reset();
        document.getElementById('modelo').focus();
    }

    function agregarProducto() {
        const modelo = document.getElementById('modelo').value.trim();
        const color = document.getElementById('color').value.trim();
        const pares = Number.parseInt(document.getElementById('pares').value, 10);
        const precio = Number.parseFloat(document.getElementById('precio').value);

        if (!modelo || !color || Number.isNaN(pares) || Number.isNaN(precio) || pares <= 0 || precio <= 0) {
            alert('Completa modelo, color, pares y precio con valores válidos.');
            return;
        }

        const subtotalItem = Number((pares * precio).toFixed(2));

        itemsPedido.push({
            modelo,
            color,
            pares,
            precio,
            subtotalItem,
        });

        limpiarFormularioProducto();
        actualizarInterfaz();
    }

    function eliminarItem(index) {
        itemsPedido.splice(index, 1);
        actualizarInterfaz();
    }

    function sincronizarOcultos(subtotalAcumulado = 0) {
        const aplicarIva = aplicarIvaGlobal.checked;
        const cliente = clienteGlobal.value;
        const pedido = pedidoInput.value.trim();

        hiddenCliente.value = cliente;
        hiddenPedido.value = pedido;
        inputHiddenDatos.value = JSON.stringify(itemsPedido);
        hiddenIvaStatus.value = aplicarIva ? 1 : 0;

        const montoIva = aplicarIva ? Number((subtotalAcumulado * 0.16).toFixed(2)) : 0;
        const totalFinal = Number((subtotalAcumulado + montoIva).toFixed(2));

        resumenSubtotal.textContent = formatoMoneda.format(subtotalAcumulado);
        resumenIva.textContent = formatoMoneda.format(montoIva);
        resumenTotal.textContent = formatoMoneda.format(totalFinal);

        contadorItems.textContent = `${itemsPedido.length} ${itemsPedido.length === 1 ? 'producto' : 'productos'}`;
    }

    function actualizarInterfaz() {
        let subtotalAcumulado = 0;

        if (itemsPedido.length === 0) {
            cuerpoTabla.innerHTML = `
                <tr id="filaVacia">
                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                        Agrega al menos un producto para iniciar el pedido.
                    </td>
                </tr>
            `;

            sincronizarOcultos(0);
            return;
        }

        cuerpoTabla.innerHTML = '';

        itemsPedido.forEach((item, index) => {
            subtotalAcumulado += item.subtotalItem;

            const row = document.createElement('tr');
            row.className = 'transition hover:bg-slate-50';

            row.innerHTML = `
                <td class="px-6 py-4 text-sm font-medium text-slate-900">${escapeHtml(item.modelo)}</td>
                <td class="px-6 py-4 text-sm text-slate-700">${escapeHtml(item.color)}</td>
                <td class="px-6 py-4 text-center text-sm text-slate-700">${item.pares}</td>
                <td class="px-6 py-4 text-right text-sm text-slate-700">${formatoMoneda.format(item.precio)}</td>
                <td class="px-6 py-4 text-right text-sm font-semibold text-slate-900">${formatoMoneda.format(item.subtotalItem)}</td>
                <td class="px-6 py-4 text-right text-sm">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700"
                        data-index="${index}"
                    >
                        Quitar
                    </button>
                </td>
            `;

            cuerpoTabla.appendChild(row);
        });

        sincronizarOcultos(subtotalAcumulado);
    }

    function filtrarClientes() {
        const query = clienteSearch.value.trim().toLowerCase();
        const options = clienteGlobal.querySelectorAll('option');

        options.forEach((option, index) => {
            if (index === 0) {
                option.hidden = false;
                return;
            }

            const text = option.textContent.toLowerCase();
            option.hidden = query.length > 0 && !text.includes(query);
        });
    }

    formProducto.addEventListener('submit', function (event) {
        event.preventDefault();
        agregarProducto();
    });

    cuerpoTabla.addEventListener('click', function (event) {
        const button = event.target.closest('button[data-index]');

        if (!button) {
            return;
        }

        eliminarItem(Number(button.dataset.index));
    });

    clienteSearch.addEventListener('input', filtrarClientes);
    clienteGlobal.addEventListener('change', actualizarInterfaz);
    pedidoInput.addEventListener('input', actualizarInterfaz);
    aplicarIvaGlobal.addEventListener('change', actualizarInterfaz);

    formFinal.addEventListener('submit', function (event) {
        actualizarInterfaz();

        if (!clienteGlobal.value.trim()) {
            event.preventDefault();
            alert('Selecciona un cliente antes de guardar.');
            return;
        }

        if (itemsPedido.length === 0) {
            event.preventDefault();
            alert('El pedido está vacío. Agrega al menos un producto.');
        }
    });

    actualizarInterfaz();
</script>
@endsection