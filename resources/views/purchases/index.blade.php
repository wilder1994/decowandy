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
    <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-start sm:justify-between">
        <x-dw-page-header class="!mb-0" title="Compras y catálogo" subtitle="Altas por sector, historial de compras y consulta del catálogo POS." />
        <div class="relative w-full sm:w-auto">
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

    <div class="flex gap-2 overflow-x-auto pb-1 [-webkit-overflow-scrolling:touch]">
        <a href="{{ route('purchases.index', array_merge(request()->except('tab'), ['tab' => 'compras'])) }}"
           class="dw-tab {{ $activeTab === 'compras' ? '' : 'opacity-60' }}" data-active="{{ $activeTab === 'compras' ? 'true' : 'false' }}">Compras</a>
        <a href="{{ route('purchases.index', array_merge(request()->except('tab'), ['tab' => 'catalogo'])) }}"
           class="dw-tab {{ $activeTab === 'catalogo' ? '' : 'opacity-60' }}" data-active="{{ $activeTab === 'catalogo' ? 'true' : 'false' }}">Catálogo</a>
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
{{-- Modal de compra --}}
<div id="purchaseModal" class="dw-app-modal hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close="true"></div>
    <div class="relative mx-auto mt-6 w-[min(920px,95%)] max-h-[92vh] overflow-y-auto rounded-dw-lg bg-dw-card p-4 shadow-dw-neon dw-hairline-neon sm:mt-12 sm:p-5">
        <div class="mb-4 flex items-center justify-between">
            <h3 id="purchaseModalTitle" class="font-display text-xl font-semibold text-dw-text">Registrar compra · Papelería</h3>
            <button id="closePurchaseModal" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <p class="mb-4 text-xs text-dw-muted">En <strong>Papelería</strong> cada línea puede escanear un código: producto nuevo o existente, con entrada a inventario.</p>
        @include('purchases.partials.form')
    </div>
