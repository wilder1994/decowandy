{{-- resources/views/purchases/index.blade.php
     Gestión de compras reales: listado + formulario para registrar nuevas compras
--}}
@extends('layouts.admin')

@section('title','Compras y catálogo — DecoWandy')

@section('content')
@php
    $sectorLabels = [
        'papeleria' => 'Papelería',
        'impresion' => 'Impresión',
        'diseno' => 'Diseño',
    ];
@endphp
<div class="dw-mobile-page space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
        <x-dw-page-header class="!mb-0 shrink-0" title="Compras y catálogo" />

        <div class="flex shrink-0 gap-2 overflow-x-auto pb-0.5 [-webkit-overflow-scrolling:touch]">
            <a href="{{ route('purchases.index', array_merge(request()->except('tab'), ['tab' => 'compras'])) }}"
               class="dw-tab {{ $activeTab === 'compras' ? '' : 'opacity-60' }}" data-active="{{ $activeTab === 'compras' ? 'true' : 'false' }}">Compras</a>
            <a href="{{ route('purchases.index', array_merge(request()->except('tab'), ['tab' => 'catalogo'])) }}"
               class="dw-tab {{ $activeTab === 'catalogo' ? '' : 'opacity-60' }}" data-active="{{ $activeTab === 'catalogo' ? 'true' : 'false' }}">Catálogo</a>
        </div>

        <div class="relative ml-auto w-full sm:w-auto">
            <button id="addMenuBtn" type="button" class="dw-btn-primary inline-flex w-full items-center justify-center gap-1 sm:w-auto">
                    <span class="material-symbols-outlined text-base">add</span>
                    Agregar
                    <span class="material-symbols-outlined text-base">expand_more</span>
                </button>
                <div id="addMenu" class="absolute left-0 z-20 mt-1 hidden w-[min(280px,calc(100vw-2rem))] min-w-[220px] overflow-hidden rounded-dw border border-dw-border bg-dw-card shadow-dw-neon sm:left-auto sm:right-0 sm:w-auto">
                    <button type="button" class="add-menu-item block w-full px-4 py-2.5 text-left text-sm hover:bg-dw-lilac-soft" data-add="papeleria">
                        <span class="font-medium text-dw-text">Papelería</span>
                        <span class="block text-xs text-dw-muted">Compra con código · stock IN</span>
                    </button>
                    <button type="button" class="add-menu-item block w-full px-4 py-2.5 text-left text-sm hover:bg-dw-lilac-soft" data-add="impresion">
                        <span class="font-medium text-dw-text">Impresión</span>
                        <span class="block text-xs text-dw-muted">Servicio o insumo</span>
                    </button>
                    <button type="button" class="add-menu-item block w-full px-4 py-2.5 text-left text-sm hover:bg-dw-lilac-soft" data-add="diseno">
                        <span class="font-medium text-dw-text">Diseño</span>
                        <span class="block text-xs text-dw-muted">Servicio / portafolio</span>
                    </button>
                </div>
        </div>
    </div>

    @if($activeTab === 'catalogo')
        @include('purchases.partials.catalog-panel', ['catalog' => $catalog])
    @else
    <div id="purchasesTabPanel" class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-center gap-3">
            <p class="w-full rounded-dw border-hairline border-dw-border bg-dw-lilac-soft px-3 py-2 text-sm text-dw-muted sm:w-auto">
                Total filtrado:
                <strong class="tabular-nums text-dw-text">${{ number_format($summaryTotal, 0, ',', '.') }}</strong>
            </p>
    </div>

    <form method="GET" action="{{ route('purchases.index') }}" class="dw-filter-panel">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4 md:items-end">
            <div>
                <label class="dw-label mb-1" for="from">Desde</label>
                <input id="from" type="date" name="from" value="{{ $filters['from'] }}" class="dw-input">
            </div>
            <div>
                <label class="dw-label mb-1" for="to">Hasta</label>
                <input id="to" type="date" name="to" value="{{ $filters['to'] }}" class="dw-input">
            </div>
            <div>
                <label class="dw-label mb-1" for="category">Categoría</label>
                <select id="category" name="category" class="dw-select">
                    <option value="all" @selected($filters['category'] === 'all')>Todas</option>
                    @foreach($categoryOptions as $option)
                        <option value="{{ $option }}" @selected($filters['category'] === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-wrap items-center justify-end gap-2">
                <x-dw-button variant="secondary" :href="route('purchases.index')">Limpiar</x-dw-button>
                <x-dw-button type="submit">Aplicar filtros</x-dw-button>
            </div>
        </div>
    </form>

    <x-dw-card padding="p-0" class="overflow-hidden">
        <div class="flex flex-col gap-2 border-b px-4 py-3 sm:flex-row sm:items-center sm:justify-between dw-hairline">
            <div>
                <h2 class="font-display text-sm font-semibold text-dw-text">Compras registradas</h2>
                <p class="text-xs text-dw-muted">Mostrando {{ $purchases->count() }} de {{ $purchases->total() }} compras.</p>
            </div>
        </div>
        <div class="overflow-x-auto px-4 py-3">
            <table class="dw-table min-w-full text-sm">
                <thead>
                    <tr class="text-left">
                        <th class="px-3 py-2">Fecha</th>
                        <th class="px-3 py-2">Categoría</th>
                        <th class="px-3 py-2">Proveedor</th>
                        <th class="px-3 py-2">Inventario</th>
                        <th class="px-3 py-2 text-right">Líneas</th>
                        <th class="px-3 py-2 text-right">Unidades</th>
                        <th class="px-3 py-2 text-right">Total</th>
                        <th class="px-3 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        @php
                            $units = $purchase->items->sum('quantity');
                        @endphp
                        <tr>
                            <td class="px-3 py-2 text-sm text-dw-text">{{ optional($purchase->date)->format('d/m/Y') }}</td>
                            <td class="px-3 py-2 font-medium text-dw-text">{{ $purchase->category }}</td>
                            <td class="px-3 py-2 text-dw-muted">{{ $purchase->supplier ?: '—' }}</td>
                            <td class="px-3 py-2">
                                @if($purchase->to_inventory)
                                    <span class="dw-badge-primary">Inventario</span>
                                @else
                                    <span class="dw-badge-warning">Gasto</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right text-dw-text">{{ number_format($purchase->items_count, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right text-dw-text">{{ number_format($units, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right font-semibold text-dw-text">${{ number_format($purchase->total, 0, ',', '.') }}</td>
                            <td class="px-3 py-2 text-right">
                                <a href="{{ route('purchases.show', $purchase) }}" class="dw-link">Ver detalles</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-6 text-center text-sm text-dw-muted">
                                No hay compras registradas con los filtros actuales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="border-t px-4 py-3 dw-hairline">
                {{ $purchases->links() }}
            </div>
        @endif
    </x-dw-card>
    </div>
    @endif
</div>
@endsection

@push('modals')
@include('purchases.partials.papeleria-modal')

{{-- Modal servicio: Impresión / Diseño --}}
<div id="serviceModal" class="dw-app-modal hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-service-close></div>
    <div class="relative mx-auto mt-20 w-[min(520px,92%)] rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
        <div class="mb-4 flex items-center justify-between">
            <h3 id="serviceModalTitle" class="font-display text-lg font-semibold text-dw-text">Nuevo servicio</h3>
            <button type="button" id="closeServiceModal" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="serviceForm" class="space-y-4">
            <input type="hidden" id="svc_sector" name="sector" value="impresion">
            <div>
                <label class="dw-label mb-1" for="svc_name">Nombre</label>
                <input id="svc_name" type="text" class="dw-input" required placeholder="Ej: Copias B/N">
            </div>
            <div>
                <label class="dw-label mb-1" for="svc_price">Precio venta (COP)</label>
                <input id="svc_price" type="number" min="0" step="1" class="dw-input" required>
            </div>
            <div>
                <label class="dw-label mb-1" for="svc_cost">Costo (opcional)</label>
                <input id="svc_cost" type="number" min="0" step="0.01" class="dw-input">
            </div>
            <div>
                <label class="dw-label mb-1" for="svc_description">Descripción (opcional)</label>
                <textarea id="svc_description" rows="2" class="dw-input"></textarea>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input id="svc_stockable" type="checkbox" class="rounded border-dw-border text-dw-primary">
                <span>Controla stock (insumo)</span>
            </label>
            <div id="svcStockFields" class="hidden grid grid-cols-2 gap-3">
                <div>
                    <label class="dw-label mb-1" for="svc_stock">Stock inicial</label>
                    <input id="svc_stock" type="number" min="0" value="0" class="dw-input">
                </div>
                <div>
                    <label class="dw-label mb-1" for="svc_min_stock">Stock mínimo</label>
                    <input id="svc_min_stock" type="number" min="0" value="0" class="dw-input">
                </div>
            </div>
            <div id="serviceFeedback" class="hidden text-sm"></div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="dw-btn-secondary" data-service-close>Cancelar</button>
                <button type="submit" class="dw-btn-primary">Guardar en catálogo</button>
            </div>
        </form>
    </div>
</div>

@include('labels.partials.print-wizard')
@include('purchases.partials.papeleria-modal-config')
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addMenuBtn = document.getElementById('addMenuBtn');
    const addMenu = document.getElementById('addMenu');
    const serviceModal = document.getElementById('serviceModal');
    const serviceForm = document.getElementById('serviceForm');
    const serviceModalTitle = document.getElementById('serviceModalTitle');
    const svcSector = document.getElementById('svc_sector');
    const svcStockable = document.getElementById('svc_stockable');
    const svcStockFields = document.getElementById('svcStockFields');
    const serviceFeedback = document.getElementById('serviceFeedback');
    const sectorLabels = @json($sectorLabels, JSON_UNESCAPED_UNICODE);
    const reorderItem = @json($reorderItem, JSON_UNESCAPED_UNICODE);
    const openOnLoad = @json($openOnLoad);
    const activeTab = @json($activeTab);
    const csrfToken = @json(csrf_token());

    addMenuBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        addMenu?.classList.toggle('hidden');
    });
    document.addEventListener('click', () => addMenu?.classList.add('hidden'));

    document.querySelectorAll('.add-menu-item').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            addMenu?.classList.add('hidden');
            const type = btn.dataset.add;
            if (type === 'papeleria') {
                window.dwOpenPapeleriaModal?.({ mode: 'create' });
            } else if (type === 'impresion' || type === 'diseno') {
                openServiceModal(type);
            } else if (activeTab === 'catalogo' && window.catalogOpenCreate) {
                window.catalogOpenCreate(type);
            }
        });
    });

    function openServiceModal(sector) {
        serviceModal?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        svcSector.value = sector;
        serviceModalTitle.textContent = `Nuevo servicio · ${sectorLabels[sector] || sector}`;
        serviceForm?.reset();
        svcSector.value = sector;
        svcStockFields?.classList.add('hidden');
        serviceFeedback?.classList.add('hidden');
    }

    function closeServiceModal() {
        serviceModal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('closeServiceModal')?.addEventListener('click', closeServiceModal);
    serviceModal?.querySelectorAll('[data-service-close]').forEach((el) => el.addEventListener('click', closeServiceModal));

    svcStockable?.addEventListener('change', () => {
        svcStockFields?.classList.toggle('hidden', !svcStockable.checked);
    });

    serviceForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const axiosInstance = window.axios;
        if (!axiosInstance) return;
        const payload = {
            name: document.getElementById('svc_name')?.value?.trim(),
            sector: svcSector.value,
            sale_price: Number(document.getElementById('svc_price')?.value || 0),
            cost: document.getElementById('svc_cost')?.value ? Number(document.getElementById('svc_cost').value) : null,
            description: document.getElementById('svc_description')?.value?.trim() || null,
            type: svcStockable?.checked ? 'product' : 'service',
            active: true,
        };
        if (payload.type === 'product') {
            payload.stock = Number(document.getElementById('svc_stock')?.value || 0);
            payload.min_stock = Number(document.getElementById('svc_min_stock')?.value || 0);
        }
        try {
            await axiosInstance.post('/api/items', payload, { headers: { 'X-CSRF-TOKEN': csrfToken } });
            closeServiceModal();
            window.location.href = '{{ route('purchases.index', ['tab' => 'catalogo']) }}';
        } catch (err) {
            serviceFeedback.textContent = err.response?.data?.message || 'No se pudo guardar el servicio.';
            serviceFeedback.classList.remove('hidden');
            serviceFeedback.classList.add('text-red-600');
        }
    });

    if (openOnLoad === 'papeleria') {
        window.dwOpenPapeleriaModal?.({ mode: 'create' });
    }

    if (reorderItem && reorderItem.sector === 'papeleria') {
        window.dwOpenPapeleriaModal?.({ mode: 'restock', item: reorderItem });
    }
});
</script>
@endpush
