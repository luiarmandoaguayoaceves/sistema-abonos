@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-blue-900 mb-6 italic">💰 Gestión de Abonos y Saldos</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md border h-fit">
            <h3 class="text-lg font-bold mb-4">Registrar Nuevo Abono</h3>
            <form action="{{ route('abonos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium">Seleccionar Pedido Pendiente</label>
                    <select name="pedido_id" required class="w-full border rounded p-2">
                        @foreach($pedidosPendientes as $p)
                            <option value="{{ $p->id }}">#{{ $p->id }} - {{ $p->cliente }} (Falta: ${{ number_format($p->saldoPendiente(), 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Monto del Abono</label>
                    <input type="number" name="monto" step="0.01" required class="w-full border rounded p-2">
                </div>
                <div>
                    <label class="block text-sm font-medium">Método</label>
                    <select name="metodo_pago" class="w-full border rounded p-2">
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Fecha de Pago</label>
                    <input type="date" name="fecha_pago" value="{{ date('Y-m-d') }}" class="w-full border rounded p-2">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">Guardar Abono</button>
            </form>
        </div>

        <div class="lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h3 id="titulo-lista" class="text-xl font-bold text-gray-800">Pendientes de Pago</h3>
                <button type="button" onclick="toggleVistas()" id="btn-toggle" class="bg-gray-200 text-gray-800 px-4 py-2 rounded font-bold hover:bg-gray-300 shadow transition">
                    Ver Historial Pagados 🗃️
                </button>
            </div>

            <!-- Lista Pendientes (pagado = false) -->
            <div id="lista-pendientes" class="space-y-4">
                @forelse($pedidosPendientes as $p)
                <div class="bg-white p-4 rounded-lg shadow border border-l-4 border-l-orange-400">
                    <div class="flex justify-between items-start border-b pb-2 mb-2">
                        <div>
                            <h4 class="font-bold text-lg">Pedido #{{ $p->id }} - {{ $p->cliente }}</h4>
                            <p class="text-xs text-gray-500 text-uppercase italic">Marca: {{ $p->cliente_relacion->marca ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total: ${{ number_format($p->total, 2) }}</p>
                            <p class="text-lg font-bold text-red-600">Resta: ${{ number_format($p->saldoPendiente(), 2) }}</p>
                        </div>
                    </div>

                    <div class="text-xs space-y-1">
                        <p class="font-bold">Historial de Abonos:</p>
                        @forelse($p->abonos as $abono)
                            <div class="flex justify-between bg-gray-50 p-1 rounded">
                                <span>{{ date('d/m/y', strtotime($abono->fecha_pago)) }} - {{ $abono->metodo_pago }}</span>
                                <span class="font-bold text-green-700">+ ${{ number_format($abono->monto, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-gray-400 italic">No hay abonos registrados.</p>
                        @endforelse
                    </div>

                    @if($p->saldoPendiente() <= 0)
                        <form action="{{ route('abonos.pagado', $p->id) }}" method="POST" class="mt-4">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-full bg-green-500 text-white font-bold py-2 rounded hover:bg-green-600 shadow animate-bounce">
                                ✅ MARCAR COMO PAGADO TOTALMENTE
                            </button>
                        </form>
                    @endif
                </div>
                @empty
                    <div class="bg-white p-6 text-center text-gray-500 rounded-lg border border-dashed">
                        No hay pedidos pendientes de pago.
                    </div>
                @endforelse
            </div>

            <!-- Lista Pagados (pagado = true) -->
            <div id="lista-pagados" class="space-y-4 hidden">
                @forelse($pedidosPagados as $p)
                <div class="bg-gray-50 p-4 rounded-lg shadow border border-l-4 border-l-green-500 opacity-90">
                    <div class="flex justify-between items-start border-b pb-2 mb-2">
                        <div>
                            <h4 class="font-bold text-lg text-gray-700">Pedido #{{ $p->id }} - {{ $p->cliente }}</h4>
                            <p class="text-xs text-gray-500 text-uppercase italic">Marca: {{ $p->cliente_relacion->marca ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Total pagado:</p>
                            <p class="text-lg font-bold text-green-700">${{ number_format($p->total, 2) }}</p>
                        </div>
                    </div>

                    <div class="text-xs space-y-1">
                        <p class="font-bold text-gray-600">Historial de Abonos:</p>
                        @foreach($p->abonos as $abono)
                            <div class="flex justify-between bg-white p-1 rounded border">
                                <span class="text-gray-600">{{ date('d/m/y', strtotime($abono->fecha_pago)) }} - {{ $abono->metodo_pago }}</span>
                                <span class="font-bold text-green-700">+ ${{ number_format($abono->monto, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 bg-green-200 text-green-800 text-center font-bold py-2 rounded text-sm">
                        ✅ LIQUIDADO TOTALMENTE
                    </div>
                </div>
                @empty
                    <div class="bg-white p-6 text-center text-gray-500 rounded-lg border border-dashed">
                        Aún no hay pedidos pagados en el historial.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function toggleVistas() {
        const pendientes = document.getElementById('lista-pendientes');
        const pagados = document.getElementById('lista-pagados');
        const btn = document.getElementById('btn-toggle');
        const titulo = document.getElementById('titulo-lista');

        if (pendientes.classList.contains('hidden')) {
            // Mostrar Pendientes
            pendientes.classList.remove('hidden');
            pagados.classList.add('hidden');
            titulo.innerText = "Pendientes de Pago";
            btn.innerHTML = "Ver Historial Pagados 🗃️";
            btn.className = "bg-gray-200 text-gray-800 px-4 py-2 rounded font-bold hover:bg-gray-300 shadow transition";
        } else {
            // Mostrar Pagados
            pendientes.classList.add('hidden');
            pagados.classList.remove('hidden');
            titulo.innerText = "Historial de Pagados";
            btn.innerHTML = "Ver Pendientes ⏳";
            btn.className = "bg-blue-200 text-blue-800 px-4 py-2 rounded font-bold hover:bg-blue-300 shadow transition";
        }
    }
</script>
@endsection