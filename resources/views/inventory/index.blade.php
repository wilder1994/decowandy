@extends('layouts.admin')

@section('title', 'Inventario — DecoWandy')

@section('content')
<div class="space-y-6">
  <x-dw-page-header title="Inventario" subtitle="Existencias, alertas y compras de reposición. La ficha comercial se edita en Catálogo." />

  <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Productos activos</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ $stats['products'] }}</div>
    </div>
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Unidades en stock</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ number_format($stats['units'], 0, ',', '.') }}</div>
    </div>
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Bajo stock</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ $stats['low_stock'] }}</div>
    </div>
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Sectores con stock</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ $stats['sectors'] }}</div>
    </div>
  </div>

  @if($lowStockItems->isNotEmpty())
  <div class="dw-card p-4">
    <h3 class="mb-3 font-display text-sm font-semibold">Alertas de inventario</h3>
    <div class="overflow-x-auto">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Producto</th>
            <th class="py-2 pr-4">Sector</th>
            <th class="py-2 pr-4">Stock</th>
            <th class="py-2 pr-4">Mínimo</th>
            <th class="py-2 pr-4 text-right">Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach($lowStockItems as $row)
            <tr>
              <td class="py-2 pr-4 font-medium">{{ $row->name }}</td>
              <td class="py-2 pr-4 text-dw-muted">{{ $sectors[$row->sector] ?? $row->sector }}</td>
              <td class="py-2 pr-4 font-semibold text-dw-rose">{{ $row->stock }}</td>
              <td class="py-2 pr-4 text-dw-muted">{{ $row->min_stock }}</td>
              <td class="py-2 pr-4 text-right">
                @if($row->sector === 'papeleria')
                  <button type="button" class="dw-link text-xs" data-restock-id="{{ $row->id }}" data-restock-name="{{ $row->name }}" data-restock-barcode="{{ $row->barcode ?? '' }}" data-restock-stock="{{ $row->stock }}" data-restock-min="{{ $row->min_stock }}" data-restock-sale-price="{{ (int) ($row->sale_price ?? 0) }}" data-restock-color="{{ $row->color ?? 'N/A' }}">Comprar más</button>
                @else
                  <button type="button" class="dw-link text-xs" data-adjust-id="{{ $row->id }}" data-adjust-name="{{ $row->name }}" data-adjust-stock="{{ $row->stock }}" data-adjust-min="{{ $row->min_stock }}">Ajustar</button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  <form method="GET" action="{{ route('inventory.index') }}" class="dw-filter-panel">
    <div class="grid gap-3 md:grid-cols-3 md:items-end">
      <div>
        <label class="dw-label mb-1" for="inv_search">Buscar</label>
        <input id="inv_search" type="search" name="search" value="{{ $filters['search'] }}" class="dw-input" placeholder="Nombre o código">
      </div>
      <div>
        <label class="dw-label mb-1" for="inv_sector">Sector</label>
        <select id="inv_sector" name="sector" class="dw-select">
          <option value="">Todos</option>
          @foreach($sectors as $key => $label)
            <option value="{{ $key }}" @selected($filters['sector'] === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex justify-end gap-2">
        <x-dw-button variant="secondary" :href="route('inventory.index')">Limpiar</x-dw-button>
        <x-dw-button type="submit">Filtrar</x-dw-button>
      </div>
    </div>
  </form>

  <div class="dw-card p-0 overflow-hidden">
    <div class="overflow-x-auto px-4 py-3">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="px-3 py-2">Producto</th>
            <th class="px-3 py-2">Código</th>
            <th class="px-3 py-2">Sector</th>
            <th class="px-3 py-2 text-right">Stock</th>
            <th class="px-3 py-2 text-right">Mínimo</th>
            <th class="px-3 py-2 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $item)
            <tr>
              <td class="px-3 py-2 font-medium text-dw-text">{{ $item->name }}</td>
              <td class="px-3 py-2 font-mono text-xs text-dw-muted">{{ $item->barcode ?: '—' }}</td>
              <td class="px-3 py-2 text-dw-muted">{{ $sectors[$item->sector] ?? $item->sector }}</td>
              <td class="px-3 py-2 text-right font-semibold {{ $item->stock <= $item->min_stock ? 'text-dw-rose' : 'text-dw-primary' }}">{{ $item->stock }}</td>
              <td class="px-3 py-2 text-right text-dw-muted">{{ $item->min_stock }}</td>
              <td class="px-3 py-2 text-right whitespace-nowrap">
                @if($item->sector !== 'papeleria')
                  <button type="button" class="dw-link text-xs mr-2" data-adjust-id="{{ $item->id }}" data-adjust-name="{{ $item->name }}" data-adjust-stock="{{ $item->stock }}" data-adjust-min="{{ $item->min_stock }}">Ajustar</button>
                @endif
                @if($item->sector === 'papeleria')
                  <button type="button" class="dw-link text-xs" data-restock-id="{{ $item->id }}" data-restock-name="{{ $item->name }}" data-restock-barcode="{{ $item->barcode ?? '' }}" data-restock-stock="{{ $item->stock }}" data-restock-min="{{ $item->min_stock }}" data-restock-sale-price="{{ (int) ($item->sale_price ?? 0) }}" data-restock-color="{{ $item->color ?? 'N/A' }}">Comprar más</button>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-3 py-8 text-center text-dw-muted">Sin productos con stock registrado.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($items->hasPages())
      <div class="border-t px-4 py-3 dw-hairline">{{ $items->links() }}</div>
    @endif
  </div>
</div>

<div id="adjustModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/40" data-adjust-close></div>
  <div class="relative mx-auto mt-24 w-[min(420px,92%)] rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
    <h3 class="mb-3 font-display text-lg font-semibold">Ajustar stock</h3>
    <p id="adjustItemName" class="mb-4 text-sm text-dw-muted"></p>
    <input type="hidden" id="adjustItemId">
    <div class="grid gap-3">
      <div>
        <label class="dw-label mb-1" for="adjustStock">Stock actual</label>
        <input id="adjustStock" type="number" min="0" class="dw-input">
      </div>
      <div>
        <label class="dw-label mb-1" for="adjustMin">Stock mínimo</label>
        <input id="adjustMin" type="number" min="0" class="dw-input">
      </div>
    </div>
    <div id="adjustFeedback" class="hidden mt-3 text-sm"></div>
    <div class="mt-4 flex justify-end gap-2">
      <button type="button" class="dw-btn-secondary" data-adjust-close>Cancelar</button>
      <button type="button" id="adjustSave" class="dw-btn-primary">Guardar</button>
    </div>
  </div>
</div>
@endsection

@push('modals')
@include('purchases.partials.papeleria-modal')
@include('purchases.partials.papeleria-modal-config')
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const axiosInstance = window.axios;
  const adjustModal = document.getElementById('adjustModal');
  const itemId = document.getElementById('adjustItemId');
  const itemName = document.getElementById('adjustItemName');
  const stockInput = document.getElementById('adjustStock');
  const minInput = document.getElementById('adjustMin');
  const adjustFeedback = document.getElementById('adjustFeedback');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  function closeAdjustModal() {
    adjustModal?.classList.add('hidden');
  }

  function restockItemFromButton(btn) {
    return {
      id: Number(btn.dataset.restockId),
      name: btn.dataset.restockName || '',
      barcode: btn.dataset.restockBarcode || '',
      stock: Number(btn.dataset.restockStock || 0),
      min_stock: Number(btn.dataset.restockMin || 0),
      sale_price: Number(btn.dataset.restockSalePrice || 0),
      color: btn.dataset.restockColor || 'N/A',
    };
  }

  document.querySelectorAll('[data-adjust-id]').forEach((btn) => {
    btn.addEventListener('click', () => {
      itemId.value = btn.dataset.adjustId;
      itemName.textContent = btn.dataset.adjustName;
      stockInput.value = btn.dataset.adjustStock;
      minInput.value = btn.dataset.adjustMin;
      adjustFeedback?.classList.add('hidden');
      adjustModal?.classList.remove('hidden');
    });
  });

  document.querySelectorAll('[data-restock-id]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const item = restockItemFromButton(btn);
      window.dwOpenPapeleriaModal?.({ mode: 'restock', item: item });
    });
  });

  adjustModal?.querySelectorAll('[data-adjust-close]').forEach((el) => el.addEventListener('click', closeAdjustModal));

  document.getElementById('adjustSave')?.addEventListener('click', async () => {
    if (!axiosInstance || !itemId.value) return;
    try {
      await axiosInstance.patch(`/api/inventory/items/${itemId.value}/stock`, {
        stock: parseInt(stockInput.value || '0', 10),
        min_stock: parseInt(minInput.value || '0', 10),
      }, { headers: { 'X-CSRF-TOKEN': csrf } });
      window.location.reload();
    } catch (e) {
      adjustFeedback.textContent = e.response?.data?.message || 'No se pudo guardar el ajuste.';
      adjustFeedback.classList.remove('hidden');
      adjustFeedback.classList.add('text-red-600');
    }
  });
});
</script>
@endpush
