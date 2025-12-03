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
<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold">Compras</h1>
            <p class="text-sm text-gray-500">Administra las compras registradas y agrega nuevas entradas que actualicen el inventario.</p>
        </div>
        <div class="rounded-2xl border border-violet-100 bg-white px-5 py-3 text-right shadow-sm">
            <span class="block text-xs uppercase tracking-wide text-gray-500">Total filtrado</span>
            <span class="text-xl font-semibold text-[color:var(--dw-primary)]">${{ number_format($summaryTotal, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <div></div>
        <button id="openPurchaseModal" type="button" class="inline-flex items-center gap-2 rounded-xl bg-[color:var(--dw-primary)] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
            <span class="material-symbols-outlined text-base">add</span>
            Agregar compra
        </button>
    </div>

    <div class="space-y-6">
        <div class="space-y-6">
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('purchases.index') }}" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Desde</label>
                            <input type="date" name="from" value="{{ $filters['from'] }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Hasta</label>
                            <input type="date" name="to" value="{{ $filters['to'] }}"
                                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
                        </div>
                        <div class="sm:col-span-2 lg:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Categoría</label>
                            <select name="category"
                                    class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
                                <option value="all" @selected($filters['category'] === 'all')>Todas</option>
                                @foreach($categoryOptions as $option)
                                    <option value="{{ $option }}" @selected($filters['category'] === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('purchases.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                            Limpiar
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[color:var(--dw-primary)] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                            Aplicar filtros
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Compras registradas</h2>
                        <p class="text-xs text-gray-500">Mostrando {{ $purchases->count() }} de {{ $purchases->total() }} compras.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-gray-500">
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
                        <tbody class="divide-y divide-gray-100">
                            @forelse($purchases as $purchase)
                                @php
                                    $units = $purchase->items->sum('quantity');
                                @endphp
                                <tr class="hover:bg-gray-50/70">
                                    <td class="px-3 py-2 text-sm text-gray-700">{{ optional($purchase->date)->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-800">{{ $purchase->category }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $purchase->supplier ?: '—' }}</td>
                                    <td class="px-3 py-2">
                                        @if($purchase->to_inventory)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">Inventario</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-500">Gasto</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right text-gray-700">{{ number_format($purchase->items_count, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-gray-700">{{ number_format($units, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900">${{ number_format($purchase->total, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-[color:var(--dw-primary)] hover:bg-violet-50">Ver detalles</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-3 py-6 text-center text-sm text-gray-500">
                                        No hay compras registradas con los filtros actuales.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($purchases->hasPages())
                    <div class="mt-4">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>

{{-- Modal de compra --}}
<div id="purchaseModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40" data-close="true"></div>
    <div class="relative mx-auto mt-12 w-[min(920px,95%)] rounded-2xl bg-white p-6 shadow-2xl max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Registrar compra</h3>
            <button id="closePurchaseModal" type="button" class="h-9 w-9 rounded-full hover:bg-gray-100 flex items-center justify-center">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <p class="text-xs text-gray-500 mb-4">Completa los datos, agrega las líneas necesarias y se enviará a la API para actualizar inventario.</p>
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
        feedbackBox.classList.remove('hidden', 'bg-red-50', 'border-red-200', 'text-red-600', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-700');

        if (type === 'error') {
            feedbackBox.classList.add('bg-red-50', 'border-red-200', 'text-red-600');
        } else {
            feedbackBox.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-700');
        }
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
