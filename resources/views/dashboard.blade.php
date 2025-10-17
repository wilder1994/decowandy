{{-- resources/views/dashboard.blade.php
    Dashboard DecoWandy (UI en español, datos placeholder)
--}}
@extends('layouts.admin')

@section('title','Dashboard — DecoWandy')

@section('content')

    {{-- TARJETAS KPI --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Ventas del día --}}
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm hover:shadow md:transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">Ventas del día</div>
                <span class="material-symbols-outlined text-[color:var(--dw-primary)]">payments</span>
            </div>
            <div class="mt-2 text-2xl font-bold">$ 0</div>
            <div class="mt-1 text-xs text-gray-500">vs ayer: —</div>
        </div>

        {{-- Ganancia del día --}}
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm hover:shadow md:transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">Ganancia del día</div>
                <span class="material-symbols-outlined text-[color:var(--dw-yellow)]">savings</span>
            </div>
            <div class="mt-2 text-2xl font-bold">$ 0</div>
            <div class="mt-1 text-xs text-gray-500">Gastos hoy: $ 0</div>
        </div>

        {{-- Ventas del mes --}}
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm hover:shadow md:transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">Ventas del mes</div>
                <span class="material-symbols-outlined text-[color:var(--dw-primary)]">trending_up</span>
            </div>
            <div class="mt-2 text-2xl font-bold">$ 0</div>
            <div class="mt-1 text-xs text-gray-500">vs mes pasado: —</div>
        </div>

        {{-- Stock bajo --}}
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm hover:shadow md:transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">Stock bajo</div>
                <span class="material-symbols-outlined text-[color:var(--dw-rose)]">inventory</span>
            </div>
            <div class="mt-2 text-2xl font-bold">0</div>
            <div class="mt-1 text-xs text-gray-500">Productos en alerta</div>
        </div>
    </div>

    {{-- GRÁFICOS PRINCIPALES --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">Flujo de caja (últimos 14 días)</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/40 text-[color:var(--dw-primary)]">Demo</span>
            </div>
            <canvas id="cashflowChart" class="mt-4"></canvas>
        </div>

        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">Top ítems (cantidades)</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/40 text-[color:var(--dw-primary)]">Demo</span>
            </div>
            <canvas id="topItemsChart" class="mt-4"></canvas>
        </div>
    </div>

    {{-- RESUMEN POR SECTOR --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
            <div class="text-sm text-gray-500">Papelería (COP)</div>
            <div class="mt-2 text-xl font-bold">$ 0</div>
            <div class="mt-2 h-2 rounded-full bg-[color:var(--dw-lilac)]/50">
                <div class="h-2 rounded-full brand-gradient w-1/4"></div>
            </div>
        </div>
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
            <div class="text-sm text-gray-500">Impresión (COP)</div>
            <div class="mt-2 text-xl font-bold">$ 0</div>
            <div class="mt-2 h-2 rounded-full bg-[color:var(--dw-lilac)]/50">
                <div class="h-2 rounded-full" style="background:linear-gradient(90deg, var(--dw-lilac), var(--dw-rose)); width: 20%"></div>
            </div>
        </div>
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
            <div class="text-sm text-gray-500">Diseño (COP)</div>
            <div class="mt-2 text-xl font-bold">$ 0</div>
            <div class="mt-2 h-2 rounded-full bg-[color:var(--dw-lilac)]/50">
                <div class="h-2 rounded-full" style="background:linear-gradient(90deg, var(--dw-primary), var(--dw-yellow)); width: 30%"></div>
            </div>
        </div>
    </div>

    {{-- LISTAS: Últimas ventas & Stock bajo --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">Últimas ventas</h3>
                <a href="#" class="text-sm text-[color:var(--dw-primary)] hover:underline">Ver todo</a>
            </div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-gray-500">
                        <tr class="text-left">
                            <th class="py-2 pr-4">Fecha</th>
                            <th class="py-2 pr-4">Vendedor</th>
                            <th class="py-2 pr-4">Ítems</th>
                            <th class="py-2 pr-4">Total</th>
                            <th class="py-2">Método</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @for ($i=0; $i<5; $i++)
                            <tr>
                                <td class="py-2 pr-4">—</td>
                                <td class="py-2 pr-4">—</td>
                                <td class="py-2 pr-4">—</td>
                                <td class="py-2 pr-4">$ 0</td>
                                <td class="py-2"><span class="px-2 py-1 rounded-full bg-[color:var(--dw-rose)]/25 text-[color:var(--dw-text)]">—</span></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold">Stock bajo</h3>
                <a href="#" class="text-sm text-[color:var(--dw-primary)] hover:underline">Gestionar</a>
            </div>
            <ul class="mt-4 space-y-3 text-sm">
                @for ($i=0; $i<5; $i++)
                    <li class="flex items-center justify-between">
                        <span class="truncate pr-3">—</span>
                        <span class="px-2 py-1 rounded-full bg-[color:var(--dw-yellow)]/30">0 / min 0</span>
                    </li>
                @endfor
            </ul>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Chart.js desde CDN para demo; luego podemos moverlo a Vite si quieres --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script defer>
    document.addEventListener('DOMContentLoaded', () => {
        // ---- Flujo de caja (línea) ----
        const cashflowCtx = document.getElementById('cashflowChart');
        if (cashflowCtx) {
            new Chart(cashflowCtx, {
                type: 'line',
                data: {
                    labels: ['D1','D2','D3','D4','D5','D6','D7','D8','D9','D10','D11','D12','D13','D14'],
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: [0,1,0,2,1,3,2,4,3,4,3,5,4,6],
                            borderWidth: 2,
                            tension: 0.35
                        },
                        {
                            label: 'Egresos',
                            data: [0,0,1,0,1,1,1,2,1,2,2,2,1,3],
                            borderWidth: 2,
                            tension: 0.35
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } },
                    plugins: {
                        legend: { labels: { boxWidth: 10 }},
                        tooltip: { callbacks: { label: c => `${c.dataset.label}: $ ${c.parsed.y}` } }
                    }
                }
            });
        }

        // ---- Top ítems (barras horizontales) ----
        const topCtx = document.getElementById('topItemsChart');
        if (topCtx) {
            new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: ['Ítem 1','Ítem 2','Ítem 3','Ítem 4','Ítem 5','Ítem 6','Ítem 7','Ítem 8','Ítem 9','Ítem 10'],
                    datasets: [{
                        label: 'Cantidad',
                        data: [5,4,4,3,3,2,2,2,1,1],
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: { x: { beginAtZero: true } },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: c => ` ${c.parsed.x} und` } }
                    }
                }
            });
        }
    });
    </script>
@endpush
