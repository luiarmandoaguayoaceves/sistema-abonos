@extends('layouts.app')

@section('content')
<div data-facturas-page class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Gestión de facturación
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Vincula archivos PDF de factura solo para pedidos del cliente Calzado vel.
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

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">
                Vincular nueva factura
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Solo aparecen pedidos de Calzado vel que todavía no tienen PDF de factura registrado.
            </p>
        </div>

        <form
            action="{{ route('facturas.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="grid grid-cols-1 gap-5 p-6 lg:grid-cols-4 lg:items-end"
        >
            @csrf

            <div class="lg:col-span-2">
                <label for="pedido_id" class="block text-sm font-medium text-slate-700">
                    Pedido <span class="text-red-500">*</span>
                </label>

                <select
                    id="pedido_id"
                    name="pedido_id"
                    required
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                    <option value="">Seleccionar pedido...</option>
                    @foreach($pedidosSinFactura as $pedido)
                        <option value="{{ $pedido->id }}" @selected(old('pedido_id') == $pedido->id)>
                            #{{ $pedido->n_pedido ?? $pedido->id }} - {{ $pedido->cliente }} - ${{ number_format($pedido->total, 2) }}
                        </option>
                    @endforeach
                </select>

                @if ($pedidosSinFactura->isEmpty())
                    <p class="mt-2 text-xs text-amber-600">
                        No hay pedidos pendientes de facturar.
                    </p>
                @endif
            </div>

            <div>
                <label for="folio_factura" class="block text-sm font-medium text-slate-700">
                    Folio factura <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="folio_factura"
                    name="folio_factura"
                    value="{{ old('folio_factura') }}"
                    required
                    maxlength="100"
                    placeholder="Ej. A-1050"
                    autocomplete="off"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
            </div>

            <div>
                <label for="archivo_pdf" class="block text-sm font-medium text-slate-700">
                    Archivo PDF <span class="text-red-500">*</span>
                </label>

                <input
                    type="file"
                    id="archivo_pdf"
                    name="archivo_pdf"
                    accept="application/pdf,.pdf"
                    required
                    data-pdf-input
                    class="mt-1 block w-full cursor-pointer rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-3 file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                >

                <p data-pdf-name class="mt-2 truncate text-xs text-slate-500">
                    Ningún archivo seleccionado
                </p>
            </div>

            <div class="lg:col-span-4">
                <button
                    type="submit"
                    @disabled($pedidosSinFactura->isEmpty())
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                >
                    Vincular factura
                </button>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        Facturas registradas
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Total: {{ $pedidosConFactura->count() }} facturas vinculadas.
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Pedido
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Folio
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Archivo
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($pedidosConFactura as $pedido)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900">
                                #{{ $pedido->n_pedido ?? $pedido->id }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                {{ $pedido->cliente }}
                            </td>

                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-sm font-semibold text-blue-700">
                                    {{ $pedido->folio_factura }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <a
                                    href="{{ asset('storage/facturas/' . $pedido->pdf_factura) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700"
                                >
                                    Ver PDF
                                </a>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <form
                                    action="{{ route('facturas.destroy', $pedido->id) }}"
                                    method="POST"
                                    data-confirm-delete
                                    class="inline-block"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-lg px-3 py-1.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 hover:text-red-700"
                                    >
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                No hay facturas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
