@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-blue-900">📋 Seguimiento de Pedidos</h2>
            <a href="{{ route('pedidos') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                + Nuevo Pedido
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4">
            <input type="text" id="search" placeholder="Buscar por pedido..." class="border px-4 py-2 rounded w-full">
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-x-auto border">
            <table class="min-w-full divide-y divide-gray-200" id="pedidos-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">N. Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Modelos</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pedidos as $pedido)
                        <tr class="{{ $pedido->pagado ? 'bg-green-100 hover:bg-green-200' : 'bg-yellow-100 hover:bg-yellow-200' }} transition border-b border-white pedido-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">{{ $pedido->n_pedido ?? $pedido->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">{{ optional($pedido->fecha_entrega ?? $pedido->created_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-semibold text-gray-900">{{ $pedido->cliente }}</div></td>
                            <td class="px-6 py-4 whitespace-nowrap text-center"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-bold">{{ $pedido->detallesPedido->count() ?: count($pedido->detalles ?? []) }} items</span></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold {{ $pedido->pagado ? 'text-green-800' : 'text-yellow-800' }}">
                                ${{ number_format($pedido->total, 2) }}
                                <div class="mt-1"><span class="text-[10px] {{ $pedido->pagado ? 'bg-green-300 text-green-900' : 'bg-yellow-300 text-yellow-900' }} px-2 py-1 rounded-full uppercase">{{ $pedido->pagado ? '✅ Pagado' : '⏳ Pendiente' }}</span></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button onclick="verDetalle({{ $pedido->id }})" class="text-blue-600 hover:text-blue-900 font-medium text-sm">Ver Detalles</button>
                            </td>
                        </tr>

                        <tr id="detalle-{{ $pedido->id }}" class="hidden bg-gray-50 detalle-row">
                            <td colspan="6" class="px-10 py-6">
                                <div class="border rounded-lg bg-white p-4 shadow-inner">
                                    <!-- Formulario de Edición -->
                                    <form action="{{ route('pedidos.updateFecha', $pedido->id) }}" method="POST" class="mb-6 pb-4 border-b flex items-center gap-4">
                                        @csrf @method('PUT')
                                        <label class="text-xs font-bold uppercase text-gray-600">Cambiar fecha:</label>
                                        <input type="datetime-local" name="fecha_entrega" value="{{ optional($pedido->fecha_entrega ?? $pedido->created_at)->format('Y-m-d\TH:i') }}" class="border rounded px-2 py-1 text-sm">
                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">Actualizar</button>
                                    </form>

                                    <p class="text-xs font-bold uppercase text-blue-600 mb-3 flex items-center"><span class="mr-2">👟</span> Desglose de Productos</p>
                                    <!-- (Tu tabla de detalles original sigue aquí) -->
                                    <table class="w-full text-sm text-left">
                                        <thead class="text-xs text-gray-400 uppercase border-b">
                                            <tr><th class="pb-2">Modelo / Color</th><th class="pb-2 text-center">Pares</th><th class="pb-2 text-right">Precio</th><th class="pb-2 text-right">Subtotal</th></tr>
                                        </thead>
                                        <tbody>
                                            @if ($pedido->detallesPedido->isNotEmpty())
                                                @foreach ($pedido->detallesPedido as $detalle)
                                                    <tr>
                                                        <td class="py-2">
                                                            <span class="font-semibold text-gray-800">{{ $detalle->modelo }}</span>
                                                            <span class="text-gray-500 text-xs">({{ $detalle->color }})</span>
                                                        </td>
                                                        <td class="py-2 text-center text-gray-700">{{ $detalle->pares }}</td>
                                                        <td class="py-2 text-right text-gray-600">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                                        <td class="py-2 text-right font-medium text-gray-900">${{ number_format($detalle->subtotal, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                @foreach (($pedido->detalles ?? []) as $item)
                                                    <tr>
                                                        <td class="py-2">
                                                            <span class="font-semibold text-gray-800">{{ $item['modelo'] ?? 'Sin modelo' }}</span>
                                                            <span class="text-gray-500 text-xs">({{ $item['color'] ?? 'Sin color' }})</span>
                                                        </td>
                                                        <td class="py-2 text-center text-gray-700">{{ $item['pares'] ?? 0 }}</td>
                                                        <td class="py-2 text-right text-gray-600">${{ number_format($item['precio'] ?? 0, 2) }}</td>
                                                        <td class="py-2 text-right font-medium text-gray-900">${{ number_format($item['subtotalItem'] ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">No hay pedidos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function verDetalle(id) {
            const fila = document.getElementById(`detalle-${id}`);
            fila.classList.toggle('hidden');
        }

        document.getElementById('search').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.pedido-row');
            
            rows.forEach((row) => {
                const idPedido = row.cells[0].textContent.toLowerCase();
                const visible = idPedido.includes(query);
                row.style.display = visible ? '' : 'none';
                
                // Ocultar fila de detalle si la principal no coincide
                const id = row.nextElementSibling.id;
                if (!visible) {
                    document.getElementById(id).classList.add('hidden');
                }
            });
        });
    </script>
@endsection