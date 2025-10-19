{{-- resources/views/finance/index.blade.php
     Finanzas — Resumen (UI demo)
--}}
@extends('layouts.admin')

@section('title','Finanzas — DecoWandy')

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Finanzas</h1>
    <p class="text-sm text-gray-500">Estado financiero general, caja e inversión.</p>
  </div>

  {{-- KPIs principales --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Caja actual</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">Efectivo + transferencias (según filtro)</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Inversión acumulada</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1"><a href="{{ route('finance.investments') }}" class="text-[color:var(--dw-primary)] hover:underline">Gestionar inversiones</a></div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Inversión por recuperar</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">% recuperado: 0%</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Utilidad neta (período)</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">Ingresos − COGS − Gastos</div>
    </div>
  </div>

  {{-- Filtros rápidos --}}
  <div class="mt-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
    <div class="grid gap-4 md:grid-cols-4">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Período</label>
        <select class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option>Este mes</option><option>Últimos 3 meses</option><option>Este año</option><option>Personalizado</option>
        </select>
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Desde</label>
        <input type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Hasta</label>
        <input type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div class="flex items-end">
        <button class="rounded-xl bg-indigo-600 text-white px-4 py-2 font-semibold hover:bg-indigo-700">Aplicar</button>
      </div>
    </div>
  </div>

  {{-- Gráficas --}}
  <div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <h3 class="font-semibold">Flujo de caja (mensual)</h3>
      <canvas id="fn_cashflow" class="mt-4"></canvas>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <h3 class="font-semibold">Ingresos vs Gastos</h3>
      <canvas id="fn_rev_exp" class="mt-4"></canvas>
    </div>
  </div>

  {{-- Tablas resumidas --}}
  <div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <h3 class="font-semibold mb-3">Estado de resultados (mes)</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left"><th class="py-2 pr-4">Concepto</th><th class="py-2 pr-4">COP</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr><td class="py-2 pr-4">Ingresos</td><td class="py-2 pr-4">$ 0</td></tr>
            <tr><td class="py-2 pr-4">COGS</td><td class="py-2 pr-4">$ 0</td></tr>
            <tr><td class="py-2 pr-4">Margen bruto</td><td class="py-2 pr-4">$ 0</td></tr>
            <tr><td class="py-2 pr-4">Gastos</td><td class="py-2 pr-4">$ 0</td></tr>
            <tr><td class="py-2 pr-4 font-semibold">Utilidad neta</td><td class="py-2 pr-4 font-semibold">$ 0</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Estado de caja (hoy)</h3>
        <button class="text-sm text-[color:var(--dw-primary)] hover:underline">Exportar CSV</button>
      </div>
      <div class="mt-3 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left"><th class="py-2 pr-4">Movimiento</th><th class="py-2 pr-4">Método</th><th class="py-2 pr-4">Monto</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @for ($i=0; $i<6; $i++)
              <tr><td class="py-2 pr-4">—</td><td class="py-2 pr-4">—</td><td class="py-2 pr-4">$ 0</td></tr>
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
  // Flujo de caja
  const c1 = document.getElementById('fn_cashflow');
  if (c1) new Chart(c1, {
    type:'line',
    data:{ labels:['Ene','Feb','Mar','Abr','May','Jun'],
      datasets:[{ label:'Entradas', data:[2,3,4,5,6,7], borderWidth:2, tension:.35 },
                { label:'Salidas',  data:[1,2,2,3,3,4], borderWidth:2, tension:.35 }]},
    options:{ responsive:true, scales:{ y:{ beginAtZero:true }}, plugins:{ legend:{ labels:{ boxWidth:10 }}}}
  });

  // Ingresos vs Gastos (barras)
  const c2 = document.getElementById('fn_rev_exp');
  if (c2) new Chart(c2, {
    type:'bar',
    data:{ labels:['Ene','Feb','Mar','Abr','May','Jun'],
      datasets:[{ label:'Ingresos', data:[5,6,7,6,8,9] }, { label:'Gastos', data:[3,3,4,4,5,5] }]},
    options:{ responsive:true, indexAxis:'x', scales:{ y:{ beginAtZero:true }}, plugins:{ legend:{ labels:{ boxWidth:10 }}}}
  });
});
</script>
@endpush
