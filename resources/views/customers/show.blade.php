@extends('layouts.admin')

@section('title','Cliente · '.$customer->name)

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">{{ $customer->name }}</h1>
      <p class="text-sm text-gray-500">Cédula: {{ $customer->document ?? 'N/A' }}</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('customers.index') }}" class="text-sm text-indigo-600 hover:underline">Volver</a>
      @if($customer->archived_at)
        <form method="POST" action="{{ route('customers.unarchive', ['customer' => $customer->getRouteKey()]) }}">
          @csrf
          <button class="rounded-xl bg-emerald-600 text-white text-sm font-semibold px-3 py-2">Reactivar</button>
        </form>
      @else
        <form method="POST" action="{{ route('customers.archive', ['customer' => $customer->getRouteKey()]) }}">
          @csrf
          <button class="rounded-xl bg-rose-600 text-white text-sm font-semibold px-3 py-2">Archivar</button>
        </form>
      @endif
    </div>
  </div>

  <div class="grid gap-4 md:grid-cols-3 mb-6">
    <div class="rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
      <h3 class="font-semibold text-gray-800 mb-2">Contacto</h3>
      <p class="text-sm text-gray-600">Correo: {{ $customer->email ?? 'Sin correo' }}</p>
      <p class="text-sm text-gray-600">Teléfono: {{ $customer->phone ?? 'Sin teléfono' }}</p>
      <p class="text-sm text-gray-600">Estado:
        @if($customer->archived_at)
          <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-700 text-xs">Archivado</span>
        @else
          <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs">Activo</span>
        @endif
      </p>
      <p class="text-sm text-gray-600">Última compra:
        {{ $customer->last_purchase_at ? $customer->last_purchase_at->format('d/m/Y H:i') : 'Sin compras' }}
      </p>
    </div>
    <div class="md:col-span-2 rounded-2xl bg-white border border-gray-100 p-4 shadow-sm">
      <h3 class="font-semibold text-gray-800 mb-3">Notas</h3>
      <p class="text-sm text-gray-600 whitespace-pre-line">{{ $customer->notes ?? 'Sin notas' }}</p>
    </div>
  </div>

  <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3 flex items-center justify-between">
      <h3 class="font-semibold text-gray-800">Historial de compras</h3>
      <span class="text-xs text-gray-500">Ventas: {{ $sales->total() }}</span>
    </div>
    <div class="px-5 pb-4 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-500">
          <tr class="text-left">
            <th class="py-2 pr-4">Fecha</th>
            <th class="py-2 pr-4">Total</th>
            <th class="py-2 pr-4">Método</th>
            <th class="py-2">Ítems</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($sales as $sale)
            <tr>
              <td class="py-2 pr-4 whitespace-nowrap">
                {{ optional($sale->sold_at ?? $sale->created_at)->format('d/m/Y H:i') }}
              </td>
              <td class="py-2 pr-4 font-semibold text-gray-900">${{ number_format($sale->total, 0, ',', '.') }}</td>
              <td class="py-2 pr-4 text-sm text-gray-700">{{ ucfirst($sale->payment_method) }}</td>
              <td class="py-2 text-sm text-gray-700">
                <ul class="list-disc list-inside">
                  @foreach($sale->items as $item)
                    <li>{{ $item->description ?? $item->item->name ?? 'Ítem' }} (x{{ $item->quantity }})</li>
                  @endforeach
                </ul>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="py-6 text-center text-sm text-gray-500">Sin compras registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 pb-4">
      {{ $sales->links() }}
    </div>
  </div>
@endsection
