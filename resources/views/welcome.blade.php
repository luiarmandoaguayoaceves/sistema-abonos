@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-2 py-2 sm:px-4">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Dashboard general
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Resumen de clientes, ventas, abonos y saldos pendientes.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a
                href="{{ route('clientes.index') }}"
                class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200"
            >
                Ver clientes
            </a>

            <a
                href="{{ route('abonos.index') }}"
                class="rounded-xl bg-blue-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800"
            >
                Ir a cobranza
            </a>
        </div>
    </div>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total vendido</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">
                ${{ number_format($totalVendido, 2) }}
            </p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total abonado</p>
            <p class="mt-2 text-2xl font-bold text-emerald-700">
                ${{ number_format($totalAbonado, 2) }}
            </p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Saldo pendiente</p>
            <p class="mt-2 text-2xl font-bold text-red-700">
                ${{ number_format($saldoPendiente, 2) }}
            </p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Clientes con deuda</p>
            <p class="mt-2 text-2xl font-bold text-amber-700">
                {{ $clientesConDeuda }}
            </p>
        </article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-lg font-semibold text-slate-900">
                Clientes y saldo pendiente
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Ordenado de mayor a menor deuda.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Cliente</th>
                        <th class="px-4 py-3 text-left font-semibold text-slate-600">Marca</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Pedidos</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Total vendido</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Abonado</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Debe</th>
                        <th class="px-4 py-3 text-center font-semibold text-slate-600">Estatus</th>
                        <th class="px-4 py-3 text-right font-semibold text-slate-600">Acción</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($clientesDashboard as $item)
                        @php
                            $cliente = $item['cliente'];
                            $saldo = $item['saldo_pendiente'];
                        @endphp

                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">
                                    {{ $cliente->nombre }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    Clave: {{ $cliente->cve_cliente ?? 'N/A' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-slate-600">
                                {{ $cliente->marca ?? 'N/A' }}
                            </td>

                            <td class="px-4 py-3 text-right text-slate-700">
                                <div class="font-semibold">{{ $item['pedidos_total'] }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $item['pedidos_pendientes'] }} pendientes
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-slate-700">
                                ${{ number_format($item['total_vendido'], 2) }}
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-emerald-700">
                                ${{ number_format($item['total_abonado'], 2) }}
                            </td>

                            <td class="px-4 py-3 text-right font-bold {{ $saldo > 0 ? 'text-red-700' : 'text-emerald-700' }}">
                                ${{ number_format($saldo, 2) }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($saldo > 0)
                                    <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700">
                                        Debe
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                        Al corriente
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a
                                    href="{{ route('abonos.index') }}"
                                    class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100"
                                >
                                    Ver abonos
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                                No hay clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($pedidosSinClienteRegistrado->isNotEmpty())
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <h2 class="text-base font-bold text-amber-900">
                Pedidos con cliente no encontrado en catálogo
            </h2>
            <p class="mt-1 text-sm text-amber-800">
                Estos pedidos tienen nombre de cliente, pero no coinciden con la tabla de clientes.
                Conviene revisar el nombre del cliente para que el dashboard cuadre correctamente.
            </p>

            <div class="mt-4 space-y-2">
                @foreach ($pedidosSinClienteRegistrado as $pedido)
                    <div class="flex flex-col gap-1 rounded-xl bg-white px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                        <span class="font-semibold text-slate-800">
                            Pedido #{{ $pedido->n_pedido ?? $pedido->id }} · {{ $pedido->cliente }}
                        </span>
                        <span class="font-bold text-red-700">
                            Debe ${{ number_format(max($pedido->saldoPendiente(), 0), 2) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection