{{-- resources/views/expenses/index.blade.php
     Gastos — Solo diseño (UI demo)
--}}
@extends('layouts.admin')

@section('title','Gastos — DecoWandy')

@section('content')
  @if (session('status'))
    <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-800">
      {{ session('status') }}
    </div>
  @endif
  {{-- Encabezado --}}
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Gastos</h1>
      <p class="text-sm text-gray-500">Registra y visualiza los gastos operativos de la empresa.</p>
    </div>
    <button id="btnNewExpense"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white brand-gradient shadow hover:opacity-90">
      <span class="material-symbols-outlined text-base">add</span> Registrar gasto
    </button>
  </div>

  {{-- Tarjetas resumen --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Total del mes</div>
        <div class="mt-1 text-2xl font-bold">
        $ {{ number_format($totalMonth, 0, ',', '.') }}
      </div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Promedio diario</div>
        <div class="mt-1 text-2xl font-bold">
        $ {{ number_format($avgDaily, 0, ',', '.') }}
      </div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Categoría con más gasto</div>
        <div class="mt-1 text-2xl font-bold text-[color:var(--dw-primary)]">
          {{ $topCategory ?? '—' }}
        </div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
      <div class="text-sm text-gray-500">Último gasto</div>
        <div class="mt-1 text-2xl font-bold">
          @if($lastExpense)
            $ {{ number_format($lastExpense->amount, 0, ',', '.') }}
          @else
            $ 0
          @endif
        </div>
    </div>
  </div>

  {{-- Filtros --}}
  <div class="mb-6 rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
    <form method="GET" action="{{ route('expenses.index') }}">
      <div class="grid gap-4 md:grid-cols-4">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Desde</label>
          <input
            type="date"
            name="from"
            value="{{ request('from') }}"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Hasta</label>
          <input
            type="date"
            name="to"
            value="{{ request('to') }}"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Categoría</label>
          <select
            name="category"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
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
          <button
            type="submit"
            class="rounded-xl bg-indigo-600 text-white px-4 py-2 font-semibold hover:bg-indigo-700 w-full">
            Aplicar filtros
          </button>
        </div>
      </div>
    </form>
  </div>


  {{-- Tabla --}}
  <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-500">
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
              <td class="py-2 pr-4 text-gray-500">
                {{ $expense->note }}
              </td>
              <td class="py-2 text-right">
                <button class="text-[color:var(--dw-primary)] hover:underline mr-2">Editar</button>
                <button class="text-rose-500 hover:underline">Eliminar</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-4 text-center text-gray-500">
                Aún no hay gastos registrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal registrar gasto --}}
  <div id="expenseModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="relative mx-auto mt-16 w-[min(640px,92%)] rounded-2xl bg-white p-5 shadow-2xl">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold">Registrar gasto</h2>
        <button id="expClose" class="h-9 w-9 rounded-full hover:bg-gray-100 flex items-center justify-center">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <form method="POST" action="{{ route('api.expenses.store') }}">
        @csrf
        <div class="grid gap-4 md:grid-cols-2">
          <div>
           <label class="block text-sm text-gray-600 mb-1">Fecha</label>
            <input
              type="date"
              name="date"
              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Concepto</label>
            <input
              type="text"
              name="concept"
              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Ej: transporte, servicios...">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Categoría</label>
            <select
              name="category"
              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
              <option value="Servicios">Servicios</option>
              <option value="Transporte">Transporte</option>
              <option value="Mantenimiento">Mantenimiento</option>
              <option value="Publicidad">Publicidad</option>
              <option value="Otros">Otros</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Valor (COP)</label>
            <input
              type="number"
              name="amount"
              id="expAmount"
              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="0"
              min="0">
          </div>
          <div>
            <label class="block text-sm text-gray-600 mb-1">Notas</label>
            <input
              type="text"
              name="note"
              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          </div>
        </div>
        <div class="mt-5 flex justify-end gap-3">
          <button id="expCancel" type="button" class="rounded-xl bg-white px-4 py-2 border hover:bg-slate-50">
            Cancelar
          </button>
          <button
            type="submit"
            class="rounded-xl bg-indigo-600 text-white px-4 py-2 shadow hover:bg-indigo-700">
            Guardar gasto
          </button>
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
