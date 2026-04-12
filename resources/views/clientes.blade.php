@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="bg-white p-6 rounded-lg shadow-md border">
        <h2 class="text-2xl font-bold text-blue-900 mb-4 italic">➕ Registrar Nuevo Cliente</h2>
        <form action="{{ route('clientes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium">Nº Cliente</label>
                <input type="text" name="numero_cliente" required placeholder="Ej. 001" class="w-full border rounded p-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Nombre Completo</label>
                <input type="text" name="nombre" required class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Marca</label>
                <input type="text" name="marca" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Frecuencia de Pago</label>
                <select name="frecuencia_pago" class="w-full border rounded p-2">
                    <option value="Semanal">Semanal</option>
                    <option value="Quincenal">Quincenal</option>
                    <option value="Mensual">Mensual</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Notas (Identificador: Expo, Estado, etc.)</label>
                <input type="text" name="notas" placeholder="Ej: Cliente de la Expo Zacatecas" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium">Teléfono</label>
                <input type="text" name="telefono" maxlength="10" class="w-full border rounded p-2">
            </div>
            <div class="md:col-span-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">Guardar Cliente</button>
            </div>
        </form>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md border">
        <table id="tablaClientes" class="display w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th>Nº Cliente</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Frecuencia</th>
                    <th>Teléfono</th>
                    <th>Notas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                <tr>
                    <td class="font-bold text-blue-800">{{ $cliente->numero_cliente }}</td>
                    <td>{{ $cliente->nombre }}</td>
                    <td>{{ $cliente->marca }}</td>
                    <td>{{ $cliente->frecuencia_pago }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td><span class="text-xs italic text-gray-500">{{ $cliente->notas }}</span></td>
                    <td class="flex space-x-2">
                        <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" onsubmit="return confirm('¿Eliminar cliente?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tablaClientes').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });
    });
</script>
@endsection