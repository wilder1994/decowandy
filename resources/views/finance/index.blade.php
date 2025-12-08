{{-- resources/views/finance/index.blade.php --}}
@extends('layouts.admin')

@section('title','Finanzas · DecoWandy')

@section('content')
  <div class="mb-6 rounded-3xl bg-gradient-to-r from-indigo-600 via-indigo-500 to-amber-400 text-white p-6 shadow-xl">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-sm uppercase tracking-wider text-indigo-100">Panel financiero</p>
        <h1 class="text-3xl font-bold">Salud financiera y reportes</h1>
        <p class="text-sm text-indigo-100/90 mt-1">Flujo de caja, inversiones y movimientos conectados a ventas, compras y gastos reales.</p>
      </div>
      <form method="GET" class="flex flex-wrap items-end gap-3 bg-white/10 backdrop-blur rounded-2xl border border-white/20 p-4">
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide text-white/80">Desde</label>
          <input type="date" name="from" value="{{ $rango['from'] }}"
                 class="mt-1 w-full rounded-xl border-white/40 bg-white/20 text-sm text-white placeholder-white/70 focus:border-white focus:ring-white">
        </div>
        <div>
          <label class="block text-xs font-semibold uppercase tracking-wide text-white/80">Hasta</label>
          <input type="date" name="to" value="{{ $rango['to'] }}"
                 class="mt-1 w-full rounded-xl border-white/40 bg-white/20 text-sm text-white placeholder-white/70 focus:border-white focus:ring-white">
        </div>
        <div class="flex gap-2">
          <button type="submit"
                  class="h-10 rounded-xl bg-white text-indigo-700 px-4 text-sm font-semibold shadow hover:bg-indigo-50">
            Aplicar
          </button>
          <a href="{{ route('finance.index') }}"
             class="h-10 rounded-xl border border-white/60 px-4 text-sm font-semibold text-white hover:bg-white/10 flex items-center">
            Limpiar
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- KPIs principales --}}
  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl bg-white border border-indigo-50 p-4 shadow-md">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase tracking-wide text-indigo-600 font-semibold">Caja estimada</div>
          <div class="mt-1 text-3xl font-bold text-slate-900">${{ number_format($resumen['caja'], 0, ',', '.') }}</div>
          <div class="text-xs text-slate-500 mt-1">Ingresos - egresos - inversión</div>
        </div>
        <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">₵</div>
      </div>
    </div>

    <div class="rounded-2xl bg-white border border-violet-50 p-4 shadow-md">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase tracking-wide text-violet-600 font-semibold">Inversión</div>
          <div class="mt-1 text-3xl font-bold text-slate-900">${{ number_format($resumen['invertido'], 0, ',', '.') }}</div>
          <div class="text-xs text-violet-600 mt-1">
            <a href="{{ route('finance.investments') }}" class="hover:underline">Gestionar inversiones</a>
          </div>
        </div>
        <div class="h-10 w-10 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center font-bold">↑</div>
      </div>
    </div>

    <div class="rounded-2xl bg-white border border-emerald-50 p-4 shadow-md">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase tracking-wide text-emerald-600 font-semibold">Recuperado</div>
          <div class="mt-1 text-3xl font-bold text-slate-900">${{ number_format($resumen['recuperado'], 0, ',', '.') }}</div>
          <div class="text-xs text-emerald-600 mt-1">{{ $resumen['porcentaje_recuperado'] }}% recuperado</div>
        </div>
        <div class="h-10 w-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">✓</div>
      </div>
    </div>

    <div class="rounded-2xl bg-white border border-amber-50 p-4 shadow-md">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase tracking-wide text-amber-600 font-semibold">Por recuperar</div>
          <div class="mt-1 text-3xl font-bold text-slate-900">${{ number_format($resumen['por_recuperar'], 0, ',', '.') }}</div>
          <div class="text-xs text-amber-600 mt-1">Utilidad periodo: ${{ number_format($resumen['utilidad'], 0, ',', '.') }}</div>
        </div>
        <div class="h-10 w-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">↺</div>
      </div>
    </div>
  </div>

  {{-- Gráficas --}}
  <div class="mt-6 grid gap-4 lg:grid-cols-3">
    <div class="rounded-2xl bg-white border border-gray-100 p-5 shadow-md h-full">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-slate-900">Flujo de caja</h3>
          <p class="text-xs text-slate-500">Del {{ $rango['from'] }} al {{ $rango['to'] }}</p>
        </div>
        <span class="text-xs px-3 py-1 rounded-full bg-indigo-50 text-indigo-700">Tendencia</span>
      </div>
      <canvas id="fn_cashflow" class="mt-4 h-56"></canvas>
    </div>
    <div class="rounded-2xl bg-white border border-gray-100 p-5 shadow-md h-full">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-slate-900">Ingresos vs egresos</h3>
        <span class="text-xs px-3 py-1 rounded-full bg-violet-50 text-violet-700">Comparativo</span>
      </div>
      <canvas id="fn_rev_exp" class="mt-4 h-56"></canvas>
    </div>
    <div class="rounded-2xl bg-white border border-gray-100 p-5 shadow-md h-full flex items-center justify-center text-sm text-slate-500">
      <span class="text-center">Añade aquí tu tercer gráfico (ej. ventas por categoría) si lo tienes disponible.</span>
    </div>
  </div>

  {{-- Estado de caja --}}
  <div class="mt-6 rounded-2xl bg-white border border-gray-100 p-5 shadow-md">
    <div class="flex items-center justify-between mb-3">
      <div>
        <h3 class="font-semibold text-slate-900">Movimientos de caja</h3>
        <p class="text-xs text-gray-500">Ventas, gastos, compras sin inventario e inversiones.</p>
      </div>
      <span class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-700">{{ count($movimientosCaja) }} movimientos</span>
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
              <td class="py-2 pr-4 text-slate-800">{{ $mov['fecha']->format('d/m/Y') }}</td>
              <td class="py-2 pr-4 text-slate-700">{{ $mov['concepto'] }}</td>
              <td class="py-2 pr-4">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-slate-100 text-slate-700">
                  {{ ucfirst($mov['metodo']) }}
                </span>
              </td>
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
          { label:'Entradas', data:cashflow.entradas, borderColor:'#6366F1', backgroundColor:'rgba(99,102,241,0.12)', tension:.35, borderWidth:2, fill:true },
          { label:'Salidas', data:cashflow.salidas, borderColor:'#F43F5E', backgroundColor:'rgba(244,63,94,0.12)', tension:.35, borderWidth:2, fill:true }
        ]
      },
      options:{
        responsive:true,
        scales:{ y:{ beginAtZero:true, grid:{ color:'rgba(0,0,0,0.05)' }}, x:{ grid:{ display:false }}},
        plugins:{ legend:{ labels:{ boxWidth:10 }}}
      }
    });
  }

  const re = document.getElementById('fn_rev_exp');
  if (re && window.Chart) {
    new Chart(re, {
      type:'bar',
      data:{
        labels: ingresosVsGastos.labels,
        datasets:[{ label:'Monto', data: ingresosVsGastos.data, backgroundColor:['#6366F1','#F97316','#22C55E','#A855F7'] }]
      },
      options:{
        responsive:true,
        scales:{ y:{ beginAtZero:true, grid:{ color:'rgba(0,0,0,0.05)'}}, x:{ grid:{ display:false }}},
        plugins:{ legend:{ display:false }}
      }
    });
  }
});
</script>
@endpush
