@extends('layouts.admin')

@section('title','Ventas — DecoWandy')

@section('content')
  @php
    $categoryOptions = ['all' => 'Todas'] + $sectorLabels;
    $paymentLabels = [
      'cash' => 'Efectivo',
      'transfer' => 'Transferencia',
      'card' => 'Tarjeta',
      'mixed' => 'Mixto',
      'other' => 'Otro',
    ];
    $sectionIndicators = [
      'diseno' => 'linear-gradient(90deg, var(--dw-primary), var(--dw-yellow))',
      'papeleria' => 'linear-gradient(90deg, var(--dw-lilac), var(--dw-rose))',
      'impresion' => 'linear-gradient(90deg, var(--dw-lilac), var(--dw-primary))',
    ];
  @endphp

  {{-- Header --}}
  <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div>
      <h1 class="text-2xl font-bold">Ventas</h1>
      <p class="text-sm text-gray-500">Listado por categoría: Diseño, Papelería e Impresión.</p>
    </div>
    <div class="text-right text-sm text-gray-500">
      @if($filters['range']['label'])
        <span class="font-medium text-gray-700">{{ $filters['range']['label'] }}</span>
      @endif
      <div>Total general: <strong>${{ number_format($overallTotal, 0, ',', '.') }}</strong></div>
    </div>
  </div>

  {{-- Filtros --}}
  <form id="filters-form" method="GET" class="mb-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
    <div class="grid gap-4 md:grid-cols-4">
      {{-- Categoría --}}
      <div class="md:col-span-1">
        <label for="f_category" class="block text-sm text-gray-600 mb-1">Categoría</label>
        <select id="f_category"
                name="category"
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          @foreach($categoryOptions as $key => $label)
            <option value="{{ $key }}" @selected($filters['category'] === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      {{-- Tipo de fecha --}}
      <div class="md:col-span-1">
        <label for="f_date_type" class="block text-sm text-gray-600 mb-1">Por fecha</label>
        <select id="f_date_type"
                name="date_type"
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="day" @selected($filters['date_type'] === 'day')>Día</option>
          <option value="week" @selected($filters['date_type'] === 'week')>Semana</option>
          <option value="month" @selected($filters['date_type'] === 'month')>Mes</option>
        </select>
      </div>

      {{-- Día --}}
      <div class="md:col-span-1" id="f_day_wrap" style="display:none">
        <label for="f_day" class="block text-sm text-gray-600 mb-1">Selecciona día</label>
        <input id="f_day" name="day" type="date"
               value="{{ $filters['day'] }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      {{-- Semana (ISO) --}}
      <div class="md:col-span-1" id="f_week_wrap" style="display:none">
        <label for="f_week" class="block text-sm text-gray-600 mb-1">Selecciona semana</label>
        <input id="f_week" name="week" type="week"
               value="{{ $filters['week'] }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      {{-- Mes --}}
      <div class="md:col-span-1" id="f_month_wrap">
        <label for="f_month" class="block text-sm text-gray-600 mb-1">Selecciona mes</label>
        <input id="f_month" name="month" type="month"
               value="{{ $filters['month'] }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-3">
      <button type="submit"
              class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-700">
        Aplicar filtros
      </button>
      <a href="{{ route('sales.index') }}"
         class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-2 border hover:bg-slate-50">
        Limpiar
      </a>
    </div>
  </form>

  @forelse($sections as $key => $section)
    <section class="mb-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 shadow-sm">
      <div class="px-5 py-4 flex items-center justify-between">
        <h2 class="font-semibold flex items-center gap-2">
          <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:{{ $sectionIndicators[$key] ?? 'linear-gradient(90deg, var(--dw-primary), var(--dw-yellow))' }}"></span>
          {{ $section['label'] }}
        </h2>
        <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/30 text-[color:var(--dw-primary)]">{{ count($section['rows']) }} ventas</span>
      </div>
      <div class="px-5 pb-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left">
              <th class="py-2 pr-4">Fecha</th>
              <th class="py-2 pr-4">Cliente</th>
              <th class="py-2 pr-4">Ítems</th>
              <th class="py-2 pr-4">Total</th>
              <th class="py-2">Método</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($section['rows'] as $row)
              <tr>
                <td class="py-2 pr-4 whitespace-nowrap">
                  @php
                    $soldAt = $row['sale']->sold_at ?? $row['sale']->created_at;
                  @endphp
                  {{ optional($soldAt)->format('d/m/Y H:i') ?? '—' }}
                </td>
                <td class="py-2 pr-4">
                  <div class="font-medium text-gray-800">{{ $row['sale']->customer_name ?? '—' }}</div>
                  <div class="text-xs text-gray-500">{{ $row['sale']->customer_email ?? $row['sale']->customer_phone ?? '' }}</div>
                </td>
                <td class="py-2 pr-4">
                  <ul class="list-disc list-inside text-gray-700 space-y-0.5">
                    @foreach($row['item_names'] as $name)
                      <li>{{ $name }}</li>
                    @endforeach
                  </ul>
                  <div class="text-xs text-gray-500 mt-1">Cantidad total: {{ rtrim(rtrim(number_format($row['quantity'], 2, ',', '.'), '0'), ',') }}</div>
                </td>
                <td class="py-2 pr-4 font-semibold text-gray-900">${{ number_format($row['total'], 0, ',', '.') }}</td>
                <td class="py-2">
                  @php
                    $method = $row['sale']->payment_method ?? 'other';
                    $methodLabel = $paymentLabels[$method] ?? ucfirst($method);
                  @endphp
                  <span class="px-2 py-1 rounded-full bg-[color:var(--dw-rose)]/25 text-xs">{{ $methodLabel }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-6 text-center text-sm text-gray-500">Sin ventas registradas para este filtro.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="px-5 pb-4 border-t border-gray-100 text-sm flex items-center justify-between">
        <span class="text-gray-500">Total ventas ({{ $section['label'] }}):</span>
        <strong>${{ number_format($section['total'], 0, ',', '.') }}</strong>
      </div>
    </section>
  @empty
    <div class="rounded-2xl bg-white border border-dashed border-gray-300 p-10 text-center text-gray-500">
      No hay ventas registradas para los filtros seleccionados.
    </div>
  @endforelse
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const typeSel = document.getElementById('f_date_type');
    const dayWrap   = document.getElementById('f_day_wrap');
    const weekWrap  = document.getElementById('f_week_wrap');
    const monthWrap = document.getElementById('f_month_wrap');

    function refreshDateInputs() {
      const v = typeSel.value;
      dayWrap.style.display   = (v === 'day')   ? '' : 'none';
      weekWrap.style.display  = (v === 'week')  ? '' : 'none';
      monthWrap.style.display = (v === 'month') ? '' : 'none';
    }
    typeSel.addEventListener('change', refreshDateInputs);
    refreshDateInputs();
  });
</script>
@endpush
