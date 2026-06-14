{{-- resources/views/finance/index.blade.php --}}
@extends('layouts.admin')

@section('title','Finanzas · DecoWandy')

@section('content')
  <div class="mb-6 rounded-dw-lg brand-gradient p-5 text-white shadow-dw-neon">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <p class="text-xs uppercase tracking-wider text-white/80">Panel financiero</p>
        <h1 class="font-display text-2xl font-bold">Salud financiera y reportes</h1>
        <p class="mt-1 text-sm text-white/90">Flujo de caja, inversiones y movimientos conectados a ventas, compras y gastos reales.</p>
      </div>
      <form method="GET" class="flex flex-wrap items-end gap-3 rounded-dw border-hairline border-white/20 bg-white/10 p-4 backdrop-blur">
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-white/80">Desde</label>
          <input type="date" name="from" value="{{ $rango['from'] }}"
                 class="dw-input border-white/30 bg-white/15 text-white placeholder-white/60">
        </div>
        <div>
          <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-white/80">Hasta</label>
          <input type="date" name="to" value="{{ $rango['to'] }}"
                 class="dw-input border-white/30 bg-white/15 text-white placeholder-white/60">
        </div>
        <div class="flex gap-2">
          <x-dw-button type="submit" class="!bg-white !text-dw-primary-dark hover:!opacity-90">Aplicar</x-dw-button>
          <x-dw-button variant="secondary" :href="route('finance.index')" class="!border-white/50 !bg-transparent !text-white hover:!bg-white/10">Limpiar</x-dw-button>
        </div>
      </form>
    </div>
  </div>

  <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-dw-kpi label="Caja estimada" :value="'$'.number_format($resumen['caja'], 0, ',', '.')" hint="Ingresos - egresos - inversión" icon="account_balance_wallet" />
    <x-dw-kpi label="Inversión" :value="'$'.number_format($resumen['invertido'], 0, ',', '.')" tone="lilac" icon="savings" hint="Ver módulo de inversiones" />
    <x-dw-kpi label="Recuperado" :value="'$'.number_format($resumen['recuperado'], 0, ',', '.')" tone="yellow" icon="check_circle" :hint="$resumen['porcentaje_recuperado'].'% recuperado'" />
    <x-dw-kpi label="Por recuperar" :value="'$'.number_format($resumen['por_recuperar'], 0, ',', '.')" tone="rose" icon="hourglass_top" :hint="'Utilidad periodo: $'.number_format($resumen['utilidad'], 0, ',', '.')" />
  </div>

  <div class="mt-6 grid gap-4 lg:grid-cols-3">
    <x-dw-card padding="p-4" class="h-full">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-display text-sm font-semibold text-dw-text">Flujo de caja</h3>
          <p class="text-xs text-dw-muted">Del {{ $rango['from'] }} al {{ $rango['to'] }}</p>
        </div>
        <x-dw-badge>Tendencia</x-dw-badge>
      </div>
      <canvas id="fn_cashflow" class="mt-4 h-56"></canvas>
    </x-dw-card>
    <x-dw-card padding="p-4" class="h-full">
      <div class="flex items-center justify-between">
        <h3 class="font-display text-sm font-semibold text-dw-text">Ingresos vs egresos</h3>
        <x-dw-badge>Comparativo</x-dw-badge>
      </div>
      <canvas id="fn_rev_exp" class="mt-4 h-56"></canvas>
    </x-dw-card>
    <x-dw-card padding="p-4" class="flex h-full items-center justify-center text-sm text-dw-muted">
      <span class="text-center">Añade aquí tu tercer gráfico (ej. ventas por categoría) si lo tienes disponible.</span>
    </x-dw-card>
  </div>

  <x-dw-card padding="p-4" class="mt-6">
    <div class="mb-3 flex items-center justify-between">
      <div>
        <h3 class="font-display text-sm font-semibold text-dw-text">Movimientos de caja</h3>
        <p class="text-xs text-dw-muted">Ventas, gastos, compras sin inventario e inversiones.</p>
      </div>
      <x-dw-badge>{{ count($movimientosCaja) }} movimientos</x-dw-badge>
    </div>
    <div class="overflow-x-auto">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Fecha</th>
            <th class="py-2 pr-4">Concepto</th>
            <th class="py-2 pr-4">Método</th>
            <th class="py-2 pr-4 text-right">Monto</th>
          </tr>
        </thead>
        <tbody>
          @forelse($movimientosCaja as $mov)
            <tr>
              <td class="py-2 pr-4 text-dw-text">{{ $mov['fecha']->format('d/m/Y') }}</td>
              <td class="py-2 pr-4 text-dw-text">{{ $mov['concepto'] }}</td>
              <td class="py-2 pr-4">
                <x-dw-badge>{{ ucfirst($mov['metodo']) }}</x-dw-badge>
              </td>
              <td class="py-2 pr-4 text-right font-semibold {{ $mov['tipo'] === 'entrada' ? 'text-dw-primary' : 'text-dw-rose' }}">
                {{ $mov['tipo'] === 'entrada' ? '+' : '-' }}${{ number_format(abs($mov['monto']), 0, ',', '.') }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-4 text-center text-sm text-dw-muted">No hay movimientos para el rango seleccionado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-dw-card>
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
