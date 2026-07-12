@extends('layouts.app')

@section('content')
<div data-seguimiento-page class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Seguimiento de pedidos
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Consulta pedidos, revisa detalles, cambia fecha de entrega y valida estatus de pago.
            </p>
        </div>

        <a
            href="{{ route('pedidos') }}"
            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800"
        >
            Nuevo pedido
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Revisa la información:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        Pedidos registrados
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Total cargado: {{ $pedidos->count() }} pedidos.
                    </p>
                </div>

                <div class="grid w-full gap-3 sm:grid-cols-2 lg:max-w-2xl">
                    <form action="{{ route('seguimiento') }}" method="GET">
                        <label for="mes" class="sr-only">Filtrar por mes</label>
                        <select
                            id="mes"
                            name="mes"
                            onchange="this.form.submit()"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        >
                            <option value="">Todos los meses</option>
                            @foreach ($monthOptions as $monthOption)
                                <option value="{{ $monthOption['value'] }}" @selected($selectedMonth === $monthOption['value'])>
                                    {{ $monthOption['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <div>
                        <label for="search" class="sr-only">Buscar pedido</label>
                        <input
                            type="text"
                            id="search"
                            data-pedidos-search
                            placeholder="Buscar por pedido, cliente o estatus..."
                            autocomplete="off"
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                        >
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            N. Pedido
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Fecha entrega
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Productos
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Total
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Acciones
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Eliminar
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 bg-white">
                    @php
                        $currentMonth = null;
                    @endphp
                    @forelse($pedidos as $pedido)
                        @php
                            $fechaEntrega = $pedido->fechaSeguimiento();
                            $monthKey = $pedido->mesSeguimiento();
                            $monthGroup = $pedidosPorMes->get($monthKey);
                            $totalDetalles = $pedido->detallesPedido->count();
                            $totalParesPedido = $pedido->totalPares();
                            $estatusTexto = $pedido->pagado ? 'Pagado' : 'Pendiente';
                            $searchableText = strtolower(
                                ($pedido->n_pedido ?? $pedido->id) . ' ' .
                                $pedido->cliente . ' ' .
                                $estatusTexto . ' ' .
                                $fechaEntrega?->format('d/m/Y H:i')
                            );
                        @endphp

                        @if ($currentMonth !== $monthKey)
                            @php
                                $currentMonth = $monthKey;
                            @endphp
                            <tr data-month-heading data-month-group="{{ $monthKey }}" class="bg-slate-100">
                                <td colspan="7" class="px-6 py-3">
                                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                        <span class="text-sm font-bold uppercase tracking-wide text-slate-700">
                                            {{ $monthGroup['label'] }}
                                        </span>
                                        <span class="text-sm font-semibold text-slate-700">
                                            {{ $monthGroup['pedidos']->count() }} pedidos · {{ number_format($monthGroup['pares']) }} pares · Subtotal ${{ number_format($monthGroup['total'], 2) }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        <tr
                            data-pedido-row
                            data-pedido-id="{{ $pedido->id }}"
                            data-month-group="{{ $monthKey }}"
                            data-pares-amount="{{ $totalParesPedido }}"
                            data-total-amount="{{ (float) $pedido->total }}"
                            data-searchable-text="{{ $searchableText }}"
                            class="transition hover:bg-slate-50"
                        >
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900">
                                {{ $pedido->n_pedido ?? $pedido->id }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                {{ optional($fechaEntrega)->format('d/m/Y H:i') }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $pedido->cliente }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                    {{ $totalDetalles }} {{ $totalDetalles === 1 ? 'producto' : 'productos' }}
                                </span>
                                <div class="mt-1 text-xs font-semibold text-slate-500">
                                    {{ number_format($totalParesPedido) }} pares
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="text-sm font-bold text-slate-900">
                                    ${{ number_format($pedido->total, 2) }}
                                </div>

                                <div class="mt-1">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $pedido->pagado ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $pedido->pagado ? 'Pagado' : 'Pendiente' }}
                                    </span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <button
                                    type="button"
                                    data-toggle-detail="{{ $pedido->id }}"
                                    aria-expanded="false"
                                    class="rounded-lg px-3 py-1.5 text-sm font-semibold text-blue-600 transition hover:bg-blue-50 hover:text-blue-700"
                                >
                                    Ver detalles
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form
                                    action="{{ route('pedidos.destroy', $pedido->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este pedido? Esta acción solo lo ocultará del sistema, no lo borrará de la base de datos.');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-100"
                                    >
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <tr data-detalle-row="{{ $pedido->id }}" data-month-group="{{ $monthKey }}" class="hidden bg-slate-50">
                            <td colspan="7" class="px-6 py-6">
                                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                                    <div class="mb-5 border-b border-slate-200 pb-5">
                                        <form
                                            action="{{ route('pedidos.updateFecha', $pedido->id) }}"
                                            method="POST"
                                            class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto]"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <div>
                                                <label class="block text-sm font-medium text-slate-700">
                                                    Fecha de entrega
                                                </label>
                                                <input
                                                    type="datetime-local"
                                                    name="fecha_entrega"
                                                    value="{{ optional($fechaEntrega)->format('Y-m-d\TH:i') }}"
                                                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                                                >
                                            </div>

                                            <div class="flex items-end">
                                                <button
                                                    type="submit"
                                                    class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 md:w-auto"
                                                >
                                                    Actualizar fecha
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="mb-3 flex items-center justify-between">
                                        <h3 class="text-sm font-bold uppercase tracking-wide text-slate-700">
                                            Desglose de productos
                                        </h3>

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                            {{ $totalDetalles }} items
                                        </span>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                                            <thead class="bg-slate-50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                        Modelo / Color
                                                    </th>
                                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                        Pares
                                                    </th>
                                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                        Precio
                                                    </th>
                                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                                        Subtotal
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody class="divide-y divide-slate-100 bg-white">
                                                @forelse ($pedido->detallesPedido as $detalle)
                                                    <tr>
                                                        <td class="px-4 py-3">
                                                            <div class="font-semibold text-slate-900">
                                                                {{ $detalle->modelo }}
                                                            </div>
                                                            <div class="text-xs text-slate-500">
                                                                {{ $detalle->color }}
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 text-center text-slate-700">
                                                            {{ $detalle->pares }}
                                                        </td>
                                                        <td class="px-4 py-3 text-right text-slate-700">
                                                            ${{ number_format($detalle->precio_unitario, 2) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-right font-semibold text-slate-900">
                                                            ${{ number_format($detalle->subtotal, 2) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
                                                            Este pedido no tiene productos registrados.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                                No hay pedidos registrados.
                            </td>
                        </tr>
                    @endforelse

                    <tr data-empty-search-row class="hidden">
                        <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500">
                            No se encontraron pedidos con esa búsqueda.
                        </td>
                    </tr>

                    @if ($pedidos->isNotEmpty())
                        <tr class="bg-slate-900 text-white">
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-semibold">
                                Total de pedidos mostrados
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-xs font-semibold uppercase tracking-wide text-slate-300">
                                    <span data-total-pares>{{ number_format($totalPares) }}</span> pares
                                </div>
                                <div class="text-base font-bold" data-total-pedidos>
                                    ${{ number_format($totalPedidos, 2) }}
                                </div>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
