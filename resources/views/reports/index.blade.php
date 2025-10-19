{{-- resources/views/reports/index.blade.php
     Reportes — Solo diseño (UI demo)
--}}
@extends('layouts.admin')

@section('title','Reportes — DecoWandy')

@section('content')
  {{-- Encabezado + filtros --}}
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Reportes</h1>
    <p class="text-sm text-gray-500">Analiza ventas, costos, categorías y flujo.</p>
  </div>

  <div class="mb-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
    <div class="grid gap-4 md:grid-cols-5">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Rango rápido</label>
        <select class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="rp_quick">
          <option>Este mes</option>
          <option>Hoy</option>
          <option>Esta semana</option>
          <option>Últimos 30 días</option>
          <option>Este año</option>
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Desde</label>
        <input type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="rp_from">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Hasta</label>
        <input type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="rp_to">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Categoría</label>
        <select class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="rp_cat">
          <option value="all">Todas</option>
          <option>Diseño</option>
          <option>Papelería</option>
          <option>Impresión</option>
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Método de pago</label>
        <select class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" id="rp_pay">
          <option value="all">Todos</option>
          <option>Efectivo</option>
          <option>Transferencia</option>
          <option>Tarjeta</option>
          <option>Mixto</option>
          <option>Otro</option>
        </select>
      </div>
    </div>
    <div class="mt-3 flex items-center gap-3">
      <button class="rounded-xl bg-indigo-600 text-white px-4 py-2 font-semibold hover:bg-indigo-700">Aplicar</button>
      <button class="rounded-xl bg-white border px-4 py-2 hover:bg-slate-50">Limpiar</button>
      <span class="text-xs text-gray-500">* Solo UI demo.</span>
    </div>
  </div>

  {{-- KPIs --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Ingresos</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">vs período anterior: —</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">COGS</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">Costo de lo vendido</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Gastos</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">Operativos + compras sin stock</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Utilidad neta</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
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
        <button class="text-sm text-[color:var(--dw-primary)] hover:underline">Exportar CSV</button>
      </div>
      <div class="mt-3 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left">
              <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Cliente</th><th class="py-2 pr-4">Items</th><th class="py-2 pr-4">Total</th><th class="py-2">Método</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @for ($i=0; $i<6; $i++)
              <tr><td class="py-2 pr-4">—</td><td class="py-2 pr-4">—</td><td class="py-2 pr-4">—</td><td class="py-2 pr-4">$ 0</td><td class="py-2"><span class="px-2 py-1 rounded-full bg-slate-100">—</span></td></tr>
            @endfor
          </tbody>
        </table>
      </div>
    </div>

    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Gastos / Compras sin stock</h3>
        <button class="text-sm text-[color:var(--dw-primary)] hover:underline">Exportar CSV</button>
      </div>
      <div class="mt-3 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left">
              <th class="py-2 pr-4">Fecha</th><th class="py-2 pr-4">Concepto</th><th class="py-2 pr-4">Categoría</th><th class="py-2 pr-4">Monto</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @for ($i=0; $i<6; $i++)
              <tr><td class="py-2 pr-4">—</td><td class="py-2 pr-4">—</td><td class="py-2 pr-4">—</td><td class="py-2 pr-4">$ 0</td></tr>
            @endfor
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
  // Flujo de caja (línea)
  const c1 = document.getElementById('rp_cashflow');
  if (c1) new Chart(c1, {
    type: 'line',
    data: { labels: ['D1','D2','D3','D4','D5','D6','D7'],
      datasets: [{ label:'Entradas', data:[0,2,1,3,2,4,3], borderWidth:2, tension:.35 },
                 { label:'Salidas',  data:[0,1,1,1,2,1,2], borderWidth:2, tension:.35 }] },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ labels:{ boxWidth:10 }}}}
  });

  // Ventas por categoría (donut)
  const c2 = document.getElementById('rp_cat');
  if (c2) new Chart(c2, {
    type: 'doughnut',
    data: { labels:['Diseño','Papelería','Impresión'], datasets:[{ data:[30,45,25] }]},
    options: { responsive:true, plugins:{ legend:{ position:'bottom' }}}
  });
});
</script>
@endpush
