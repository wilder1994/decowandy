{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel DecoWandy')</title>
    @include('partials.dw-head-assets')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full">
    <div class="min-h-screen grid grid-cols-12">

        <aside class="col-span-12 md:col-span-3 lg:col-span-2 sticky top-0 h-screen dw-hairline border-r bg-dw-card">
            <div class="flex items-center gap-2.5 px-4 py-3.5">
                <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="h-8 w-auto">
                <div class="font-display text-sm font-bold text-dw-text">DecoWandy</div>
            </div>
            <nav class="space-y-0.5 px-2 pb-4 text-sm">
                @can('access-dashboard')
                    <x-dw-nav-link :href="url('/dashboard')" :active="request()->is('dashboard')" icon="dashboard">Dashboard</x-dw-nav-link>
                @endcan
                @can('operate')
                    <x-dw-nav-link :href="route('sales.index')" :active="request()->is('ventas*')" icon="point_of_sale">Ventas</x-dw-nav-link>
                    <x-dw-nav-link :href="route('customers.index')" :active="request()->is('clientes*')" icon="group">Clientes</x-dw-nav-link>
                @endcan
                @can('manage-inventory')
                    <x-dw-nav-link :href="route('items.index')" :active="request()->is('items*')" icon="inventory_2">Ítems - Inventario</x-dw-nav-link>
                    <x-dw-nav-link :href="route('purchases.index')" :active="request()->is('compras*')" icon="shopping_cart">Compras</x-dw-nav-link>
                @endcan
                @can('manage-finance')
                    <x-dw-nav-link :href="route('expenses.index')" :active="request()->is('gastos*')" icon="request_quote">Gastos</x-dw-nav-link>
                @endcan
                @can('view-reports')
                    <x-dw-nav-link :href="route('reports.index')" :active="request()->is('reportes*') || request()->is('finanzas')" icon="monitoring">Finanzas y reportes</x-dw-nav-link>
                @endcan
                @can('manage-finance')
                    <x-dw-nav-link :href="route('finance.investments')" :active="request()->is('finanzas/inversiones*')" icon="savings">Inversiones</x-dw-nav-link>
                @endcan
                @can('manage-users')
                    <x-dw-nav-link :href="route('settings.users')" :active="request()->is('ajustes/usuarios*')" icon="manage_accounts">Usuarios</x-dw-nav-link>
                @endcan
                @can('manage-public-page')
                    <x-dw-nav-link :href="route('settings.public')" :active="request()->is('ajustes/welcome*')" icon="settings">Ajustes</x-dw-nav-link>
                @endcan
            </nav>
        </aside>

        <main class="col-span-12 flex h-screen flex-col overflow-hidden md:col-span-9 lg:col-span-10">

            <header class="sticky top-0 z-30 border-b dw-hairline bg-dw-card/95 backdrop-blur">
                <div class="flex w-full items-center justify-between px-4 py-2.5 lg:px-5">
                    <div class="hidden text-xs font-medium uppercase tracking-wide text-dw-muted md:block">Panel administrativo</div>
                    <div class="flex items-center gap-2">
                        @can('operate')
                            <x-dw-button id="openSaleModal" type="button" class="hidden sm:inline-flex">
                                <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                                Registrar venta
                            </x-dw-button>
                        @endcan
                        <div class="relative">
                            <button id="userMenuBtn" type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-dw-lilac-soft dw-hairline-neon focus:outline-none">
                                <span class="material-symbols-outlined text-[18px] text-dw-text">person</span>
                            </button>
                            <div id="userMenu" class="absolute right-0 z-50 mt-2 hidden w-44 rounded-dw bg-dw-card py-1 shadow-dw-neon dw-hairline-neon">
                                <div class="px-3 py-2 text-xs text-dw-muted">{{ Auth::user()->name ?? 'Usuario' }}</div>
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm hover:bg-dw-lilac-soft">Perfil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 text-left text-sm hover:bg-dw-lilac-soft">Cerrar sesión</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="subtle-gradient relative h-8 overflow-hidden">
                    <svg class="absolute inset-0 h-full w-full" viewBox="0 0 1440 100" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M0,60 C240,100 480,20 720,60 C960,100 1200,20 1440,60 L1440,100 L0,100 Z" fill="url(#gradAdmin)"/>
                        <defs>
                            <linearGradient id="gradAdmin" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="rgba(169,138,212,.2)"/>
                                <stop offset="50%" stop-color="rgba(196,161,224,.2)"/>
                                <stop offset="100%" stop-color="rgba(248,163,201,.14)"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto">
                <div class="w-full px-4 py-5 lg:px-5 lg:py-6">
                    @yield('content')
                </div>
            </div>

            @can('operate')
                @include('sales.partials.modal-create')
            @endcan
        </main>
    </div>

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
    @stack('scripts')
</body>
</html>
