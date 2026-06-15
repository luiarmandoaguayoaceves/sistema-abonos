@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- Encabezado --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Clientes
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Administra los clientes registrados, su frecuencia de pago y datos de contacto.
            </p>
        </div>

        <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700">
            Total visibles: {{ $clientes->count() }}
        </div>
    </div>

    {{-- Mensajes --}}
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Revisa los campos del formulario:</p>
            <ul class="mt-2 list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">
                Registrar nuevo cliente
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Captura solo la información necesaria para identificar y dar seguimiento al cliente.
            </p>
        </div>

        <form action="{{ route('clientes.store') }}" method="POST" class="grid grid-cols-1 gap-5 p-6 md:grid-cols-4">
            @csrf

            <div>
                <label for="cve_cliente" class="block text-sm font-medium text-slate-700">
                    Nº Cliente
                </label>
                <input
                    id="cve_cliente"
                    type="number"
                    name="cve_cliente"
                    value="{{ old('cve_cliente') }}"
                    placeholder="Ej. 1001"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                @error('cve_cliente')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="nombre" class="block text-sm font-medium text-slate-700">
                    Nombre completo <span class="text-red-500">*</span>
                </label>
                <input
                    id="nombre"
                    type="text"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    required
                    placeholder="Nombre del cliente"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                @error('nombre')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="marca" class="block text-sm font-medium text-slate-700">
                    Marca
                </label>
                <input
                    id="marca"
                    type="text"
                    name="marca"
                    value="{{ old('marca') }}"
                    placeholder="Ej. Marca comercial"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                @error('marca')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="frecuencia_pago" class="block text-sm font-medium text-slate-700">
                    Frecuencia de pago <span class="text-red-500">*</span>
                </label>
                <select
                    id="frecuencia_pago"
                    name="frecuencia_pago"
                    required
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                    <option value="">Selecciona una opción</option>
                    <option value="Semanal" @selected(old('frecuencia_pago') === 'Semanal')>Semanal</option>
                    <option value="Quincenal" @selected(old('frecuencia_pago') === 'Quincenal')>Quincenal</option>
                    <option value="Mensual" @selected(old('frecuencia_pago') === 'Mensual')>Mensual</option>
                </select>
                @error('frecuencia_pago')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="telefono" class="block text-sm font-medium text-slate-700">
                    Teléfono
                </label>
                <input
                    id="telefono"
                    type="text"
                    name="telefono"
                    value="{{ old('telefono') }}"
                    maxlength="30"
                    placeholder="Ej. 3312345678"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                @error('telefono')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="correo" class="block text-sm font-medium text-slate-700">
                    Correo
                </label>
                <input
                    id="correo"
                    type="email"
                    name="correo"
                    value="{{ old('correo') }}"
                    placeholder="cliente@correo.com"
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >
                @error('correo')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-4">
                <label for="notas" class="block text-sm font-medium text-slate-700">
                    Notas
                </label>
                <textarea
                    id="notas"
                    name="notas"
                    rows="3"
                    placeholder="Ej. Cliente de Expo Zacatecas, referencia interna, observaciones, etc."
                    class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                >{{ old('notas') }}</textarea>
                @error('notas')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-4 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
                >
                    Guardar cliente
                </button>
            </div>
        </form>
    </section>

    {{-- Tabla --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-900">
                Clientes registrados
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Listado paginado de clientes dados de alta.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Nº Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Marca</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Frecuencia</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Notas</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse ($clientes as $cliente)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-900">
                                {{ $cliente->cve_cliente ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-700">
                                <div class="font-medium text-slate-900">
                                    {{ $cliente->nombre }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ $cliente->marca ?? '—' }}
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                    {{ $cliente->frecuencia_pago }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-700">
                                <div>{{ $cliente->telefono ?? '—' }}</div>
                                @if ($cliente->correo)
                                    <div class="text-xs text-slate-500">{{ $cliente->correo }}</div>
                                @endif
                            </td>

                            <td class="max-w-xs px-6 py-4 text-sm text-slate-500">
                                <span class="line-clamp-2">
                                    {{ $cliente->notas ?? '—' }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                <form
                                    action="{{ route('clientes.destroy', $cliente->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar cliente? Esta acción no se puede deshacer.')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-200"
                                    >
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-slate-500">
                                Todavía no hay clientes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $clientes->links() }}
        </div>
    </section>
</div>
@endsection