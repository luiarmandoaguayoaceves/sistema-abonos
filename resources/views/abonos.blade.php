@extends('layouts.app')

@section('content')
<div data-abonos-page class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Gestión de abonos y saldos
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Registra pagos parciales, consulta saldos pendientes y controla pedidos liquidados.
            </p>
        </div>

        <a
            href="{{ route('seguimiento') }}"
            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
        >
            Ver seguimiento
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        <section class="h-fit overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-slate-900">
                    Registrar nuevo abono
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Selecciona un pedido pendiente y captura el pago recibido.
                </p>
            </div>

            <form action="{{ route('abonos.store') }}" method="POST" class="space-y-5 p-6">
                @csrf

                <div>
                    <label for="pedidoSearch" class="block text-sm font-medium text-slate-700">
                        Buscar pedido
                    </label>
                    <input
                        type="text"
                        id="pedidoSearch"
                        data-pedido-search
                        placeholder="Buscar por pedido o cliente..."
                        autocomplete="off"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                </div>

                <div>
                    <label for="pedido_id" class="block text-sm font-medium text-slate-700">
                        Pedido pendiente <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="pedido_id"
                        name="pedido_id"
                        required
                        data-pedido-select
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                        <option value="">Seleccionar pedido...</option>
                        @foreach($pedidosPendientes as $pedido)
                            <option value="{{ $pedido->id }}" @selected(old('pedido_id') == $pedido->id)>
                                #{{ $pedido->n_pedido ?? $pedido->id }} - {{ $pedido->cliente }} - Falta ${{ number_format($pedido->saldoPendiente(), 2) }}
                            </option>
                        @endforeach
                    </select>

                    @if ($pedidosPendientes->isEmpty())
                        <p class="mt-2 text-xs text-amber-600">
                            No hay pedidos pendientes de pago.
                        </p>
                    @endif
                </div>

                <div>
                    <label for="monto" class="block text-sm font-medium text-slate-700">
                        Monto del abono <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="number"
                        id="monto"
                        name="monto"
                        value="{{ old('monto') }}"
                        step="0.01"
                        min="0.01"
                        required
                        placeholder="0.00"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                </div>

                <div>
                    <label for="metodo_pago" class="block text-sm font-medium text-slate-700">
                        Método de pago <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="metodo_pago"
                        name="metodo_pago"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                        <option value="Efectivo" @selected(old('metodo_pago') === 'Efectivo')>Efectivo</option>
                        <option value="Transferencia" @selected(old('metodo_pago') === 'Transferencia')>Transferencia</option>
                        <option value="Cheque" @selected(old('metodo_pago') === 'Cheque')>Cheque</option>
                    </select>
                </div>

                <div>
                    <label for="fecha_pago" class="block text-sm font-medium text-slate-700">
                        Fecha de pago <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        id="fecha_pago"
                        name="fecha_pago"
                        value="{{ old('fecha_pago', now()->format('Y-m-d')) }}"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                    >
                </div>

                <button
                    type="submit"
                    @disabled($pedidosPendientes->isEmpty())
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                >
                    Guardar abono
                </button>
            </form>
        </section>

        <section class="space-y-5 lg:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 data-abonos-title class="text-lg font-semibold text-slate-900">
                            Pendientes de pago
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Pendientes: {{ $pedidosPendientes->count() }} · Pagados: {{ $pedidosPagados->count() }}
                        </p>
                    </div>

                    <div class="grid w-full grid-cols-1 gap-3 md:w-auto md:grid-cols-[240px_auto]">
                        <div>
                            <label for="clienteFilter" class="block text-sm font-medium text-slate-700">
                                Filtrar cliente
                            </label>

                            <select
                                id="clienteFilter"
                                data-cliente-filter
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                            >
                                <option value="">Todos los clientes</option>
                                @foreach ($pedidosPendientes->pluck('cliente')->merge($pedidosPagados->pluck('cliente'))->unique()->sort() as $cliente)
                                    <option value="{{ strtolower($cliente) }}">{{ $cliente }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button
                                type="button"
                                data-toggle-abonos-view
                                class="w-full rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-200"
                            >
                                Ver historial pagados
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div data-pendientes-list class="space-y-4">
                @forelse($pedidosPendientes as $pedido)
                    @php
                        $saldoPendiente = $pedido->saldoPendiente();
                        $totalAbonado = (float) $pedido->total - $saldoPendiente;
                    @endphp

                    <article
                        data-abono-card
                        data-client="{{ strtolower($pedido->cliente) }}"
                        class="overflow-hidden rounded-2xl border border-l-4 border-slate-200 border-l-amber-400 bg-white shadow-sm"
                    >
                        <div class="border-b border-slate-200 p-5">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">
                                        Pedido #{{ $pedido->n_pedido ?? $pedido->id }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $pedido->cliente }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-2 text-right sm:grid-cols-3 md:text-right">
                                    <div>
                                        <p class="text-xs uppercase text-slate-500">Total</p>
                                        <p class="font-semibold text-slate-900">${{ number_format($pedido->total, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-slate-500">Abonado</p>
                                        <p class="font-semibold text-emerald-700">${{ number_format($totalAbonado, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase text-slate-500">Saldo</p>
                                        <p class="font-bold text-amber-700">${{ number_format($saldoPendiente, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <h4 class="text-sm font-bold uppercase tracking-wide text-slate-700">
                                Historial de abonos
                            </h4>

                            <div class="mt-3 space-y-2">
                                @forelse($pedido->abonos as $abono)
                                    <div class="flex flex-col gap-1 rounded-xl bg-slate-50 px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                                        <span class="text-slate-600">
                                            {{ $abono->fecha_pago->format('d/m/Y') }} · {{ $abono->metodo_pago }}
                                        </span>
                                        <span class="font-bold text-emerald-700">
                                            + ${{ number_format($abono->monto, 2) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="rounded-xl border border-dashed border-slate-300 px-4 py-3 text-sm text-slate-500">
                                        No hay abonos registrados.
                                    </p>
                                @endforelse
                            </div>

                            @if ($saldoPendiente <= 0)
                                <form
                                    action="{{ route('abonos.pagado', $pedido->id) }}"
                                    method="POST"
                                    data-confirm-liquidar
                                    class="mt-5"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"
                                    >
                                        Marcar como pagado totalmente
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                        No hay pedidos pendientes de pago.
                    </div>
                @endforelse
            </div>

            <div data-pagados-list class="hidden space-y-4">
                @forelse($pedidosPagados as $pedido)
                    <article
                        data-abono-card
                        data-client="{{ strtolower($pedido->cliente) }}"
                        class="overflow-hidden rounded-2xl border border-l-4 border-slate-200 border-l-emerald-500 bg-white shadow-sm"
                    >
                        <div class="border-b border-slate-200 p-5">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-900">
                                        Pedido #{{ $pedido->n_pedido ?? $pedido->id }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $pedido->cliente }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    <p class="text-xs uppercase text-slate-500">Total pagado</p>
                                    <p class="text-lg font-bold text-emerald-700">${{ number_format($pedido->total, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <h4 class="text-sm font-bold uppercase tracking-wide text-slate-700">
                                Historial de abonos
                            </h4>

                            <div class="mt-3 space-y-2">
                                @forelse($pedido->abonos as $abono)
                                    <div class="flex flex-col gap-1 rounded-xl bg-slate-50 px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                                        <span class="text-slate-600">
                                            {{ $abono->fecha_pago->format('d/m/Y') }} · {{ $abono->metodo_pago }}
                                        </span>
                                        <span class="font-bold text-emerald-700">
                                            + ${{ number_format($abono->monto, 2) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="rounded-xl border border-dashed border-slate-300 px-4 py-3 text-sm text-slate-500">
                                        No hay abonos registrados.
                                    </p>
                                @endforelse
                            </div>

                            <div class="mt-5 rounded-xl bg-emerald-50 px-4 py-3 text-center text-sm font-bold text-emerald-700">
                                Liquidado totalmente
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                        Aún no hay pedidos pagados en el historial.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection