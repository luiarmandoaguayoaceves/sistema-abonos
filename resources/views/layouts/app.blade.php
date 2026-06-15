<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calzado NuevaEra</title>

    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Sistema Abonos">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:bg-red-700 hover:text-white"
                        >
                            Salir
                        </button>
                    </form>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="btn-menu" type="button" class="text-blue-100 hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

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
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-blue-100 hover:bg-red-700 hover:text-white"
                >
                    Salir
                </button>
            </form>
        </div>
    </nav>
    <div
        id="pwa-install-banner"
        class="fixed inset-x-4 bottom-4 z-50 hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-2xl sm:left-auto sm:right-6 sm:w-96"
    >
        <div class="flex items-start gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-900 text-white">
                📱
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-900">
                    Instalar Sistema Abonos
                </p>
                <p class="mt-1 text-xs text-slate-500">
                    Abre el sistema como app, sin barra de búsqueda del navegador.
                </p>

                <div class="mt-3 flex gap-2">
                    <button
                        id="pwa-install-button"
                        type="button"
                        class="rounded-xl bg-blue-900 px-4 py-2 text-xs font-bold text-white transition hover:bg-blue-800"
                    >
                        Instalar
                    </button>

                    <button
                        id="pwa-install-close"
                        type="button"
                        class="rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-200"
                    >
                        Después
                    </button>
                </div>
            </div>
        </div>
    </div>

    <main class="container mx-auto mt-6 md:mt-10 p-4 md:p-6 bg-white rounded shadow w-full max-w-full overflow-x-hidden">
        @yield('content')
    </main>
</body>

</html>