</div>

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
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const addMenuBtn = document.getElementById('addMenuBtn');
    const addMenu = document.getElementById('addMenu');
    const purchaseModal = document.getElementById('purchaseModal');
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
                openPurchaseModal();
            } else if (type === 'impresion' || type === 'diseno') {
                openServiceModal(type);
            } else if (activeTab === 'catalogo' && window.catalogOpenCreate) {
                window.catalogOpenCreate(type);
            }
        });
    });

    function openPurchaseModal() {
        purchaseModal?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        const cat = document.getElementById('purchase-category');
        if (cat) cat.value = 'Papelería';
        cat?.dispatchEvent(new Event('change'));
    }

    function closePurchaseModal() {
        purchaseModal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

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

    document.getElementById('closePurchaseModal')?.addEventListener('click', closePurchaseModal);
    document.getElementById('closeServiceModal')?.addEventListener('click', closeServiceModal);
    purchaseModal?.querySelector('[data-close]')?.addEventListener('click', closePurchaseModal);
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
        openPurchaseModal();
    }

    if (reorderItem && reorderItem.sector === 'papeleria') {
        openPurchaseModal();
        setTimeout(() => {
            const row = document.querySelector('#purchase-lines tr');
            const barcode = row?.querySelector('.barcode');
            const product = row?.querySelector('.product');
            const itemId = row?.querySelector('.item-id');
            if (barcode && reorderItem.barcode) barcode.value = reorderItem.barcode;
            if (product) product.value = reorderItem.name;
            if (itemId) itemId.value = reorderItem.id;
            const status = row?.querySelector('.line-status');
            if (status) {
                status.textContent = 'Reordenar — producto existente';
                status.className = 'line-status text-[10px] text-green-700';
            }
        }, 300);
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('purchase-form');
    if (!form) return;

    const storeUrl = @json(route('api.purchases.store'));
    const barcodeLookupUrl = @json(url('/api/items/by-barcode'));
    const nextBarcodeUrl = @json(route('api.items.next-barcode'));
    const csrfToken = @json(csrf_token());
    const inventoryConfig = @json($inventoryConfig, JSON_UNESCAPED_UNICODE);

    const categoryInput = document.getElementById('purchase-category');
    const papeleriaHint = document.getElementById('papeleriaPurchaseHint');
    const linesBody = document.getElementById('purchase-lines');
    const templatePapeleria = document.getElementById('purchase-line-papeleria');
    const templateStandard = document.getElementById('purchase-line-standard');
    const headPapeleria = document.querySelector('.purchase-head-papeleria');
    const headStandard = document.querySelector('.purchase-head-standard');
    const addBtn = document.getElementById('add-purchase-line');
    const summaryLines = document.getElementById('summary-lines');
    const summaryUnits = document.getElementById('summary-units');
    const summaryAmount = document.getElementById('summary-amount');
    const feedbackBox = document.getElementById('purchase-feedback');

    let lineIndex = 0;
    let lookupTimer = null;

    const currencyFormatter = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
    const numberFormatter = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });

    function isPapeleriaCategory(value) {
        const v = (value || '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        return v === 'papeleria';
    }

    function suggestedSalePrice(cost) {
        const pct = Number(inventoryConfig.markup_percent || 40);
        const c = Number(cost || 0);
        if (!Number.isFinite(c) || c <= 0) return 0;
        return Math.round(c * (1 + pct / 100));
    }

    function showFeedback(message, type = 'success') {
        if (!feedbackBox) return;
        feedbackBox.textContent = message;
        feedbackBox.classList.remove('hidden', 'dw-alert-success', 'dw-alert-error');
        feedbackBox.classList.add(type === 'error' ? 'dw-alert-error' : 'dw-alert-success');
        if (window.dwShowToast) {
            window.dwShowToast(message, type === 'error' ? 'error' : 'success');
        }
    }

    function clearFeedback() {
        if (!feedbackBox) return;
        feedbackBox.classList.add('hidden');
        feedbackBox.textContent = '';
    }

    function updateCategoryMode() {
        const papeleria = isPapeleriaCategory(categoryInput?.value);
        headPapeleria?.classList.toggle('hidden', !papeleria);
        headStandard?.classList.toggle('hidden', papeleria);
        papeleriaHint?.classList.toggle('hidden', !papeleria);
    }

    function recalc() {
        let totalLines = 0;
        let totalUnits = 0;
        let totalAmount = 0;

        linesBody.querySelectorAll('tr').forEach((row) => {
            const productInput = row.querySelector('.product');
            const qtyInput = row.querySelector('.qty');
            const costInput = row.querySelector('.cost');
            const unitCell = row.querySelector('.unit');

            const quantity = parseInt(qtyInput?.value ?? '0', 10) || 0;
            const totalCost = parseInt(costInput?.value ?? '0', 10) || 0;

            if (productInput?.value.trim()) totalLines += 1;
            totalUnits += quantity;
            totalAmount += totalCost;

            if (unitCell) {
                unitCell.textContent = numberFormatter.format(quantity > 0 ? Math.floor(totalCost / quantity) : 0);
            }
        });

        if (summaryLines) summaryLines.textContent = String(totalLines);
        if (summaryUnits) summaryUnits.textContent = numberFormatter.format(totalUnits);
        if (summaryAmount) summaryAmount.textContent = currencyFormatter.format(totalAmount);
    }

    async function lookupBarcode(row, code) {
        const status = row.querySelector('.line-status');
        const productInput = row.querySelector('.product');
        const itemIdInput = row.querySelector('.item-id');
        const saleInput = row.querySelector('.sale-price');
        const colorInput = row.querySelector('.color');

        if (!code) {
            if (status) status.textContent = '';
            if (itemIdInput) itemIdInput.value = '';
            return;
        }

        try {
            const res = await fetch(`${barcodeLookupUrl}/${encodeURIComponent(code)}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const data = await res.json();

            if (res.ok && data?.item) {
                if (productInput) productInput.value = data.item.name || '';
                if (itemIdInput) itemIdInput.value = data.item.id || '';
                if (saleInput && !saleInput.dataset.touched) {
                    saleInput.value = Math.round(Number(data.item.sale_price || 0));
                }
                if (colorInput) colorInput.value = data.item.color || 'N/A';
                if (status) {
                    status.textContent = 'Producto existente';
                    status.className = 'line-status text-[10px] text-green-700';
                }
                return;
            }
        } catch (e) {
            console.warn(e);
        }

        if (itemIdInput) itemIdInput.value = '';
        if (status) {
            status.textContent = 'Nuevo — se creará al guardar';
            status.className = 'line-status text-[10px] text-dw-primary';
        }
    }

    function wirePapeleriaRow(row) {
        const barcodeInput = row.querySelector('.barcode');
        const productInput = row.querySelector('.product');
        const qtyInput = row.querySelector('.qty');
        const costInput = row.querySelector('.cost');
        const saleInput = row.querySelector('.sale-price');
        const colorInput = row.querySelector('.color');
        const removeBtn = row.querySelector('.remove-line');
        const scanBtn = row.querySelector('.scan-barcode');
        const genBtn = row.querySelector('.gen-barcode');

        if (colorInput && window.dwInitColorCombobox) {
            window.dwInitColorCombobox(colorInput, { colors: inventoryConfig.colors });
        }

        [qtyInput, costInput, productInput].forEach((input) => input?.addEventListener('input', recalc));

        costInput?.addEventListener('input', () => {
            if (!saleInput?.dataset.touched) {
                const qty = parseInt(qtyInput?.value || '1', 10) || 1;
                const total = parseInt(costInput.value || '0', 10) || 0;
                const unit = qty > 0 ? Math.floor(total / qty) : 0;
                saleInput.value = suggestedSalePrice(unit) || '';
            }
        });

        saleInput?.addEventListener('input', () => {
            saleInput.dataset.touched = '1';
        });

        barcodeInput?.addEventListener('input', () => {
            clearTimeout(lookupTimer);
            lookupTimer = setTimeout(() => lookupBarcode(row, barcodeInput.value.trim()), 350);
        });

        barcodeInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupBarcode(row, barcodeInput.value.trim());
                productInput?.focus();
            }
        });

        scanBtn?.addEventListener('click', () => {
            if (!window.dwOpenBarcodeScanner) {
                showFeedback('Escáner no disponible.', 'error');
                return;
            }
            window.dwOpenBarcodeScanner({
                parentModal: document.getElementById('purchaseModal'),
                onDetected: (code) => {
                    if (barcodeInput) barcodeInput.value = code;
                    lookupBarcode(row, code);
                    qtyInput?.focus();
                    qtyInput?.select?.();
                },
                onError: () => showFeedback('No se pudo acceder a la cámara.', 'error'),
                onScanError: (message) => showFeedback(message, 'error'),
            });
        });

        genBtn?.addEventListener('click', async () => {
            try {
                const res = await fetch(nextBarcodeUrl, {
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                if (data?.barcode && barcodeInput) {
                    barcodeInput.value = data.barcode;
                    lookupBarcode(row, data.barcode);
                }
            } catch (e) {
                showFeedback('No se pudo generar código DWY.', 'error');
            }
        });

        removeBtn?.addEventListener('click', () => {
            row.remove();
            if (!linesBody.querySelector('tr')) addLine();
            else recalc();
        });
    }

    function wireStandardRow(row) {
        const qtyInput = row.querySelector('.qty');
        const costInput = row.querySelector('.cost');
        const productInput = row.querySelector('.product');
        const removeBtn = row.querySelector('.remove-line');

        [qtyInput, costInput, productInput].forEach((input) => input?.addEventListener('input', recalc));

        removeBtn?.addEventListener('click', () => {
            row.remove();
            if (!linesBody.querySelector('tr')) addLine();
            else recalc();
        });
    }

    function addLine() {
        const papeleria = isPapeleriaCategory(categoryInput?.value);
        const template = papeleria ? templatePapeleria : templateStandard;
        if (!template || !linesBody) return;

        const clone = template.content.firstElementChild.cloneNode(true);
        const idx = lineIndex++;

        const productInput = clone.querySelector('.product');
        const qtyInput = clone.querySelector('.qty');
        const costInput = clone.querySelector('.cost');

        if (productInput) productInput.name = `items[${idx}][product_name]`;
        if (qtyInput) {
            qtyInput.name = `items[${idx}][quantity]`;
            if (!qtyInput.value) qtyInput.value = 1;
        }
        if (costInput) {
            costInput.name = `items[${idx}][total_cost]`;
            if (!costInput.value) costInput.value = 0;
        }

        if (papeleria) {
            wirePapeleriaRow(clone);
        } else {
            const itemSelect = clone.querySelector('.item-select');
            if (itemSelect) itemSelect.name = `items[${idx}][item_id]`;
            wireStandardRow(clone);
        }

        linesBody.appendChild(clone);
        recalc();
    }

    function rebuildLines() {
        linesBody.innerHTML = '';
        lineIndex = 0;
        addLine();
    }

    categoryInput?.addEventListener('change', () => {
        updateCategoryMode();
        rebuildLines();
    });
    categoryInput?.addEventListener('input', updateCategoryMode);

    addBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        addLine();
    });

    function openModal() {
        document.getElementById('purchaseModal')?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        updateCategoryMode();
    }

    function closeModal() {
        document.getElementById('purchaseModal')?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    updateCategoryMode();
    addLine();

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFeedback();

        const papeleria = isPapeleriaCategory(categoryInput?.value);
        const payload = {
            date: form.querySelector('[name="date"]').value,
            category: categoryInput?.value.trim(),
            supplier: form.querySelector('[name="supplier"]').value.trim() || null,
            note: form.querySelector('[name="note"]').value.trim() || null,
            to_inventory: form.querySelector('[name="to_inventory"]').checked,
            items: [],
        };

        if (!payload.date || !payload.category) {
            showFeedback('Completa fecha y categoría.', 'error');
            return;
        }

        linesBody.querySelectorAll('tr').forEach((row) => {
            const product = row.querySelector('.product')?.value?.trim() ?? '';
            const qty = parseInt(row.querySelector('.qty')?.value ?? '0', 10) || 0;
            const totalCost = parseInt(row.querySelector('.cost')?.value ?? '0', 10) || 0;
            if (!product) return;

            const line = { product_name: product, quantity: qty, total_cost: totalCost };

            if (papeleria) {
                const barcode = row.querySelector('.barcode')?.value?.trim() ?? '';
                const salePrice = parseInt(row.querySelector('.sale-price')?.value ?? '0', 10) || 0;
                const color = row.querySelector('.color')?.value?.trim() || 'N/A';
                const itemId = row.querySelector('.item-id')?.value ?? '';

                if (barcode) line.barcode = barcode;
                if (salePrice > 0) line.sale_price = salePrice;
                if (color) line.color = color;
                if (itemId) line.item_id = Number(itemId);
            } else {
                const itemValue = row.querySelector('.item-select')?.value ?? '';
                if (itemValue) line.item_id = Number(itemValue);
            }

            payload.items.push(line);
        });

        if (!payload.items.length) {
            showFeedback('Agrega al menos una línea válida.', 'error');
            return;
        }

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                showFeedback('Compra registrada. Actualizando…', 'success');
                form.reset();
                if (categoryInput) categoryInput.value = 'Papelería';
                rebuildLines();
                setTimeout(() => window.location.reload(), 900);
                closeModal();
                return;
            }

            const data = await response.json().catch(() => null);
            if (data?.errors) {
                showFeedback(Object.values(data.errors).flat().join(' '), 'error');
            } else if (data?.message) {
                showFeedback(data.message, 'error');
            } else {
                showFeedback('No se pudo registrar la compra.', 'error');
            }
        } catch (error) {
            console.error(error);
            showFeedback('Error inesperado al guardar.', 'error');
        }
    });
});
</script>
@endpush
