@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-blue-900 mb-6 border-b pb-2">📦 Elaboración de Pedidos - Calzado NuevaEra</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="md:col-span-1 bg-white p-6 rounded-lg border shadow-sm h-fit">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Agregar Producto</h3>
                <form id="formProducto" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex flex-col space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Modelo</label>
                            <input type="text" id="modelo" placeholder="Ej. Bota-01" required
                                class="w-full border-gray-300 rounded-md p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Color</label>
                            <input type="text" id="color" required
                                class="w-full border-gray-300 rounded-md p-2 border">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pares</label>
                            <input type="number" id="pares" min="1" required
                                class="w-full border-gray-300 rounded-md p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Precio Unit.</label>
                            <input type="number" id="precio" step="0.01" min="0" required
                                class="w-full border-gray-300 rounded-md p-2 border">
                        </div>
                    </div>

                    <div class="flex space-x-2 pt-4">
                        <button type="button" onclick="agregarALaTabla()"
                            class="flex-1 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 font-bold transition">
                            Añadir al Pedido →
                        </button>
                    </div>
                </form>
            </div>

            <div class="md:col-span-2 space-y-6">
                <div class="bg-white p-6 rounded-lg border shadow-sm">

                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="grid grid-cols-5 gap-4 items-end">
                            <div class="col-span-4">
                                <label class="block text-sm font-bold text-blue-800 uppercase mb-1">Nombre del Cliente / Razón Social</label>
                                <select id="cliente_global" class="w-full text-lg font-semibold border-gray-300 rounded-md p-2 border focus:ring-2 focus:ring-blue-500">
                                    <option value="">Buscar y seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->nombre }}">{{ $cliente->numero_cliente }} - {{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-blue-800 uppercase mb-1">N. Pedido</label>
                                <input type="text" id="pedido" placeholder="Opcional"
                                    class="w-full text-lg font-semibold border-gray-300 rounded-md p-2 border focus:ring-2 focus:ring-blue-500"
                                    oninput="actualizarInterfaz()">
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Resumen del Pedido</h3>
                    <table class="w-full text-left border-collapse mb-4">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 uppercase text-xs">
                                <th class="p-2 border">Modelo</th>
                                <th class="p-2 border">Color</th>
                                <th class="p-2 border text-center">Pares</th>
                                <th class="p-2 border text-right">Subtotal</th>
                                <th class="p-2 border text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTabla">
                        </tbody>
                    </table>

                    <div class="mt-4 border-t pt-4 flex justify-between items-end">
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded border">
                            <input type="checkbox" id="aplicarIvaGlobal" onchange="actualizarInterfaz()"
                                class="h-5 w-5 text-blue-600 border-gray-300 rounded cursor-pointer">
                            <label for="aplicarIvaGlobal" class="text-sm font-bold text-gray-700 cursor-pointer">
                                ¿Aplicar IVA (16%) a todo el pedido?
                            </label>
                        </div>

                        <div class="text-right space-y-1">
                            <p class="text-gray-600">Subtotal: <span id="resumenSubtotal"
                                    class="font-bold text-gray-800">$0.00</span></p>
                            <p class="text-gray-600">IVA (16%): <span id="resumenIva"
                                    class="font-bold text-gray-800">$0.00</span></p>
                            <p class="text-2xl font-bold text-green-700">Total: <span id="resumenTotal">$0.00</span></p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('pedidos.store') }}" method="POST" id="formFinal">
                    @csrf
                    <input type="hidden" name="cliente" id="hiddenCliente">
                    <input type="hidden" name="datos_pedido" id="inputHiddenDatos">
                    <input type="hidden" name="iva_aplicado" id="hiddenIvaStatus">
                    <input type="hidden" name="pedido" id="hiddenPedido">

                    <button type="submit"
                        class="w-full bg-green-600 text-white py-4 rounded-lg text-xl font-bold shadow-lg hover:bg-green-700 transition transform hover:scale-[1.01]">
                        💾 Confirmar y Guardar Pedido Completo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let itemsPedido = [];

        function agregarALaTabla() {
            const modelo = document.getElementById('modelo').value;
            const color = document.getElementById('color').value;
            const pares = parseInt(document.getElementById('pares').value);
            const precio = parseFloat(document.getElementById('precio').value);

            // Validación de campos de producto
            if (!modelo || !color || isNaN(pares) || isNaN(precio)) {
                alert("Por favor, completa todos los datos del calzado.");
                return;
            }

            const subtotalItem = pares * precio;
            itemsPedido.push({
                modelo,
                color,
                pares,
                precio,
                subtotalItem
            });

            // Reseteamos el formulario del producto sin tocar el número de pedido
            document.getElementById('formProducto').reset();
            actualizarInterfaz();
        }

        function eliminarItem(index) {
            itemsPedido.splice(index, 1);
            actualizarInterfaz();
        }

        function actualizarInterfaz() {
            const tbody = document.getElementById('cuerpoTabla');
            const aplicarIva = document.getElementById('aplicarIvaGlobal').checked;
            const nombreCliente = document.getElementById('cliente_global').value;
            const pedido = document.getElementById('pedido').value;

            tbody.innerHTML = "";
            let subtotalAcumulado = 0;

            itemsPedido.forEach((item, index) => {
                subtotalAcumulado += item.subtotalItem;
                tbody.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="p-2 border">${item.modelo}</td>
                    <td class="p-2 border">${item.color}</td>
                    <td class="p-2 border text-center">${item.pares}</td>
                    <td class="p-2 border text-right font-semibold">$${item.subtotalItem.toFixed(2)}</td>
                    <td class="p-2 border text-center">
                        <button onclick="eliminarItem(${index})" class="text-red-500 hover:text-red-700 text-sm">Quitar</button>
                    </td>
                </tr>`;
            });

            let montoIva = aplicarIva ? (subtotalAcumulado * 0.16) : 0;
            let totalFinal = subtotalAcumulado + montoIva;

            document.getElementById('resumenSubtotal').innerText = `$${subtotalAcumulado.toFixed(2)}`;
            document.getElementById('resumenIva').innerText = `$${montoIva.toFixed(2)}`;
            document.getElementById('resumenTotal').innerText = `$${totalFinal.toFixed(2)}`;

            // Asignar valores a los inputs ocultos para el envío al servidor
            document.getElementById('hiddenCliente').value = nombreCliente;
            document.getElementById('hiddenPedido').value = pedido;
            document.getElementById('inputHiddenDatos').value = JSON.stringify(itemsPedido);
            document.getElementById('hiddenIvaStatus').value = aplicarIva ? 1 : 0;
        }

        // Modifica tu función de validación final así:
        document.getElementById('formFinal').onsubmit = function() {
            // REFUERZO: Capturar el nombre justo antes de enviar
            const clienteReal = document.getElementById('cliente_global').value;
            const pedidoReal = document.getElementById('pedido').value;
            document.getElementById('hiddenCliente').value = clienteReal;
            document.getElementById('hiddenPedido').value = pedidoReal;

            if (!clienteReal.trim()) {
                alert("¡Error! Debes escribir el nombre del cliente antes de guardar.");
                return false;
            }
            if (itemsPedido.length === 0) {
                alert("El pedido está vacío. Agrega al menos un modelo de calzado.");
                return false;
            }
            return true;
        };

        function sincronizarCliente() {
            const nombre = document.getElementById('cliente_global').value;
            document.getElementById('hiddenCliente').value = nombre;
            actualizarInterfaz();
        }
    </script>

    <!-- Scripts para Select con Buscador (Select2) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#cliente_global').select2({
                placeholder: "Busca un cliente por nombre o número...",
                allowClear: true,
                width: '100%'
            }).on('change', sincronizarCliente);
        });
    </script>
@endsection
