{{-- resources/views/reports/index.blade.php
     Finanzas y reportes — Métricas consolidadas
--}}
@extends('layouts.admin')

@section('title','Finanzas y reportes — DecoWandy')

@section('content')
  {{-- Encabezado + filtros --}}
  <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <h1 class="text-2xl font-bold">Finanzas y reportes</h1>
      <p class="text-sm text-gray-500">Controla flujo de caja, utilidades y desempeño por categoría.</p>
    </div>
    <form method="GET" class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 flex flex-wrap items-end gap-3">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Desde</label>
        <input type="date" name="from" value="{{ $filtros['from'] }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Hasta</label>
        <input type="date" name="to" value="{{ $filtros['to'] }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Categoría</label>
        <select name="category" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="all" @selected($filtros['category']==='all')>Todas</option>
          <option value="diseno" @selected($filtros['category']==='diseno')>Diseño</option>
          <option value="papeleria" @selected($filtros['category']==='papeleria')>Papelería</option>
          <option value="impresion" @selected($filtros['category']==='impresion')>Impresión</option>
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Método de pago</label>
        <select name="payment" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="all" @selected($filtros['payment']==='all')>Todos</option>
          <option value="cash" @selected($filtros['payment']==='cash')>Efectivo</option>
          <option value="transfer" @selected($filtros['payment']==='transfer')>Transferencia</option>
          <option value="card" @selected($filtros['payment']==='card')>Tarjeta</option>
          <option value="mixed" @selected($filtros['payment']==='mixed')>Mixto</option>
          <option value="other" @selected($filtros['payment']==='other')>Otro</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button class="rounded-xl bg-indigo-600 text-white px-4 py-2 font-semibold hover:bg-indigo-700">Aplicar</button>
        <a href="{{ route('reports.index') }}" class="rounded-xl bg-white border px-4 py-2 hover:bg-slate-50">Limpiar</a>
      </div>
    </form>
  </div>

  {{-- Resumen financiero --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl bg-gradient-to-br from-indigo-50/80 via-white to-emerald-50/80 border border-indigo-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">Caja estimada</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($resumen['caja'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Ingresos - egresos - inversión</div>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-violet-50/80 via-white to-indigo-50/80 border border-violet-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">Inversión acumulada</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($resumen['invertido'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ $resumen['porcentaje_recuperado'] }}% recuperado</div>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-emerald-50/80 via-white to-amber-50/80 border border-emerald-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">Recuperado</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($resumen['recuperado'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Utilidad periodo: ${{ number_format($resumen['utilidad'], 0, ',', '.') }}</div>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-amber-50/80 via-white to-rose-50/80 border border-amber-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">Por recuperar</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($resumen['por_recuperar'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Foco en ROI</div>
    </div>
  </div>

  {{-- KPIs de ventas y gastos --}}
  <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl bg-gradient-to-br from-indigo-50/80 via-white to-emerald-50/80 border border-indigo-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">Ingresos</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($totales['ingresos'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Ventas filtradas</div>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-violet-50/80 via-white to-indigo-50/80 border border-violet-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">COGS estimado</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($totales['cogs'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Costo según ficha de producto</div>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-emerald-50/80 via-white to-amber-50/80 border border-emerald-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">Gastos</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($totales['gastos'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Gastos + compras sin inventario</div>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-amber-50/80 via-white to-rose-50/80 border border-amber-100 p-4 shadow-lg">
      <div class="text-sm text-gray-600">Utilidad neta</div>
      <div class="mt-1 text-2xl font-bold text-slate-900">${{ number_format($totales['utilidad'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Ingresos − COGS − Gastos</div>
    </div>
  </div>

  {{-- Movimientos y listados --}}
  <div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h3 class="font-semibold">Movimientos de caja</h3>
          <p class="text-xs text-gray-500">Ventas, gastos, compras sin inventario e inversiones.</p>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left">
              <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Concepto</th><th class="py-2 pr-4">Método</th><th class="py-2 pr-4 text-right">Monto</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($movimientosCaja as $mov)
              <tr>
                <td class="py-2 pr-4">{{ $mov['fecha']->format('d/m/Y') }}</td>
                <td class="py-2 pr-4">{{ $mov['concepto'] }}</td>
                <td class="py-2 pr-4 text-gray-500">{{ ucfirst($mov['metodo']) }}</td>
                <td class="py-2 pr-4 text-right font-semibold {{ $mov['tipo'] === 'entrada' ? 'text-emerald-700' : 'text-rose-600' }}">
                  {{ $mov['tipo'] === 'entrada' ? '+' : '-' }}${{ number_format(abs($mov['monto']), 0, ',', '.') }}
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="py-3 text-center text-sm text-gray-500">Sin movimientos para el rango seleccionado.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="grid gap-6">
      <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold">Ventas</h3>
          <span class="text-xs text-gray-500">Últimas 8</span>
        </div>
        <div class="mt-3 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-gray-500">
              <tr class="text-left">
                <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Cliente</th><th class="py-2 pr-4">Ítems</th><th class="py-2 pr-4">Total</th><th class="py-2">Método</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse($ventasListado as $sale)
                <tr>
                  <td class="py-2 pr-4">{{ optional($sale->sold_at)->format('d/m/Y') ?? '—' }}</td>
                  <td class="py-2 pr-4">{{ $sale->customer_name ?: 'Mostrador' }}</td>
                  <td class="py-2 pr-4">{{ $sale->items_count }}</td>
                  <td class="py-2 pr-4">${{ number_format($sale->total, 0, ',', '.') }}</td>
                  <td class="py-2">
                    <span class="px-2 py-1 rounded-full bg-slate-100">{{ $sale->payment_method }}</span>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="py-3 text-center text-sm text-gray-500">Sin ventas en el rango seleccionado.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold">Gastos / Compras sin stock</h3>
          <span class="text-xs text-gray-500">Últimos movimientos</span>
        </div>
        <div class="mt-3 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="text-gray-500">
              <tr class="text-left">
                <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Concepto</th><th class="py-2 pr-4">Categoría</th><th class="py-2 pr-4">Monto</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @forelse($gastosListado as $item)
                <tr>
                  <td class="py-2 pr-4">{{ $item['fecha']->format('d/m/Y') }}</td>
                  <td class="py-2 pr-4">{{ $item['concepto'] }}</td>
                  <td class="py-2 pr-4">{{ $item['categoria'] }}</td>
                  <td class="py-2 pr-4">${{ number_format($item['monto'], 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="py-3 text-center text-sm text-gray-500">Sin egresos en el rango seleccionado.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Gráficas --}}
  <div class="mt-6 grid gap-4 lg:grid-cols-3">
    <div class="rounded-2xl bg-gradient-to-br from-indigo-50/80 via-white to-emerald-50/80 border border-indigo-100 p-5 shadow-lg h-full">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-semibold">Flujo de caja</h3>
          <p class="text-xs text-gray-500">Incluye inversiones y compras sin inventario.</p>
        </div>
        <span class="text-xs px-3 py-1 rounded-full bg-indigo-50 text-indigo-700">Línea</span>
      </div>
      <canvas id="rp_cashflow" class="mt-4 h-52"></canvas>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-violet-50/70 via-white to-indigo-50/80 border border-violet-100 p-5 shadow-lg h-full">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Ingresos vs egresos</h3>
        <span class="text-xs px-3 py-1 rounded-full bg-violet-100 text-violet-800">Barras</span>
      </div>
      <canvas id="rp_ingresos_gastos" class="mt-4 h-52"></canvas>
    </div>
    <div class="rounded-2xl bg-gradient-to-br from-rose-50/70 via-white to-violet-50/70 border border-rose-100 p-5 shadow-lg h-full">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Ventas por categoría</h3>
        <span class="text-xs px-3 py-1 rounded-full bg-emerald-100 text-emerald-800">Dona 3D</span>
      </div>
      <div id="rp_cat" class="mt-4 h-52"></div>
    </div>
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
