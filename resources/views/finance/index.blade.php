{{-- resources/views/finance/index.blade.php
     Finanzas — Resumen real de caja e inversiones
--}}
@extends('layouts.admin')

@section('title','Finanzas — DecoWandy')

@section('content')
  <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div>
      <h1 class="text-2xl font-bold">Finanzas</h1>
      <p class="text-sm text-gray-500">Resumen de caja, egresos e inversión con datos reales.</p>
    </div>
    <form method="GET" class="flex flex-wrap items-end gap-3 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Desde</label>
        <input type="date" name="from" value="{{ $rango['from'] }}" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
      </div>
      <div>
        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Hasta</label>
        <input type="date" name="to" value="{{ $rango['to'] }}" class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
      </div>
      <div class="flex gap-2">
        <button type="submit" class="h-10 rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow hover:bg-indigo-700">Aplicar</button>
        <a href="{{ route('finance.index') }}" class="h-10 rounded-xl border px-4 text-sm font-semibold text-gray-700 hover:bg-slate-50 flex items-center">Limpiar</a>
      </div>
    </form>
  </div>

  {{-- KPIs principales --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Caja estimada</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($resumen['caja'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Ingresos - egresos - inversión</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Inversión acumulada</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($resumen['invertido'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1"><a href="{{ route('finance.investments') }}" class="text-[color:var(--dw-primary)] hover:underline">Gestionar inversiones</a></div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Recuperado</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($resumen['recuperado'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">{{ $resumen['porcentaje_recuperado'] }}% recuperado</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Por recuperar</div>
      <div class="mt-1 text-2xl font-bold">${{ number_format($resumen['por_recuperar'], 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Utilidad periodo: ${{ number_format($resumen['utilidad'], 0, ',', '.') }}</div>
    </div>
  </div>

  {{-- Gráficas --}}
  <div class="mt-6 grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <h3 class="font-semibold">Flujo de caja ({{ $rango['from'] }} a {{ $rango['to'] }})</h3>
      <canvas id="fn_cashflow" class="mt-4"></canvas>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <h3 class="font-semibold">Ingresos vs egresos</h3>
      <canvas id="fn_rev_exp" class="mt-4"></canvas>
    </div>
  </div>

  {{-- Estado de caja --}}
  <div class="mt-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div>
        <h3 class="font-semibold">Movimientos de caja</h3>
        <p class="text-xs text-gray-500">Incluye ventas, gastos, compras sin inventario e inversiones.</p>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-500">
          <tr class="text-left">
            <th class="py-2 pr-4">Fecha</th>
            <th class="py-2 pr-4">Concepto</th>
            <th class="py-2 pr-4">Método</th>
            <th class="py-2 pr-4 text-right">Monto</th>
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
            <tr>
              <td colspan="4" class="py-4 text-center text-sm text-gray-500">No hay movimientos para el rango seleccionado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
<script defer>
document.addEventListener('DOMContentLoaded', () => {
  const cashflow = @json($cashflowDataset, JSON_UNESCAPED_UNICODE);
  const ingresosVsGastos = @json($ingresosVsGastosDataset, JSON_UNESCAPED_UNICODE);

  const cf = document.getElementById('fn_cashflow');
  if (cf && window.Chart) {
    new Chart(cf, {
      type:'line',
      data:{
        labels: cashflow.labels,
        datasets:[
          { label:'Entradas', data:cashflow.entradas, borderColor:'#22c55e', backgroundColor:'rgba(34,197,94,0.1)', tension:.35, borderWidth:2 },
          { label:'Salidas', data:cashflow.salidas, borderColor:'#f43f5e', backgroundColor:'rgba(244,63,94,0.1)', tension:.35, borderWidth:2 }
        ]
      },
      options:{ responsive:true, scales:{ y:{ beginAtZero:true }}, plugins:{ legend:{ labels:{ boxWidth:10 }}}}
    });
  }

  const re = document.getElementById('fn_rev_exp');
  if (re && window.Chart) {
    new Chart(re, {
      type:'bar',
      data:{ labels: ingresosVsGastos.labels, datasets:[{ label:'Monto', data: ingresosVsGastos.data, backgroundColor:['#6366f1','#f97316','#a855f7'] }]},
      options:{ responsive:true, scales:{ y:{ beginAtZero:true }}, plugins:{ legend:{ display:false }}}
    });
  }
});
</script>
@endpush
