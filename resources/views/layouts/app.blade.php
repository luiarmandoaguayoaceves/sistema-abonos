<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calzado NuevaEra</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-blue-900 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold tracking-wider flex items-center">
                        CALZADO 
                        <span class="bg-white text-blue-900 px-2 py-1 rounded mr-2">NUEVAERA</span>
                    </a>
                </div>

                <!-- Menú de Computadora (Escritorio) -->
                <div class="hidden md:flex items-center space-x-2">
                    <a href="{{ route('home') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        Inicio
                    </a>

                    <a href="{{ route('clientes.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('clientes.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        Clientes
                    </a>

                    <a href="{{ route('pedidos') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('pedidos') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        Nuevo Pedido
                    </a>

                    <a href="{{ route('seguimiento') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('seguimiento') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        Seguimiento
                    </a>

                    <a href="{{ route('abonos.index') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('abonos.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        Cobranza/Abonos
                    </a>

                    <a href="{{ route('facturas') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('facturas') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                        Facturación
                    </a>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="btn-menu" class="text-blue-100 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menú Móvil (Oculto por defecto) -->
        <div id="menu-movil" class="hidden md:hidden bg-blue-800 pb-3 pt-2 px-2 space-y-1 shadow-inner">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700' }}">
                Inicio
            </a>
            <a href="{{ route('clientes.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('clientes.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700' }}">
                Clientes
            </a>
            <a href="{{ route('pedidos') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('pedidos') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700' }}">
                Nuevo Pedido
            </a>
            <a href="{{ route('seguimiento') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('seguimiento') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700' }}">
                Seguimiento
            </a>
            <a href="{{ route('abonos.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('abonos.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700' }}">
                Cobranza/Abonos
            </a>
            <a href="{{ route('facturas') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('facturas') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700' }}">
                Facturación
            </a>
        </div>
    </nav>

    <div class="container mx-auto mt-6 md:mt-10 p-4 md:p-6 bg-white rounded shadow w-full max-w-full overflow-x-hidden">
        @yield('content')
    </div>

    <!-- Script para abrir y cerrar el menú móvil -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnMenu = document.getElementById('btn-menu');
            const menuMovil = document.getElementById('menu-movil');
            
            btnMenu.addEventListener('click', function() {
                menuMovil.classList.toggle('hidden');
            });
        });
    </script>
</body>

</html>
