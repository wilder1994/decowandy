{{-- resources/views/reports/index.blade.php
     Finanzas y reportes — Métricas consolidadas
--}}
@extends('layouts.admin')

@section('title','Finanzas y reportes — DecoWandy')

@section('content')
  <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <x-dw-page-header title="Finanzas y reportes" subtitle="Controla flujo de caja, utilidades y desempeño por categoría." />
    <form method="GET" class="dw-filter-panel flex flex-wrap items-end gap-3">
      <div>
        <label class="dw-label mb-1">Desde</label>
        <input type="date" name="from" value="{{ $filtros['from'] }}" class="dw-input">
      </div>
      <div>
        <label class="dw-label mb-1">Hasta</label>
        <input type="date" name="to" value="{{ $filtros['to'] }}" class="dw-input">
      </div>
      <div>
        <label class="dw-label mb-1">Categoría</label>
        <select name="category" class="dw-select">
          <option value="all" @selected($filtros['category']==='all')>Todas</option>
          <option value="diseno" @selected($filtros['category']==='diseno')>Diseño</option>
          <option value="papeleria" @selected($filtros['category']==='papeleria')>Papelería</option>
          <option value="impresion" @selected($filtros['category']==='impresion')>Impresión</option>
        </select>
      </div>
      <div>
        <label class="dw-label mb-1">Método de pago</label>
        <select name="payment" class="dw-select">
          <option value="all" @selected($filtros['payment']==='all')>Todos</option>
          <option value="cash" @selected($filtros['payment']==='cash')>Efectivo</option>
          <option value="transfer" @selected($filtros['payment']==='transfer')>Transferencia</option>
          <option value="card" @selected($filtros['payment']==='card')>Tarjeta</option>
          <option value="mixed" @selected($filtros['payment']==='mixed')>Mixto</option>
          <option value="other" @selected($filtros['payment']==='other')>Otro</option>
        </select>
      </div>
      <div class="flex gap-2">
        <x-dw-button type="submit">Aplicar</x-dw-button>
        <x-dw-button variant="secondary" :href="route('reports.index')">Limpiar</x-dw-button>
      </div>
    </form>
  </div>

  <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-dw-kpi label="Caja estimada" :value="'$'.number_format($resumen['caja'], 0, ',', '.')" hint="Ingresos - egresos - inversión" icon="account_balance_wallet" />
    <x-dw-kpi label="Inversión acumulada" :value="'$'.number_format($resumen['invertido'], 0, ',', '.')" tone="lilac" icon="savings" :hint="$resumen['porcentaje_recuperado'].'% recuperado'" />
    <x-dw-kpi label="Recuperado" :value="'$'.number_format($resumen['recuperado'], 0, ',', '.')" tone="yellow" icon="trending_up" :hint="'Utilidad periodo: $'.number_format($resumen['utilidad'], 0, ',', '.')" />
    <x-dw-kpi label="Por recuperar" :value="'$'.number_format($resumen['por_recuperar'], 0, ',', '.')" tone="rose" icon="hourglass_top" hint="Foco en ROI" />
  </div>

  <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-dw-kpi label="Ingresos" :value="'$'.number_format($totales['ingresos'], 0, ',', '.')" hint="Ventas filtradas" icon="payments" />
    <x-dw-kpi label="COGS estimado" :value="'$'.number_format($totales['cogs'], 0, ',', '.')" tone="lilac" hint="Costo según ficha de producto" icon="inventory_2" />
    <x-dw-kpi label="Gastos" :value="'$'.number_format($totales['gastos'], 0, ',', '.')" tone="yellow" hint="Gastos + compras sin inventario" icon="receipt_long" />
    <x-dw-kpi label="Utilidad neta" :value="'$'.number_format($totales['utilidad'], 0, ',', '.')" tone="rose" hint="Ingresos − COGS − Gastos" icon="account_balance" />
  </div>

  <div class="mt-6 grid gap-6 lg:grid-cols-2">
    <x-dw-card padding="p-4">
      <div class="mb-3 flex items-center justify-between">
        <div>
          <h3 class="font-display text-sm font-semibold text-dw-text">Movimientos de caja</h3>
          <p class="text-xs text-dw-muted">Ventas, gastos, compras sin inventario e inversiones.</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="dw-table min-w-full text-sm">
          <thead>
            <tr class="text-left">
              <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Concepto</th><th class="py-2 pr-4">Método</th><th class="py-2 pr-4 text-right">Monto</th>
            </tr>
          </thead>
          <tbody>
            @forelse($movimientosCaja as $mov)
              <tr>
                <td class="py-2 pr-4">{{ $mov['fecha']->format('d/m/Y') }}</td>
                <td class="py-2 pr-4">{{ $mov['concepto'] }}</td>
                <td class="py-2 pr-4 text-dw-muted">{{ ucfirst($mov['metodo']) }}</td>
                <td class="py-2 pr-4 text-right font-semibold {{ $mov['tipo'] === 'entrada' ? 'text-dw-primary' : 'text-dw-rose' }}">
                  {{ $mov['tipo'] === 'entrada' ? '+' : '-' }}${{ number_format(abs($mov['monto']), 0, ',', '.') }}
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="py-3 text-center text-sm text-dw-muted">Sin movimientos para el rango seleccionado.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-dw-card>

    <div class="grid gap-6">
      <x-dw-card padding="p-4">
        <div class="flex items-center justify-between">
          <h3 class="font-display text-sm font-semibold text-dw-text">Ventas</h3>
          <span class="text-xs text-dw-muted">Últimas 8</span>
        </div>
        <div class="mt-3 overflow-x-auto">
          <table class="dw-table min-w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Cliente</th><th class="py-2 pr-4">Ítems</th><th class="py-2 pr-4">Total</th><th class="py-2">Método</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ventasListado as $sale)
                <tr>
                  <td class="py-2 pr-4">{{ optional($sale->sold_at)->format('d/m/Y') ?? '—' }}</td>
                  <td class="py-2 pr-4">{{ $sale->customer_name ?: 'Mostrador' }}</td>
                  <td class="py-2 pr-4">{{ $sale->items_count }}</td>
                  <td class="py-2 pr-4">${{ number_format($sale->total, 0, ',', '.') }}</td>
                  <td class="py-2"><x-dw-badge>{{ $sale->payment_method }}</x-dw-badge></td>
                </tr>
              @empty
                <tr><td colspan="5" class="py-3 text-center text-sm text-dw-muted">Sin ventas en el rango seleccionado.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-dw-card>

      <x-dw-card padding="p-4">
        <div class="flex items-center justify-between">
          <h3 class="font-display text-sm font-semibold text-dw-text">Gastos / Compras sin stock</h3>
          <span class="text-xs text-dw-muted">Últimos movimientos</span>
        </div>
        <div class="mt-3 overflow-x-auto">
          <table class="dw-table min-w-full text-sm">
            <thead>
              <tr class="text-left">
                <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Concepto</th><th class="py-2 pr-4">Categoría</th><th class="py-2 pr-4">Monto</th>
              </tr>
            </thead>
            <tbody>
              @forelse($gastosListado as $item)
                <tr>
                  <td class="py-2 pr-4">{{ $item['fecha']->format('d/m/Y') }}</td>
                  <td class="py-2 pr-4">{{ $item['concepto'] }}</td>
                  <td class="py-2 pr-4">{{ $item['categoria'] }}</td>
                  <td class="py-2 pr-4">${{ number_format($item['monto'], 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="py-3 text-center text-sm text-dw-muted">Sin egresos en el rango seleccionado.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-dw-card>
    </div>
  </div>

  <div class="mt-6 grid gap-4 lg:grid-cols-3">
    <x-dw-card padding="p-4" class="h-full">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-display text-sm font-semibold text-dw-text">Flujo de caja</h3>
          <p class="text-xs text-dw-muted">Incluye inversiones y compras sin inventario.</p>
        </div>
        <x-dw-badge>Línea</x-dw-badge>
      </div>
      <canvas id="rp_cashflow" class="mt-4 h-52"></canvas>
    </x-dw-card>
    <x-dw-card padding="p-4" class="h-full">
      <div class="flex items-center justify-between">
        <h3 class="font-display text-sm font-semibold text-dw-text">Ingresos vs egresos</h3>
        <x-dw-badge>Barras</x-dw-badge>
      </div>
      <canvas id="rp_ingresos_gastos" class="mt-4 h-52"></canvas>
    </x-dw-card>
    <x-dw-card padding="p-4" class="h-full">
      <div class="flex items-center justify-between">
        <h3 class="font-display text-sm font-semibold text-dw-text">Ventas por categoría</h3>
        <x-dw-badge>Dona 3D</x-dw-badge>
      </div>
      <div id="rp_cat" class="mt-4 h-52"></div>
    </x-dw-card>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js" defer></script>
<script defer>
document.addEventListener('DOMContentLoaded', () => {
  const cashflow = @json($cashflowDataset, JSON_UNESCAPED_UNICODE);
  const ingresosVsGastos = @json($ingresosVsGastosDataset, JSON_UNESCAPED_UNICODE);
  const porCategoria = @json($ventasPorCategoria, JSON_UNESCAPED_UNICODE);

  const c1 = document.getElementById('rp_cashflow');
  if (c1 && window.Chart) {
    const ctx = c1.getContext('2d');
    const gradIn = ctx.createLinearGradient(0, 0, c1.width, 0);
    gradIn.addColorStop(0, '#10B981');
    gradIn.addColorStop(0.5, '#22C55E');
    gradIn.addColorStop(1, '#3B82F6');

    const gradOut = ctx.createLinearGradient(0, 0, c1.width, 0);
    gradOut.addColorStop(0, '#F43F5E');
    gradOut.addColorStop(0.5, '#EC4899');
    gradOut.addColorStop(1, '#F59E0B');

    new Chart(c1, {
      type: 'line',
      data: {
        labels: cashflow.labels,
        datasets: [
          { label:'Entradas', data:cashflow.entradas, borderWidth:3, tension:.5, borderColor:gradIn, fill:false, pointRadius:2, pointBackgroundColor:'#10B981' },
          { label:'Salidas',  data:cashflow.salidas,  borderWidth:3, tension:.5, borderColor:gradOut, fill:false, pointRadius:2, pointBackgroundColor:'#F43F5E' }
        ]
      },
      options: {
        responsive:true,
        scales:{
          y:{ beginAtZero:true, grid:{ color:'rgba(15,23,42,0.06)' }, ticks:{ color:'#475569' }},
          x:{ grid:{ display:false }, ticks:{ color:'#475569' }}
        },
        plugins:{ legend:{ labels:{ boxWidth:10 } } }
      }
    });
  }

  const c2 = document.getElementById('rp_ingresos_gastos');
  if (c2 && window.Chart) {
    const ctx2 = c2.getContext('2d');
    const makeGradient = () => {
      const g = ctx2.createLinearGradient(0, 0, 0, c2.height);
      g.addColorStop(0, '#A855F7');
      g.addColorStop(0.35, '#8B5CF6');
      g.addColorStop(0.65, '#6366F1');
      g.addColorStop(1, '#60A5FA');
      return g;
    };
    const bars = ingresosVsGastos.labels.length;
    const backgrounds = Array.from({length: bars}, () => makeGradient());

    new Chart(c2, {
      type: 'bar',
      data: {
        labels: ingresosVsGastos.labels,
        datasets:[{
          label:'Monto',
          data: ingresosVsGastos.data,
          backgroundColor: backgrounds,
          borderWidth: 0,
          borderRadius: 12,
          borderSkipped: false,
          shadowOffsetX: 0,
          shadowOffsetY: 0,
          shadowBlur: 18,
          shadowColor: 'rgba(15,23,42,0.25)'
        }]
      },
      options: {
        responsive:true,
        scales:{
          y:{ beginAtZero:true, grid:{ color:'rgba(15,23,42,0.06)' }, ticks:{ color:'#475569' } },
          x:{ grid:{ display:false }, ticks:{ color:'#475569' } }
        },
        plugins:{ legend:{ display:false } }
      }
    });
  }

  const c3 = document.getElementById('rp_cat');
  if (c3 && window.echarts) {
    const chart = echarts.init(c3);
    const baseColors = ['#6366F1','#F472B6','#8B5CF6','#EC4899','#A855F7'];
    const seriesData = (porCategoria.labels || []).map((label, idx) => {
      const colorTop = baseColors[idx % baseColors.length];
      const colorBottom = echarts.color.lift(colorTop, -0.2);
      return {
        value: porCategoria.data?.[idx] ?? 0,
        name: label,
        itemStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: colorTop },
            { offset: 1, color: colorBottom },
          ]),
          shadowBlur: 25,
          shadowColor: 'rgba(15, 23, 42, 0.25)',
          borderWidth: 2,
          borderColor: '#0f172a',
        }
      };
    });
    chart.setOption({
      tooltip: { trigger:'item', formatter: '{b}: {c} ({d}%)' },
      legend: { bottom: 0, textStyle:{ color:'#4b5563', fontSize:11 } },
      series: [{
        type: 'pie',
        radius: ['42%','72%'],
        roseType: 'radius',
        avoidLabelOverlap: true,
        label: { color:'#0f172a', fontWeight:'700', formatter: '{d}%' },
        labelLine: { length: 12, length2: 8, smooth: true },
        selectedOffset: 14,
        hoverOffset: 16,
        itemStyle: { borderRadius: 6 },
        data: seriesData
      }]
    });
  }
});
</script>
@endpush
