<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - Sistema Abonos</title>

    <meta name="theme-color" content="#0f172a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-xl">
            <div class="bg-blue-900 px-8 py-8 text-white">
                <p class="text-sm font-semibold uppercase tracking-widest text-blue-200">
                    Calzado NuevaEra
                </p>
                <h1 class="mt-2 text-2xl font-bold">
                    Iniciar sesión
                </h1>
                <p class="mt-2 text-sm text-blue-100">
                    Acceso protegido para información interna del sistema.
                </p>
            </div>

            <form action="{{ route('login.store') }}" method="POST" class="space-y-5 px-8 py-8">
                @csrf

                @if ($errors->any())
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        Usuario / correo
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="admin@tudominio.com"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-900 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="mt-1 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-none transition focus:border-blue-900 focus:ring-2 focus:ring-blue-100"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="rounded border-slate-300 text-blue-900 focus:ring-blue-900"
                    >
                    Mantener sesión iniciada
                </label>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-blue-900 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800"
                >
                    Entrar al sistema
                </button>
            </form>
        </section>
    </main>
</body>
</html>