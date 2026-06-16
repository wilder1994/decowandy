{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    @include('partials.dw-theme-init')
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel DecoWandy')</title>
    @include('partials.dw-head-assets')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full">
    <div id="adminSidebarBackdrop" class="dw-admin-backdrop md:hidden" aria-hidden="true"></div>

    <div class="min-h-screen md:grid md:grid-cols-12">

        <aside id="adminSidebar" class="dw-admin-sidebar fixed inset-y-0 left-0 z-40 flex h-screen w-[min(17.5rem,85vw)] flex-col border-r bg-dw-card dw-hairline md:static md:col-span-3 md:w-auto lg:col-span-2">
            <div class="flex items-center justify-between gap-2.5 px-4 py-3.5">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="h-8 w-auto">
                    <div class="font-display text-sm font-bold text-dw-text">DecoWandy</div>
                </div>
                <button id="adminMenuClose" type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-dw text-dw-muted hover:bg-dw-lilac-soft md:hidden" aria-label="Cerrar menú">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
            <nav class="flex-1 space-y-0.5 overflow-y-auto px-2 pb-4 text-sm">
                @can('access-dashboard')
                    <x-dw-nav-link :href="url('/dashboard')" :active="request()->is('dashboard')" icon="dashboard">Dashboard</x-dw-nav-link>
                @endcan
                @can('operate')
                    <x-dw-nav-link :href="route('sales.index')" :active="request()->is('ventas*')" icon="point_of_sale">Ventas</x-dw-nav-link>
                    <x-dw-nav-link :href="route('customers.index')" :active="request()->is('clientes*')" icon="group">Clientes</x-dw-nav-link>
                @endcan
                @can('manage-inventory')
                    <x-dw-nav-link :href="route('purchases.index')" :active="request()->is('compras*')" icon="shopping_cart">Compras y catálogo</x-dw-nav-link>
                    <x-dw-nav-link :href="route('inventory.index')" :active="request()->is('inventario*') || request()->is('items*')" icon="inventory_2">Inventario</x-dw-nav-link>
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

        <main class="flex min-h-screen w-full flex-col overflow-hidden md:col-span-9 lg:col-span-10">

            <header class="dw-header-bar sticky top-0 z-30 border-b dw-hairline backdrop-blur">
                <div class="flex w-full items-center justify-between gap-2 px-4 py-2.5 lg:px-5">
                    <div class="flex min-w-0 items-center gap-2">
                        <button id="adminMenuBtn" type="button" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-dw border border-dw-border bg-dw-card text-dw-text hover:bg-dw-lilac-soft md:hidden" aria-label="Abrir menú">
                            <span class="material-symbols-outlined text-xl">menu</span>
                        </button>
                        <div class="min-w-0 md:block">
                            <span class="font-display text-sm font-semibold text-dw-text md:hidden">DecoWandy</span>
                            <span class="hidden text-xs font-medium uppercase tracking-wide text-dw-muted md:inline">Panel administrativo</span>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <div class="hidden min-[420px]:block">
                            <x-dw-theme-toggle />
                        </div>
                        @can('operate')
                            <x-dw-button id="openSaleModal" type="button" class="inline-flex shrink-0 gap-1 px-2.5 sm:px-4" aria-label="Registrar venta">
                                <span class="material-symbols-outlined text-base">add_shopping_cart</span>
                                <span class="hidden sm:inline">Registrar venta</span>
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

            <div class="flex-1 overflow-y-auto dw-admin-main-pad">
                <div class="dw-page-shell w-full">
                    @yield('content')
                </div>
            </div>

            @can('operate')
                @include('sales.partials.modal-create')
            @endcan
        </main>
    </div>

    @stack('modals')

    @can('operate')
        <button type="button" class="js-open-sale-modal dw-mobile-sale-fab md:hidden" aria-label="Registrar venta">
            <span class="material-symbols-outlined text-[1.65rem]">point_of_sale</span>
        </button>
    @endcan

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('userMenuBtn');
            const menu = document.getElementById('userMenu');
            if (btn && menu) {
                btn.addEventListener('click', () => menu.classList.toggle('hidden'));
                document.addEventListener('click', (e) => {
                    if (!menu.contains(e.target) && !btn.contains(e.target)) menu.classList.add('hidden');
                });
            }

            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            const openBtn = document.getElementById('adminMenuBtn');
            const closeBtn = document.getElementById('adminMenuClose');
            const mobileQuery = window.matchMedia('(max-width: 767px)');

            const openSidebar = () => {
                if (!sidebar || !backdrop || !mobileQuery.matches) return;
                sidebar.classList.add('is-open');
                backdrop.classList.add('is-visible');
                document.body.classList.add('overflow-hidden');
            };

            const closeSidebar = () => {
                if (!sidebar || !backdrop) return;
                sidebar.classList.remove('is-open');
                backdrop.classList.remove('is-visible');
                document.body.classList.remove('overflow-hidden');
            };

            openBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                openSidebar();
            });
            closeBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                closeSidebar();
            });
            backdrop?.addEventListener('click', closeSidebar);

            sidebar?.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (mobileQuery.matches) closeSidebar();
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeSidebar();
            });

            mobileQuery.addEventListener('change', (event) => {
                if (!event.matches) closeSidebar();
            });

            closeSidebar();
        });
    </script>
    @stack('scripts')
</body>
</html>
