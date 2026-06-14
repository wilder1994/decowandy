@extends('layouts.admin')

@section('title', 'Ventas — DecoWandy')

@section('content')
  @php
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
    $tabOrder = ['impresion', 'papeleria', 'diseno'];
  @endphp

  <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <x-dw-page-header title="Ventas" subtitle="Listado por categoría: Diseño, Papelería e Impresión." />
    <div class="text-right text-sm text-dw-muted">
      @if($filters['range']['label'])
        <span class="font-medium text-dw-text">{{ $filters['range']['label'] }}</span>
      @endif
      <div>Total general: <strong class="text-dw-text">${{ number_format($overallTotal, 0, ',', '.') }}</strong></div>
    </div>
  </div>

  <div id="saleToast" class="hidden dw-toast">
    <span class="material-symbols-outlined text-base">check_circle</span>
    <span id="saleToastText">Venta registrada.</span>
  </div>

  <div class="mb-3 flex flex-wrap gap-2">
    @foreach($tabOrder as $tabKey)
      @continue(!isset($sections[$tabKey]))
      <button type="button" data-section-tab="{{ $tabKey }}" class="dw-tab" data-active="false">
        {{ $sections[$tabKey]['label'] ?? ucfirst($tabKey) }}
      </button>
    @endforeach
  </div>

  <form id="filters-form" method="GET" class="dw-filter-panel">
    <div class="grid gap-3 md:grid-cols-5">
      <div>
        <label for="f_date_type" class="dw-label mb-1">Por fecha</label>
        <select id="f_date_type" name="date_type" class="dw-select">
          <option value="day" @selected($filters['date_type'] === 'day')>Día</option>
          <option value="week" @selected($filters['date_type'] === 'week')>Semana</option>
          <option value="month" @selected($filters['date_type'] === 'month')>Mes</option>
        </select>
      </div>
      <div id="f_day_wrap" style="display:none">
        <label for="f_day" class="dw-label mb-1">Selecciona día</label>
        <input id="f_day" name="day" type="date" value="{{ $filters['day'] }}" class="dw-input">
      </div>
      <div id="f_week_wrap" style="display:none">
        <label for="f_week" class="dw-label mb-1">Selecciona semana</label>
        <input id="f_week" name="week" type="week" value="{{ $filters['week'] }}" class="dw-input">
      </div>
      <div id="f_month_wrap">
        <label for="f_month" class="dw-label mb-1">Selecciona mes</label>
        <input id="f_month" name="month" type="month" value="{{ $filters['month'] }}" class="dw-input">
      </div>
      <div class="flex flex-wrap items-end justify-end gap-2 md:col-span-1">
        <x-dw-button type="submit">Aplicar filtros</x-dw-button>
        <x-dw-button variant="secondary" :href="route('sales.index')">Limpiar</x-dw-button>
      </div>
    </div>
  </form>

  @forelse($sections as $key => $section)
    <section class="dw-section-panel" data-section-block="{{ $key }}" style="{{ $key === 'impresion' ? '' : 'display:none;' }}">
      <div class="flex items-center justify-between border-b px-4 py-3 dw-hairline">
        <h2 class="flex items-center gap-2 font-display text-sm font-semibold">
          <span class="inline-block h-2 w-2 rounded-full" style="background:{{ $sectionIndicators[$key] ?? 'linear-gradient(90deg, var(--dw-primary), var(--dw-yellow))' }}"></span>
          {{ $section['label'] }}
        </h2>
        <x-dw-badge>{{ count($section['rows']) }} ventas</x-dw-badge>
      </div>
      <div class="overflow-x-auto px-4 py-3">
        <table class="dw-table min-w-full text-sm">
          <thead>
            <tr class="text-left">
              <th class="py-2 pr-4">Fecha</th>
              <th class="py-2 pr-4">Cliente</th>
              <th class="py-2 pr-4">Ítems</th>
              <th class="py-2 pr-4">Total</th>
              <th class="py-2 pr-4">Método</th>
              <th class="py-2 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($section['rows'] as $row)
              <tr>
                <td class="whitespace-nowrap py-2 pr-4 text-dw-muted">
                  @php $soldAt = $row['sale']->sold_at ?? $row['sale']->created_at; @endphp
                  {{ optional($soldAt)->format('d/m/Y H:i') ?? '—' }}
                </td>
                <td class="py-2 pr-4">
                  <div class="font-medium text-dw-text">{{ $row['sale']->customer_name ?? '—' }}</div>
                  <div class="text-xs text-dw-muted">{{ $row['sale']->customer_email ?? $row['sale']->customer_phone ?? '' }}</div>
                </td>
                <td class="py-2 pr-4">
                  <ul class="list-inside list-disc space-y-0.5 text-dw-text">
                    @foreach($row['item_names'] as $name)
                      <li>{{ $name }}</li>
                    @endforeach
                  </ul>
                  <div class="mt-1 text-xs text-dw-muted">Cantidad total: {{ rtrim(rtrim(number_format($row['quantity'], 2, ',', '.'), '0'), ',') }}</div>
                </td>
                <td class="py-2 pr-4 font-semibold text-dw-text">${{ number_format($row['total'], 0, ',', '.') }}</td>
                <td class="py-2 pr-4">
                  @php
                    $method = $row['sale']->payment_method ?? 'other';
                    $methodLabel = $paymentLabels[$method] ?? ucfirst($method);
                  @endphp
                  <x-dw-badge variant="danger">{{ $methodLabel }}</x-dw-badge>
                </td>
                <td class="py-2 text-right">
                  <a href="{{ route('sales.show', $row['sale']->id) }}" class="dw-link">Ver factura</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-6 text-center text-dw-muted">Sin ventas registradas para este filtro.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="flex items-center justify-between border-t px-4 py-3 text-sm dw-hairline">
        <span class="text-dw-muted">Total ventas ({{ $section['label'] }}):</span>
        <strong class="text-dw-text">${{ number_format($section['total'], 0, ',', '.') }}</strong>
      </div>
    </section>
  @empty
    <div class="dw-empty">No hay ventas registradas para los filtros seleccionados.</div>
  @endforelse
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const typeSel = document.getElementById('f_date_type');
    const dayWrap = document.getElementById('f_day_wrap');
    const weekWrap = document.getElementById('f_week_wrap');
    const monthWrap = document.getElementById('f_month_wrap');

    function refreshDateInputs() {
      const v = typeSel.value;
      dayWrap.style.display = (v === 'day') ? '' : 'none';
      weekWrap.style.display = (v === 'week') ? '' : 'none';
      monthWrap.style.display = (v === 'month') ? '' : 'none';
    }
    typeSel.addEventListener('change', refreshDateInputs);
    refreshDateInputs();

    const tabButtons = document.querySelectorAll('[data-section-tab]');
    const sectionBlocks = document.querySelectorAll('[data-section-block]');
    function setActiveSection(key) {
      tabButtons.forEach(btn => {
        btn.dataset.active = (btn.getAttribute('data-section-tab') === key) ? 'true' : 'false';
      });
      sectionBlocks.forEach(block => {
        block.style.display = (block.getAttribute('data-section-block') === key) ? '' : 'none';
      });
    }
    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => setActiveSection(btn.getAttribute('data-section-tab')));
    });
    setActiveSection('impresion');

    @if(request('open') === 'create')
    window.addEventListener('load', () => {
      if (typeof openModal === 'function') {
        openModal();
      } else {
        document.getElementById('saleModal')?.classList.remove('hidden');
      }
    });
    @endif
  });
</script>
@endpush
