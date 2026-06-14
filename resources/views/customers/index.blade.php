@extends('layouts.admin')

@section('title', 'Clientes · DecoWandy')

@section('content')
  <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
    <x-dw-page-header title="Clientes" subtitle="Gestiona clientes, estado y últimas compras." />
    <a href="{{ route('customers.index') }}" class="dw-link">Refrescar</a>
  </div>

  <form method="GET" class="dw-filter-panel">
    <div class="grid gap-3 md:grid-cols-3">
      <div>
        <label class="dw-label mb-1" for="q">Buscar</label>
        <input id="q" name="q" type="text" value="{{ $search }}" placeholder="Nombre, cédula, email o teléfono" class="dw-input">
      </div>
      <div>
        <label class="dw-label mb-1" for="status">Estado</label>
        <select id="status" name="status" class="dw-select">
          <option value="active" @selected($status==='active')>Activos</option>
          <option value="archived" @selected($status==='archived')>Archivados</option>
          <option value="all" @selected($status==='all')>Todos</option>
        </select>
      </div>
      <div class="flex items-end gap-2">
        <x-dw-button type="submit">Filtrar</x-dw-button>
        <x-dw-button variant="secondary" :href="route('customers.index')">Limpiar</x-dw-button>
      </div>
    </div>
  </form>

  <x-dw-card padding="p-0" class="overflow-hidden">
    <div class="flex items-center justify-between border-b px-4 py-3 dw-hairline">
      <div class="text-sm text-dw-muted">Total: {{ $customers->total() }} clientes</div>
    </div>
    <div class="overflow-x-auto px-4 py-3">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Cliente</th>
            <th class="py-2 pr-4">Contacto</th>
            <th class="py-2 pr-4">Última compra</th>
            <th class="py-2 pr-4">Ventas</th>
            <th class="py-2">Estado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($customers as $customer)
            <tr>
              <td class="py-2 pr-4">
                <div class="font-semibold text-dw-text">{{ $customer->name }}</div>
                <div class="text-xs text-dw-muted">Cédula: {{ $customer->document ?? 'N/A' }}</div>
              </td>
              <td class="py-2 pr-4 text-xs text-dw-muted">
                <div>{{ $customer->email ?? 'Sin correo' }}</div>
                <div>{{ $customer->phone ?? 'Sin teléfono' }}</div>
              </td>
              <td class="py-2 pr-4 text-dw-text">
                @if($customer->last_purchase_at)
                  {{ $customer->last_purchase_at->format('d/m/Y H:i') }}
                @else
                  Sin compras
                @endif
              </td>
              <td class="py-2 pr-4 text-dw-text">{{ $customer->sales_count }}</td>
              <td class="py-2">
                <div class="flex flex-wrap items-center gap-2">
                  @if($customer->archived_at)
                    <x-dw-badge variant="danger">Archivado</x-dw-badge>
                  @else
                    <x-dw-badge variant="primary">Activo</x-dw-badge>
                  @endif
                  <a href="{{ route('customers.show', $customer) }}" class="dw-link">Historial</a>
                  @if($customer->archived_at)
                    <form method="POST" action="{{ route('customers.unarchive', $customer) }}">
                      @csrf
                      <button type="submit" class="dw-link text-dw-primary">Reactivar</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('customers.archive', $customer) }}">
                      @csrf
                      <button type="submit" class="dw-link text-dw-rose">Archivar</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-6 text-center text-dw-muted">Sin clientes con este filtro.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="border-t px-4 py-3 dw-hairline">
      {{ $customers->links() }}
    </div>
  </x-dw-card>
@endsection
