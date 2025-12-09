{{-- resources/views/layouts/admin.blade.php
    Layout del panel DecoWandy
    - Código/IDs en inglés
    - Textos visibles y comentarios en español
--}}
<!DOCTYPE html>
<html lang="es" class="h-full bg-[color:var(--dw-bg)]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Panel DecoWandy')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    {{-- Tipografías sugeridas --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@500;700&display=swap" rel="stylesheet">

    <style>
        :root{
            /* Paleta basada en el logo: morado + lila + rosa suave + amarillo pastel */
            --dw-primary: #A98AD4;  /* Morado notorio (principal para acentos) */
            --dw-lilac:   #C4A1E0;  /* Lila sutil (fondos suaves / bordes) */
            --dw-rose:    #F8A3C9;  /* Rosa suave (detalles y badges) */
            --dw-yellow:  #F7D87B;  /* Amarillo pastel (indicadores / highlights) */
            --dw-bg:      #F8F8F8;  /* Fondo base */
            --dw-text:    #333333;  /* Texto principal */
            --dw-card:    #FFFFFF;  /* Fondo de tarjetas */
        }
        body{ font-family:'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial; }
        .brand-gradient { background: linear-gradient(135deg, var(--dw-primary), var(--dw-lilac)); }
        .subtle-gradient { background: linear-gradient(90deg, rgba(169,138,212,.08), rgba(196,161,224,.08), rgba(248,163,201,.08)); }
        .ring-brand { box-shadow: 0 0 0 3px rgba(169,138,212,.18); }
    </style>
</head>
<body class="min-h-full text-[color:var(--dw-text)]">
    <div class="min-h-screen grid grid-cols-12">

        {{-- SIDEBAR --}}
        <aside class="col-span-12 md:col-span-3 lg:col-span-2 bg-white border-r border-gray-100 h-screen sticky top-0">
            <div class="px-4 py-4 flex items-center gap-3">
                <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="h-9 w-auto">
                <div class="font-bold" style="font-family:'Poppins'">DecoWandy</div>
            </div>
            <nav class="px-2 pb-4 space-y-1 text-sm">
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50 text-[color:var(--dw-text)] @if(request()->is('dashboard')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">dashboard</span> Dashboard
                </a>
                <a href="{{ route('sales.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50 @if(request()->is('ventas')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">point_of_sale</span> Ventas
                </a>
                <a href="{{ route('customers.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50 @if(request()->is('clientes*')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">group</span> Clientes
                </a>
                <a href="{{ route('items.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50 @if(request()->is('items')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">inventory_2</span> Ítems - Inventario
                </a>
                <a href="{{ route('purchases.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50 @if(request()->is('compras*')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">shopping_cart</span> Compras
                </a>
                <a href="{{ route('expenses.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50
                        @if(request()->is('gastos')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">request_quote</span> Gastos
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50 @if(request()->is('reportes') || request()->is('finanzas')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">monitoring</span> Finanzas y reportes
                </a>
                <a href="{{ route('finance.investments') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50 @if(request()->is('finanzas/inversiones')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">savings</span> Inversiones
                </a>
                <a href="{{ route('settings.public') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-violet-50
                            @if(request()->is('ajustes*')) bg-violet-50 text-[color:var(--dw-primary)] @endif">
                    <span class="material-symbols-outlined text-base">settings</span> Ajustes
                </a>

            </nav>
        </aside>

        {{-- CONTENT --}}
        <main class="col-span-12 md:col-span-9 lg:col-span-10 flex flex-col h-screen overflow-hidden">

            {{-- TOPBAR --}}
            <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-100">
                <div class="w-full px-4 lg:px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="hidden md:block text-sm text-gray-500">Panel administrativo</div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button id="openSaleModal"
                                type="button"
                                class="hidden sm:inline-flex items-center gap-2 px-3 py-2 rounded-xl text-white brand-gradient shadow hover:opacity-90">
                            <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                            Registrar venta
                        </button>


                        {{-- Menú del usuario --}}
                        <div class="relative">
                            <button id="userMenuBtn" class="h-9 w-9 rounded-full bg-[color:var(--dw-lilac)] ring-brand focus:outline-none flex items-center justify-center">
                                <span class="material-symbols-outlined text-[color:var(--dw-text)] text-[20px]">person</span>
                            </button>
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-44 rounded-xl bg-white border border-gray-100 shadow-lg z-50">
                                <div class="px-3 py-2 text-xs text-gray-500">
                                    {{ Auth::user()->name ?? 'Usuario' }}
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm hover:bg-gray-50">
                                    Perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50">
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Franja decorativa con onda sutil --}}
                <div class="relative h-12 subtle-gradient overflow-hidden">
                    <svg class="absolute inset-0 w-full h-full" viewBox="0 0 1440 100" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M0,60 C240,100 480,20 720,60 C960,100 1200,20 1440,60 L1440,100 L0,100 Z"
                              fill="url(#gradAdmin)"/>
                        <defs>
                            <linearGradient id="gradAdmin" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%"  stop-color="rgba(169,138,212,.25)"/>
                                <stop offset="50%" stop-color="rgba(196,161,224,.25)"/>
                                <stop offset="100%" stop-color="rgba(248,163,201,.18)"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </header>

            {{-- SLOT DE CONTENIDO --}}
            <div class="flex-1 overflow-y-auto">
                <div class="w-full px-4 lg:px-6 py-8">
                    @yield('content')
                </div>
            </div>

            {{-- Modal Registrar venta (UI) --}}
            @include('sales.partials.modal-create')

        </main>
    </div>

    {{-- Script para desplegar el menú del usuario --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('userMenuBtn');
            const menu = document.getElementById('userMenu');
            if (!btn || !menu) return;
            btn.addEventListener('click', () => menu.classList.toggle('hidden'));
            document.addEventListener('click', (e) => {
                if (!menu.contains(e.target) && !btn.contains(e.target)) menu.classList.add('hidden');
            });
        });
    </script>

    {{-- Iconos Google (ligeros). Puedes quitar si ya usas otros íconos. --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL,GRAD@400,0,0" rel="stylesheet" />
@stack('scripts')
</body>
</html>
