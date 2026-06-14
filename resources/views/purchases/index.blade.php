{{-- resources/views/purchases/index.blade.php
     Gestión de compras reales: listado + formulario para registrar nuevas compras
--}}
@extends('layouts.admin')

@section('title','Compras — DecoWandy')

@section('content')
@php
    $sectorLabels = [
        'papeleria' => 'Papelería',
        'impresion' => 'Impresión',
        'diseno' => 'Diseño',
    ];
@endphp
<div class="space-y-6">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h1 class="dw-page-title">Compras</h1>
        <div class="flex flex-wrap items-center gap-4">
            <p class="whitespace-nowrap rounded-dw border-hairline border-dw-border bg-dw-lilac-soft px-3 py-1.5 text-sm text-dw-muted">
                Total filtrado:
                <strong class="tabular-nums text-dw-text">${{ number_format($summaryTotal, 0, ',', '.') }}</strong>
            </p>
            <span class="hidden h-6 w-px shrink-0 bg-dw-border sm:block" aria-hidden="true"></span>
            <x-dw-button id="openPurchaseModal" type="button" class="shrink-0">
                <span class="material-symbols-outlined text-base">add</span>
                Agregar compra
            </x-dw-button>
        </div>
    </div>

    <form method="GET" action="{{ route('purchases.index') }}" class="dw-filter-panel">
        <div class="grid gap-3 md:grid-cols-4 md:items-end">
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

