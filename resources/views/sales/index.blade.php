{{-- resources/views/sales/index.blade.php
     Índice de Ventas — solo diseño
     - Tablas a todo el ancho, apiladas
     - Filtros por Categoría y Fecha (día/semana/mes) — UI
--}}
@extends('layouts.admin')

@section('title','Ventas — DecoWandy')

@section('content')

  {{-- Header --}}
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Ventas</h1>
    <p class="text-sm text-gray-500">Listado por categoría: Diseño, Papelería e Impresión.</p>
  </div>

  {{-- Filtros (solo UI) --}}
  <div class="mb-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
    <div class="grid gap-4 md:grid-cols-4">
      {{-- Categoría --}}
      <div class="md:col-span-1">
        <label class="block text-sm text-gray-600 mb-1">Categoría</label>
        <select id="f_category"
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="all">Todas</option>
          <option value="Diseño">Diseño</option>
          <option value="Papelería">Papelería</option>
          <option value="Impresión">Impresión</option>
        </select>
      </div>

      {{-- Tipo de fecha --}}
      <div class="md:col-span-1">
        <label class="block text-sm text-gray-600 mb-1">Por fecha</label>
        <select id="f_date_type"
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="day">Día</option>
          <option value="week">Semana</option>
          <option value="month" selected>Mes</option>
        </select>
      </div>

      {{-- Día --}}
      <div class="md:col-span-1" id="f_day_wrap" style="display:none">
        <label class="block text-sm text-gray-600 mb-1">Selecciona día</label>
        <input id="f_day" type="date"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      {{-- Semana (ISO) --}}
      <div class="md:col-span-1" id="f_week_wrap" style="display:none">
        <label class="block text-sm text-gray-600 mb-1">Selecciona semana</label>
        <input id="f_week" type="week"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>

      {{-- Mes --}}
      <div class="md:col-span-1" id="f_month_wrap">
        <label class="block text-sm text-gray-600 mb-1">Selecciona mes</label>
        <input id="f_month" type="month"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>
    </div>

    <div class="mt-4 flex items-center gap-3">
      <button type="button"
              class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-700">
        Aplicar filtros
      </button>
      <button type="button"
              class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-2 border hover:bg-slate-50">
        Limpiar
      </button>
      <span class="text-xs text-gray-500">* Solo diseño (aún sin consultas reales).</span>
    </div>
  </div>

  {{-- Tabla: DISEÑO --}}
  <section class="mb-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 shadow-sm">
    <div class="px-5 py-4 flex items-center justify-between">
      <h2 class="font-semibold flex items-center gap-2">
        <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:linear-gradient(90deg, var(--dw-primary), var(--dw-yellow))"></span>
        Diseño
      </h2>
      <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/30 text-[color:var(--dw-primary)]">Demo</span>
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
          @for ($i=0; $i<8; $i++)
            <tr>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">$ 0</td>
              <td class="py-2"><span class="px-2 py-1 rounded-full bg-[color:var(--dw-rose)]/25">—</span></td>
            </tr>
          @endfor
        </tbody>
      </table>
    </div>
    <div class="px-5 pb-4 border-t border-gray-100 text-sm flex items-center justify-between">
      <span class="text-gray-500">Total ventas (diseño):</span>
      <strong>$ 0</strong>
    </div>
  </section>

  {{-- Tabla: PAPELERÍA --}}
  <section class="mb-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 shadow-sm">
    <div class="px-5 py-4 flex items-center justify-between">
      <h2 class="font-semibold flex items-center gap-2">
        <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:linear-gradient(90deg, var(--dw-lilac), var(--dw-rose))"></span>
        Papelería
      </h2>
      <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/30 text-[color:var(--dw-primary)]">Demo</span>
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
          @for ($i=0; $i<8; $i++)
            <tr>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">$ 0</td>
              <td class="py-2"><span class="px-2 py-1 rounded-full bg-[color:var(--dw-yellow)]/40">—</span></td>
            </tr>
          @endfor
        </tbody>
      </table>
    </div>
    <div class="px-5 pb-4 border-t border-gray-100 text-sm flex items-center justify-between">
      <span class="text-gray-500">Total ventas (papelería):</span>
      <strong>$ 0</strong>
    </div>
  </section>

  {{-- Tabla: IMPRESIÓN --}}
  <section class="mb-2 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 shadow-sm">
    <div class="px-5 py-4 flex items-center justify-between">
      <h2 class="font-semibold flex items-center gap-2">
        <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:linear-gradient(90deg, var(--dw-lilac), var(--dw-primary))"></span>
        Impresión
      </h2>
      <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/30 text-[color:var(--dw-primary)]">Demo</span>
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
          @for ($i=0; $i<8; $i++)
            <tr>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">—</td>
              <td class="py-2 pr-4">$ 0</td>
              <td class="py-2"><span class="px-2 py-1 rounded-full bg-[color:var(--dw-rose)]/25">—</span></td>
            </tr>
          @endfor
        </tbody>
      </table>
    </div>
    <div class="px-5 pb-4 border-t border-gray-100 text-sm flex items-center justify-between">
      <span class="text-gray-500">Total ventas (impresión):</span>
      <strong>$ 0</strong>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  // Mostrar los campos de fecha según el tipo: day/week/month (solo UI)
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
    refreshDateInputs(); // init
  });
</script>
@endpush

