@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <h2 class="text-3xl font-bold text-blue-900 mb-6 italic">📑 Gestión de Facturación</h2>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md border mb-8">
        <h3 class="text-lg font-bold mb-4">Subir Nueva Factura</h3>
        <form action="{{ route('facturas.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Seleccionar Pedido</label>
                <select name="pedido_id" required class="w-full border rounded p-2">
                    <option value="">Seleccionar Pedido...</option>
                    @foreach($pedidosSinFactura as $p)
                        <option value="{{ $p->id }}">#{{ $p->id }} - {{ $p->cliente }} (${{ number_format($p->total, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Folio de Factura</label>
                <input type="text" name="folio_factura" required class="w-full border rounded p-2" placeholder="Ej: A-1050">
            </div>
            <div>
                <label class="block text-sm font-medium">Archivo PDF</label>
                <input type="file" name="archivo_pdf" accept=".pdf" required class="w-full text-sm">
            </div>
            <div class="w-full">
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded font-bold hover:bg-green-700">Vincular Factura</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md border overflow-x-auto">
        <table class="w-full min-w-[700px] text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">ID Pedido</th>
                    <th class="p-3 border">Cliente</th>
                    <th class="p-3 border">Folio Factura</th>
                    <th class="p-3 border text-center">Archivo</th>
                    <th class="p-3 border text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidosConFactura as $pedido)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 border">#{{ $pedido->id }}</td>
                    <td class="p-3 border">{{ $pedido->cliente }}</td>
                    <td class="p-3 border font-mono font-bold text-blue-700">{{ $pedido->folio_factura }}</td>
                    <td class="p-3 border text-center">
                        <a href="{{ asset('storage/facturas/' . $pedido->pdf_factura) }}" target="_blank" class="text-red-600 hover:underline flex items-center justify-center">
                            <span class="mr-1">PDF</span> 📥
                        </a>
                    </td>
                    <td class="p-3 border text-center">
                        <form action="{{ route('facturas.destroy', $pedido->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta factura para subir otra?')">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200">Eliminar Error</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500 italic">No hay facturas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection