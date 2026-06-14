@extends('layouts.admin')

@section('title', 'Dashboard — DecoWandy')

@section('content')
    <x-dw-page-header title="Dashboard" subtitle="Resumen operativo del negocio." />

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <x-dw-kpi label="Ventas del día" icon="payments" :value="'$ ' . number_format($kpis['sales_today'], 0, ',', '.')" :hint="'vs ayer: $ ' . number_format($kpis['sales_yesterday'], 0, ',', '.')" />
        <x-dw-kpi label="Ganancia del día" icon="savings" tone="yellow" :value="'$ ' . number_format(max($kpis['sales_today'] - $kpis['expenses_today'], 0), 0, ',', '.')" :hint="'Gastos hoy: $ ' . number_format($kpis['expenses_today'], 0, ',', '.')" />
        <x-dw-kpi label="Ventas del mes" icon="trending_up" :value="'$ ' . number_format($kpis['sales_month'], 0, ',', '.')" :hint="'vs mes pasado: $ ' . number_format($kpis['sales_prev_month'], 0, ',', '.')" />
        <x-dw-kpi label="Stock bajo" icon="inventory" tone="rose" :value="(string) $kpis['low_stock_count']" hint="Productos en alerta" />
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-2">
        <x-dw-card padding="p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-display text-sm font-semibold">Flujo de caja (últimos 14 días)</h3>
                <x-dw-badge>14 días</x-dw-badge>
            </div>
            <canvas id="cashflowChart" class="mt-3 max-h-56"></canvas>
        </x-dw-card>

        <x-dw-card padding="p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-display text-sm font-semibold">Top ítems (cantidades)</h3>
                <x-dw-badge>Top 10</x-dw-badge>
            </div>
            <canvas id="topItemsChart" class="mt-3 max-h-56"></canvas>
        </x-dw-card>
    </div>

    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['label' => 'Papelería (COP)', 'key' => 'papeleria', 'gradient' => 'brand-gradient'],
            ['label' => 'Impresión (COP)', 'key' => 'impresion', 'gradient' => 'bg-gradient-to-r from-dw-lilac to-dw-rose'],
            ['label' => 'Diseño (COP)', 'key' => 'diseno', 'gradient' => 'bg-gradient-to-r from-dw-primary to-dw-yellow'],
        ] as $sector)
            @php
                $amount = $kpis['sectors'][$sector['key']] ?? 0;
                $maxSector = max($kpis['sectors']);
                $width = $maxSector > 0 ? max(5, ($amount / $maxSector) * 100) : 0;
            @endphp
            <x-dw-card padding="p-3.5">
                <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">{{ $sector['label'] }}</div>
                <div class="mt-1 font-display text-lg font-bold">$ {{ number_format($amount, 0, ',', '.') }}</div>
                <div class="mt-2 h-1.5 rounded-full bg-dw-lilac-soft">
                    <div class="h-1.5 rounded-full {{ $sector['gradient'] }}" style="width: {{ $width }}%"></div>
                </div>
            </x-dw-card>
        @endforeach
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-3">
        <x-dw-card padding="p-4" class="lg:col-span-2">
            <div class="flex items-center justify-between">
                <h3 class="font-display text-sm font-semibold">Últimas ventas</h3>
                @can('operate')
                    <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-dw-primary hover:underline">Ver todo</a>
                @endcan
            </div>
            <div class="mt-3 overflow-x-auto">
                <table class="dw-table min-w-full text-sm">
                    <thead>
                        <tr class="text-left">
                            <th class="py-2 pr-4">Fecha</th>
                            <th class="py-2 pr-4">Ítems</th>
                            <th class="py-2 pr-4">Total</th>
                            <th class="py-2">Método</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lastSales as $sale)
                            <tr>
                                <td class="py-2 pr-4 text-dw-muted">{{ optional($sale->sold_at ?? $sale->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="py-2 pr-4">{{ $sale->items_count }}</td>
                                <td class="py-2 pr-4 font-medium">$ {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td class="py-2"><x-dw-badge variant="danger">{{ $sale->payment_method }}</x-dw-badge></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-3 text-center text-dw-muted">No hay ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-dw-card>

        <x-dw-card padding="p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-display text-sm font-semibold">Stock bajo</h3>
                @can('manage-inventory')
                    <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-dw-primary hover:underline">Gestionar</a>
                @endcan
            </div>
            <ul class="mt-3 space-y-2 text-sm">
                @forelse($lowStock as $stock)
                    <li class="flex items-center justify-between gap-2">
                        <span class="truncate text-dw-text">{{ $stock->item->name ?? 'Producto' }}</span>
                        <x-dw-badge variant="warning">{{ $stock->quantity }} / {{ $stock->min_threshold }}</x-dw-badge>
                    </li>
                @empty
                    <li class="text-dw-muted">Sin productos en alerta.</li>
                @endforelse
            </ul>
        </x-dw-card>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script defer>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.dwChartDefaults === 'function') {
            window.dwChartDefaults();
        }

        const theme = window.dwChartTheme || {};
        const cashflowCtx = document.getElementById('cashflowChart');
        if (cashflowCtx) {
            new Chart(cashflowCtx, {
                type: 'line',
                data: {
                    labels: @json($cashflow['labels']),
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: @json($cashflow['ingresos']),
                            borderWidth: 1.5,
                            tension: 0.35,
                            borderColor: theme.primary || '#A98AD4',
                            backgroundColor: 'rgba(169, 138, 212, 0.12)',
                            pointRadius: 2,
                        },
                        {
                            label: 'Egresos',
                            data: @json($cashflow['egresos']),
                            borderWidth: 1.5,
                            tension: 0.35,
                            borderColor: theme.danger || '#F472B6',
                            backgroundColor: 'rgba(244, 114, 182, 0.1)',
                            pointRadius: 2,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: { grid: { lineWidth: 0.5 } },
                        y: { beginAtZero: true, grid: { lineWidth: 0.5 } },
                    },
                    plugins: {
                        tooltip: { callbacks: { label: c => `${c.dataset.label}: $ ${c.parsed.y}` } },
                    },
                },
            });
        }

        const topCtx = document.getElementById('topItemsChart');
        if (topCtx) {
            new Chart(topCtx, {
                type: 'bar',
                data: {
                    labels: @json($topItems->pluck('name')),
                    datasets: [{
                        label: 'Cantidad',
                        data: @json($topItems->pluck('qty')),
                        borderWidth: 0.5,
                        backgroundColor: 'rgba(196, 161, 224, 0.55)',
                        borderColor: theme.primary || '#A98AD4',
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        x: { beginAtZero: true, grid: { lineWidth: 0.5 } },
                        y: { grid: { display: false } },
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: c => ` ${c.parsed.x} und` } },
                    },
                },
            });
        }
    });
    </script>
@endpush
