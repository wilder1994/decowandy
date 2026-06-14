<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'DecoWandy') }}</title>
        @include('partials.dw-head-assets')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased">
        <div class="flex min-h-screen items-center justify-center px-4 py-8" style="background: linear-gradient(145deg, #2D2A32 0%, #4A3F5C 45%, #6B5A82 100%);">
            <div class="w-full max-w-4xl">
                <div class="mb-6 text-center">
                    <a href="/" class="inline-flex flex-col items-center gap-2">
                        <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="h-12 w-auto">
                        <div class="text-white">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">DecoWandy</p>
                            <p class="font-display text-xl font-bold">Panel Administrativo</p>
                        </div>
                    </a>
                </div>
                <div class="overflow-hidden rounded-dw-lg bg-dw-card shadow-dw-neon dw-hairline-neon">
                    <div class="grid md:grid-cols-3">
                        <div class="hidden p-6 text-white md:block brand-gradient">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Acceso seguro</p>
                            <h2 class="mt-2 font-display text-xl font-bold">Bienvenido</h2>
                            <p class="mt-2 text-sm leading-relaxed text-white/85">Administra ventas, clientes e inventario con una interfaz unificada.</p>
                        </div>
                        <div class="p-5 md:col-span-2 md:p-6">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
