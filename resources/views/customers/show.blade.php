@extends('layouts.admin')

@section('title','Cliente · '.$customer->name)

@section('content')
  <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <x-dw-page-header :title="$customer->name" :subtitle="'Cédula: '.($customer->document ?? 'N/A')" />
    <div class="flex gap-2">
      <x-dw-button variant="secondary" :href="route('customers.index')">Volver</x-dw-button>
      @if($customer->archived_at)
        <form method="POST" action="{{ route('customers.unarchive', ['customer' => $customer->getRouteKey()]) }}">
          @csrf
          <x-dw-button type="submit">Reactivar</x-dw-button>
        </form>
      @else
        <form method="POST" action="{{ route('customers.archive', ['customer' => $customer->getRouteKey()]) }}">
          @csrf
          <x-dw-button type="submit" variant="danger">Archivar</x-dw-button>
        </form>
      @endif
    </div>
  </div>

  <div class="mb-6 grid gap-4 md:grid-cols-3">
    <x-dw-card>
      <h3 class="mb-2 font-display text-sm font-semibold text-dw-text">Contacto</h3>
      <p class="text-sm text-dw-muted">Correo: {{ $customer->email ?? 'Sin correo' }}</p>
      <p class="text-sm text-dw-muted">Teléfono: {{ $customer->phone ?? 'Sin teléfono' }}</p>
      <p class="mt-2 text-sm text-dw-muted">Estado:
        @if($customer->archived_at)
          <x-dw-badge variant="danger">Archivado</x-dw-badge>
        @else
          <x-dw-badge variant="primary">Activo</x-dw-badge>
        @endif
      </p>
      <p class="mt-1 text-sm text-dw-muted">Última compra:
        {{ $customer->last_purchase_at ? $customer->last_purchase_at->format('d/m/Y H:i') : 'Sin compras' }}
      </p>
    </x-dw-card>
    <x-dw-card class="md:col-span-2">
      <h3 class="mb-2 font-display text-sm font-semibold text-dw-text">Notas</h3>
      <p class="whitespace-pre-line text-sm text-dw-muted">{{ $customer->notes ?? 'Sin notas' }}</p>
    </x-dw-card>
  </div>

  <x-dw-card padding="p-0" class="overflow-hidden">
    <div class="flex items-center justify-between border-b px-4 py-3 dw-hairline">
      <h3 class="font-display text-sm font-semibold text-dw-text">Historial de compras</h3>
      <span class="text-xs text-dw-muted">Ventas: {{ $sales->total() }}</span>
    </div>
    <div class="overflow-x-auto px-4 py-3">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Fecha</th>
            <th class="py-2 pr-4">Total</th>
            <th class="py-2 pr-4">Método</th>
            <th class="py-2">Ítems</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sales as $sale)
            <tr>
              <td class="whitespace-nowrap py-2 pr-4 text-dw-text">
                {{ optional($sale->sold_at ?? $sale->created_at)->format('d/m/Y H:i') }}
              </td>
              <td class="py-2 pr-4 font-semibold text-dw-text">${{ number_format($sale->total, 0, ',', '.') }}</td>
              <td class="py-2 pr-4"><x-dw-badge>{{ ucfirst($sale->payment_method) }}</x-dw-badge></td>
              <td class="py-2 text-sm text-dw-muted">
                <ul class="list-inside list-disc">
                  @foreach($sale->items as $item)
                    <li>{{ $item->description ?? $item->item->name ?? 'Ítem' }} (x{{ $item->quantity }})</li>
                  @endforeach
                </ul>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-6 text-center text-sm text-dw-muted">Sin compras registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($sales->hasPages())
      <div class="border-t px-4 py-3 dw-hairline">{{ $sales->links() }}</div>
    @endif
  </x-dw-card>
@endsection
