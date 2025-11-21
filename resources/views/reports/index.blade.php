{{-- resources/views/reports/index.blade.php
     Reportes — Métricas financieras reales
--}}
@extends('layouts.admin')

@section('title','Reportes — DecoWandy')

@section('content')
  {{-- Encabezado + filtros --}}
  <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <h1 class="text-2xl font-bold">Reportes</h1>
      <p class="text-sm text-gray-500">Analiza ventas, costos, categorías y flujo.</p>
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

  {{-- KPIs --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Ingresos</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($totales['ingresos'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Ventas filtradas</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">COGS estimado</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($totales['cogs'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Costo según ficha de producto</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Gastos</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($totales['gastos'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Gastos + compras sin inventario</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Utilidad neta</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($totales['utilidad'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Ingresos − COGS − Gastos</div>
    </div>
  </div>

  {{-- Gráficas --}}
  <div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <h3 class="font-semibold">Flujo de caja</h3>
      <canvas id="rp_cashflow" class="mt-4"></canvas>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <h3 class="font-semibold">Ventas por categoría</h3>
      <canvas id="rp_cat" class="mt-4"></canvas>
    </div>
  </div>

  {{-- Listados --}}
  <div class="mt-6 grid gap-6 lg:grid-cols-2">
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script defer>
document.addEventListener('DOMContentLoaded', () => {
  const cashflow = @json($cashflowDataset, JSON_UNESCAPED_UNICODE);
  const porCategoria = @json($ventasPorCategoria, JSON_UNESCAPED_UNICODE);

  const c1 = document.getElementById('rp_cashflow');
  if (c1 && window.Chart) new Chart(c1, {
    type: 'line',
    data: { labels: cashflow.labels,
      datasets: [{ label:'Entradas', data:cashflow.entradas, borderWidth:2, tension:.35, borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,0.1)' },
                 { label:'Salidas',  data:cashflow.salidas, borderWidth:2, tension:.35, borderColor:'#f43f5e', backgroundColor:'rgba(244,63,94,0.1)' }] },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ labels:{ boxWidth:10 }}}}
  });

  const c2 = document.getElementById('rp_cat');
  if (c2 && window.Chart) new Chart(c2, {
    type: 'doughnut',
    data: { labels: porCategoria.labels, datasets:[{ data: porCategoria.data, backgroundColor:['#6366f1','#f97316','#22c55e'] }]},
    options: { responsive:true, plugins:{ legend:{ position:'bottom', labels:{ boxWidth:10 }}} }
  });
});
</script>
@endpush
