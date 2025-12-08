@extends('layouts.admin')

@section('title','Clientes · DecoWandy')

@section('content')
  <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <div>
      <h1 class="text-2xl font-bold">Clientes</h1>
      <p class="text-sm text-gray-500">Gestiona clientes, estado y últimas compras.</p>
    </div>
    <a href="{{ route('customers.index') }}"
       class="text-sm text-indigo-600 hover:underline">Refrescar</a>
  </div>

  <form method="GET" class="mb-6 rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
    <div class="grid gap-4 md:grid-cols-3">
      <div>
        <label class="block text-sm text-gray-600 mb-1" for="q">Buscar</label>
        <input id="q" name="q" type="text" value="{{ $search }}"
               placeholder="Nombre, cédula, email o teléfono"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1" for="status">Estado</label>
        <select id="status" name="status"
                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="active" @selected($status==='active')>Activos</option>
          <option value="archived" @selected($status==='archived')>Archivados</option>
          <option value="all" @selected($status==='all')>Todos</option>
        </select>
      </div>
      <div class="flex items-end gap-3">
        <button type="submit"
                class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-700">
          Filtrar
        </button>
        <a href="{{ route('customers.index') }}"
           class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-2 border hover:bg-slate-50">
          Limpiar
        </a>
      </div>
    </div>
  </form>

  <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3 flex items-center justify-between">
      <div class="text-sm text-gray-500">Total: {{ $customers->total() }} clientes</div>
    </div>
    <div class="px-5 pb-4 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-500">
          <tr class="text-left">
            <th class="py-2 pr-4">Cliente</th>
            <th class="py-2 pr-4">Contacto</th>
            <th class="py-2 pr-4">Última compra</th>
            <th class="py-2 pr-4">Ventas</th>
            <th class="py-2">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($customers as $customer)
            <tr>
              <td class="py-2 pr-4">
                <div class="font-semibold text-gray-800">{{ $customer->name }}</div>
                <div class="text-xs text-gray-500">Cédula: {{ $customer->document ?? 'N/A' }}</div>
              </td>
              <td class="py-2 pr-4 text-xs text-gray-600">
                <div>{{ $customer->email ?? 'Sin correo' }}</div>
                <div>{{ $customer->phone ?? 'Sin teléfono' }}</div>
              </td>
              <td class="py-2 pr-4 text-sm text-gray-700">
                @if($customer->last_purchase_at)
                  {{ $customer->last_purchase_at->format('d/m/Y H:i') }}
                @else
                  Sin compras
                @endif
              </td>
              <td class="py-2 pr-4 text-sm text-gray-700">
                {{ $customer->sales_count }}
              </td>
              <td class="py-2 flex flex-wrap items-center gap-2">
                @if($customer->archived_at)
                  <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-xs">Archivado</span>
                @else
                  <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs">Activo</span>
                @endif
                <a href="{{ route('customers.show', $customer) }}"
                   class="text-indigo-600 text-xs font-semibold hover:underline">Historial</a>
                @if($customer->archived_at)
                  <form method="POST" action="{{ route('customers.unarchive', $customer) }}">
                    @csrf
                    <button class="text-xs text-emerald-700 hover:underline">Reactivar</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('customers.archive', $customer) }}">
                    @csrf
                    <button class="text-xs text-rose-700 hover:underline">Archivar</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-sm text-gray-500">Sin clientes con este filtro.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 pb-4">
      {{ $customers->links() }}
    </div>
  </div>
@endsection
