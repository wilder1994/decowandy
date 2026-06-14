{{-- resources/views/expenses/index.blade.php
     Gastos — Solo diseño (UI demo)
--}}
@extends('layouts.admin')

@section('title','Gastos — DecoWandy')

@section('content')
  @if (session('status'))
    <div class="dw-alert-success">{{ session('status') }}</div>
  @endif

  <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <x-dw-page-header title="Gastos" subtitle="Registra y visualiza los gastos operativos de la empresa." />
    <x-dw-button id="btnNewExpense" type="button">
      <span class="material-symbols-outlined text-base">add</span>
      Registrar gasto
    </x-dw-button>
  </div>

  <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <x-dw-kpi label="Total del mes" :value="'$ '.number_format($totalMonth, 0, ',', '.')" />
    <x-dw-kpi label="Promedio diario" :value="'$ '.number_format($avgDaily, 0, ',', '.')" tone="yellow" />
    <x-dw-kpi label="Categoría con más gasto" :value="$topCategory ?? '—'" tone="lilac" />
    <x-dw-kpi label="Último gasto" :value="$lastExpense ? '$ '.number_format($lastExpense->amount, 0, ',', '.') : '$ 0'" tone="rose" />
  </div>

  <form method="GET" action="{{ route('expenses.index') }}" class="dw-filter-panel mb-6">
    <div class="grid gap-3 md:grid-cols-4">
      <div>
        <label class="dw-label mb-1" for="from">Desde</label>
        <input id="from" type="date" name="from" value="{{ request('from') }}" class="dw-input">
      </div>
      <div>
        <label class="dw-label mb-1" for="to">Hasta</label>
        <input id="to" type="date" name="to" value="{{ request('to') }}" class="dw-input">
      </div>
      <div>
        <label class="dw-label mb-1" for="category">Categoría</label>
        <select id="category" name="category" class="dw-select">
            @php
              $currentCategory = request('category', 'all');
            @endphp
            <option value="all" {{ $currentCategory === 'all' ? 'selected' : '' }}>Todas</option>
            <option value="Servicios" {{ $currentCategory === 'Servicios' ? 'selected' : '' }}>Servicios</option>
            <option value="Transporte" {{ $currentCategory === 'Transporte' ? 'selected' : '' }}>Transporte</option>
            <option value="Mantenimiento" {{ $currentCategory === 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
            <option value="Publicidad" {{ $currentCategory === 'Publicidad' ? 'selected' : '' }}>Publicidad</option>
            <option value="Otros" {{ $currentCategory === 'Otros' ? 'selected' : '' }}>Otros</option>
          </select>
        </div>
        <div class="flex items-end">
          <x-dw-button type="submit" class="w-full">Aplicar filtros</x-dw-button>
        </div>
      </div>
  </form>

  <x-dw-card padding="p-0" class="overflow-hidden">
    <div class="overflow-x-auto px-4 py-3">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Fecha</th>
            <th class="py-2 pr-4">Concepto</th>
            <th class="py-2 pr-4">Categoría</th>
            <th class="py-2 pr-4">Monto (COP)</th>
            <th class="py-2 pr-4">Notas</th>
            <th class="py-2 text-right w-28">Acciones</th>
          </tr>
        </thead>
        <tbody id="expenseTable" class="divide-y divide-gray-100">
          @forelse($expenses as $expense)
            <tr>
              <td class="py-2 pr-4">
                {{ $expense->date }}
              </td>
              <td class="py-2 pr-4">
                {{ $expense->concept }}
              </td>
              <td class="py-2 pr-4">
                {{ $expense->category }}
              </td>
              <td class="py-2 pr-4">
                $ {{ number_format($expense->amount, 0, ',', '.') }}
              </td>
              <td class="py-2 pr-4 text-dw-muted">{{ $expense->note }}</td>
              <td class="py-2 text-right">
                <button class="dw-link mr-2">Editar</button>
                <button class="text-dw-rose hover:underline text-xs font-semibold">Eliminar</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-4 text-center text-dw-muted">
                Aún no hay gastos registrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-dw-card>

  <div id="expenseModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative mx-auto mt-16 w-[min(640px,92%)] rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="font-display text-xl font-semibold text-dw-text">Registrar gasto</h2>
        <button id="expClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <form method="POST" action="{{ route('api.expenses.store') }}">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="dw-label mb-1">Fecha</label>
            <input type="date" name="date" class="dw-input">
          </div>
          <div>
            <label class="dw-label mb-1">Concepto</label>
            <input type="text" name="concept" class="dw-input" placeholder="Ej: transporte, servicios...">
          </div>
          <div>
            <label class="dw-label mb-1">Categoría</label>
            <select name="category" class="dw-select">
              <option value="Servicios">Servicios</option>
              <option value="Transporte">Transporte</option>
              <option value="Mantenimiento">Mantenimiento</option>
              <option value="Publicidad">Publicidad</option>
              <option value="Otros">Otros</option>
            </select>
          </div>
          <div>
            <label class="dw-label mb-1">Valor (COP)</label>
            <input type="number" name="amount" id="expAmount" class="dw-input" placeholder="0" min="0">
          </div>
          <div>
            <label class="dw-label mb-1">Notas</label>
            <input type="text" name="note" class="dw-input">
          </div>
        </div>
        <div class="mt-5 flex justify-end gap-2">
          <x-dw-button id="expCancel" type="button" variant="secondary">Cancelar</x-dw-button>
          <x-dw-button type="submit">Guardar gasto</x-dw-button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  const modal=document.getElementById('expenseModal');
  const btn=document.getElementById('btnNewExpense');
  const close=document.getElementById('expClose');
  const cancel=document.getElementById('expCancel');
  [btn,close,cancel].forEach(b=>b&&b.addEventListener('click',()=>modal.classList.toggle('hidden')));
});
</script>
@endpush
