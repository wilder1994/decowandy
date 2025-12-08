<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-b from-emerald-900/90 via-emerald-800/85 to-slate-900/85 min-h-screen">
        <div class="min-h-screen flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-4xl">
                <div class="text-center mb-8">
                    <a href="/" class="inline-flex flex-col items-center gap-2">
                        <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center ring-1 ring-white/20 shadow-lg">
                            <span class="text-white font-extrabold text-lg">DW</span>
                        </div>
                        <div class="text-white">
                            <p class="text-sm uppercase tracking-[0.25em] text-white/80">DecoWandy</p>
                            <p class="text-2xl font-black leading-tight">Panel Administrativo</p>
                        </div>
                    </a>
                </div>
                <div class="bg-white/95 backdrop-blur-sm shadow-2xl ring-1 ring-slate-200 rounded-2xl overflow-hidden">
                    <div class="grid md:grid-cols-3">
                        <div class="hidden md:block bg-gradient-to-br from-emerald-800 via-emerald-700 to-emerald-600 text-white p-8">
                            <p class="text-sm uppercase tracking-[0.25em] text-white/70">Acceso seguro</p>
                            <h2 class="text-2xl font-bold mt-2">Bienvenido a DecoWandy</h2>
                            <p class="text-sm text-white/80 mt-3 leading-relaxed">Administra ventas, clientes e inventario con una interfaz limpia y consistente.</p>
                        </div>
                        <div class="md:col-span-2 p-6 md:p-8">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