{{-- Modal de compra --}}
<div id="purchaseModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close="true"></div>
    <div class="relative mx-auto mt-12 w-[min(920px,95%)] max-h-[92vh] overflow-y-auto rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="font-display text-xl font-semibold text-dw-text">Registrar compra</h3>
            <button id="closePurchaseModal" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <p class="mb-4 text-xs text-dw-muted">Completa los datos, agrega las líneas necesarias y se enviará a la API para actualizar inventario.</p>
        @include('purchases.partials.form')
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('purchase-form');
    const openModalBtn = document.getElementById('openPurchaseModal');
    const purchaseModal = document.getElementById('purchaseModal');
    const closeModalBtn = document.getElementById('closePurchaseModal');
    if (!form) return;

    const storeUrl = @json(route('api.purchases.store'));
    const csrfToken = @json(csrf_token());

    const linesBody = document.getElementById('purchase-lines');
    const template = document.getElementById('purchase-line-template');
    const addBtn = document.getElementById('add-purchase-line');
    const summaryLines = document.getElementById('summary-lines');
    const summaryUnits = document.getElementById('summary-units');
    const summaryAmount = document.getElementById('summary-amount');
    const feedbackBox = document.getElementById('purchase-feedback');

    let lineIndex = 0;

    const currencyFormatter = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    });

    const numberFormatter = new Intl.NumberFormat('es-CO', {
        maximumFractionDigits: 0,
    });

    function showFeedback(message, type = 'success') {
        if (!feedbackBox) return;

        feedbackBox.textContent = message;
        feedbackBox.classList.remove('hidden', 'dw-alert-success', 'dw-alert-error');
        feedbackBox.classList.add(type === 'error' ? 'dw-alert-error' : 'dw-alert-success');
    }

    function clearFeedback() {
        if (!feedbackBox) return;
        feedbackBox.classList.add('hidden');
        feedbackBox.textContent = '';
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

            if (productInput && productInput.value.trim().length > 0) {
                totalLines += 1;
            }

            totalUnits += quantity;
            totalAmount += totalCost;

            if (unitCell) {
                const unitValue = quantity > 0 ? Math.floor(totalCost / quantity) : 0;
                unitCell.textContent = numberFormatter.format(unitValue);
            }
        });

        if (summaryLines) summaryLines.textContent = totalLines.toString();
        if (summaryUnits) summaryUnits.textContent = numberFormatter.format(totalUnits);
        if (summaryAmount) summaryAmount.textContent = currencyFormatter.format(totalAmount);
    }

    function wireRow(row) {
        const qtyInput = row.querySelector('.qty');
        const costInput = row.querySelector('.cost');
        const productInput = row.querySelector('.product');
        const removeBtn = row.querySelector('.remove-line');

        [qtyInput, costInput, productInput].forEach((input) => {
            if (!input) return;
            input.addEventListener('input', recalc);
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                row.remove();
                if (!linesBody.querySelector('tr')) {
                    addLine();
                } else {
                    recalc();
                }
            });
        }
    }

    function addLine() {
        if (!template || !linesBody) return;
        const clone = template.content.firstElementChild.cloneNode(true);
        const currentIndex = lineIndex++;

        const productInput = clone.querySelector('.product');
        const qtyInput = clone.querySelector('.qty');
        const costInput = clone.querySelector('.cost');
        const itemSelect = clone.querySelector('.item-select');

        if (productInput) productInput.name = `items[${currentIndex}][product_name]`;
        if (qtyInput) {
            qtyInput.name = `items[${currentIndex}][quantity]`;
            if (!qtyInput.value) qtyInput.value = 1;
        }
        if (costInput) {
            costInput.name = `items[${currentIndex}][total_cost]`;
            if (!costInput.value) costInput.value = 0;
        }
        if (itemSelect) itemSelect.name = `items[${currentIndex}][item_id]`;

        linesBody.appendChild(clone);
        const appendedRow = linesBody.lastElementChild;
        wireRow(appendedRow);
        recalc();
    }

    addBtn?.addEventListener('click', (event) => {
        event.preventDefault();
        addLine();
    });

    function openModal() {
        purchaseModal?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        purchaseModal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    openModalBtn?.addEventListener('click', openModal);
    closeModalBtn?.addEventListener('click', closeModal);
    purchaseModal?.addEventListener('click', (e) => {
        if (e.target.dataset.close) {
            closeModal();
        }
    });

    addLine();

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFeedback();

        const payload = {
            date: form.querySelector('[name="date"]').value,
            category: form.querySelector('[name="category"]').value.trim(),
            supplier: form.querySelector('[name="supplier"]').value.trim() || null,
            note: form.querySelector('[name="note"]').value.trim() || null,
            to_inventory: form.querySelector('[name="to_inventory"]').checked,
            items: [],
        };

        if (!payload.date) {
            showFeedback('Selecciona la fecha de la compra.', 'error');
            return;
        }

        if (!payload.category) {
            showFeedback('Indica la categoría de la compra.', 'error');
            return;
        }

        linesBody.querySelectorAll('tr').forEach((row) => {
            const product = row.querySelector('.product')?.value?.trim() ?? '';
            const qty = parseInt(row.querySelector('.qty')?.value ?? '0', 10) || 0;
            const totalCost = parseInt(row.querySelector('.cost')?.value ?? '0', 10) || 0;
            const itemValue = row.querySelector('.item-select')?.value ?? '';

            if (product.length === 0) {
                return;
            }

            payload.items.push({
                product_name: product,
                quantity: qty,
                total_cost: totalCost,
                item_id: itemValue !== '' ? Number(itemValue) : null,
            });
        });

        if (!payload.items.length) {
            showFeedback('Agrega al menos una línea con nombre válido.', 'error');
            return;
        }

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                showFeedback('Compra registrada correctamente. Actualizando listado...', 'success');
                form.reset();
                linesBody.innerHTML = '';
                lineIndex = 0;
                addLine();
                recalc();
                setTimeout(() => window.location.reload(), 900);
                closeModal();
                return;
            }

            const data = await response.json().catch(() => null);
            if (data?.errors) {
                const messages = Object.values(data.errors).flat();
                showFeedback(messages.join(' '), 'error');
            } else if (data?.message) {
                showFeedback(data.message, 'error');
            } else {
                showFeedback('No se pudo registrar la compra. Intenta nuevamente.', 'error');
            }
        } catch (error) {
            console.error(error);
            showFeedback('Ocurrió un error inesperado al guardar la compra.', 'error');
        }
    });
});
</script>
@endpush
