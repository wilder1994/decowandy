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
            <div class="mt-2 text-2xl font-bold">$ {{ number_format($kpis['sales_today'], 0, ',', '.') }}</div>
            <div class="mt-1 text-xs text-gray-500">vs ayer: $ {{ number_format($kpis['sales_yesterday'], 0, ',', '.') }}</div>
        </div>

        {{-- Ganancia del día --}}
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm hover:shadow md:transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">Ganancia del día</div>
                <span class="material-symbols-outlined text-[color:var(--dw-yellow)]">savings</span>
            </div>
            <div class="mt-2 text-2xl font-bold">$ {{ number_format(max($kpis['sales_today'] - $kpis['expenses_today'], 0), 0, ',', '.') }}</div>
            <div class="mt-1 text-xs text-gray-500">Gastos hoy: $ {{ number_format($kpis['expenses_today'], 0, ',', '.') }}</div>
        </div>

        {{-- Ventas del mes --}}
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm hover:shadow md:transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">Ventas del mes</div>
                <span class="material-symbols-outlined text-[color:var(--dw-primary)]">trending_up</span>
            </div>
            <div class="mt-2 text-2xl font-bold">$ {{ number_format($kpis['sales_month'], 0, ',', '.') }}</div>
            <div class="mt-1 text-xs text-gray-500">vs mes pasado: $ {{ number_format($kpis['sales_prev_month'], 0, ',', '.') }}</div>
        </div>

        {{-- Stock bajo --}}
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm hover:shadow md:transition">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">Stock bajo</div>
                <span class="material-symbols-outlined text-[color:var(--dw-rose)]">inventory</span>
            </div>
            <div class="mt-2 text-2xl font-bold">{{ $kpis['low_stock_count'] }}</div>
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
            @php $papel = $kpis['sectors']['papeleria'] ?? 0; $maxSector = max($kpis['sectors']); @endphp
            <div class="mt-2 text-xl font-bold">$ {{ number_format($papel, 0, ',', '.') }}</div>
            <div class="mt-2 h-2 rounded-full bg-[color:var(--dw-lilac)]/50">
                @php $w = $maxSector > 0 ? max(5, ($papel / $maxSector) * 100) : 0; @endphp
                <div class="h-2 rounded-full brand-gradient" style="width: {{ $w }}%"></div>
            </div>
        </div>
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
            <div class="text-sm text-gray-500">Impresión (COP)</div>
            @php $imp = $kpis['sectors']['impresion'] ?? 0; @endphp
            <div class="mt-2 text-xl font-bold">$ {{ number_format($imp, 0, ',', '.') }}</div>
            <div class="mt-2 h-2 rounded-full bg-[color:var(--dw-lilac)]/50">
                @php $w = $maxSector > 0 ? max(5, ($imp / $maxSector) * 100) : 0; @endphp
                <div class="h-2 rounded-full" style="background:linear-gradient(90deg, var(--dw-lilac), var(--dw-rose)); width: {{ $w }}%"></div>
            </div>
        </div>
        <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
            <div class="text-sm text-gray-500">Diseño (COP)</div>
            @php $dis = $kpis['sectors']['diseno'] ?? 0; @endphp
            <div class="mt-2 text-xl font-bold">$ {{ number_format($dis, 0, ',', '.') }}</div>
            <div class="mt-2 h-2 rounded-full bg-[color:var(--dw-lilac)]/50">
                @php $w = $maxSector > 0 ? max(5, ($dis / $maxSector) * 100) : 0; @endphp
                <div class="h-2 rounded-full" style="background:linear-gradient(90deg, var(--dw-primary), var(--dw-yellow)); width: {{ $w }}%"></div>
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
                        @forelse($lastSales as $sale)
                            <tr>
                                <td class="py-2 pr-4">{{ optional($sale->sold_at ?? $sale->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="py-2 pr-4">-</td>
                                <td class="py-2 pr-4">{{ $sale->items_count }}</td>
                                <td class="py-2 pr-4">$ {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="py-2"><span class="px-2 py-1 rounded-full bg-[color:var(--dw-rose)]/25 text-[color:var(--dw-text)]">{{ $sale->payment_method }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-3 text-center text-sm text-gray-500">No hay ventas registradas.</td>
                            </tr>
                        @endforelse
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
                @forelse($lowStock as $stock)
                    <li class="flex items-center justify-between">
                        <span class="truncate pr-3">{{ $stock->item->name ?? 'Producto' }}</span>
                        <span class="px-2 py-1 rounded-full bg-[color:var(--dw-yellow)]/30">{{ $stock->quantity }} / min {{ $stock->min_threshold }}</span>
                    </li>
                @empty
                    <li class="text-gray-500">Sin productos en alerta.</li>
                @endforelse
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
                    labels: @json($cashflow['labels']),
                    datasets: [
                        { label:'Ingresos', data:@json($cashflow['ingresos']), borderWidth:2, tension:0.35, borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,0.1)' },
                        { label:'Egresos',  data:@json($cashflow['egresos']),  borderWidth:2, tension:0.35, borderColor:'#f43f5e', backgroundColor:'rgba(244,63,94,0.1)' },
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
                    labels: @json($topItems->pluck('name')),
                    datasets: [{ label:'Cantidad', data: @json($topItems->pluck('qty')), borderWidth:1 }]
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
