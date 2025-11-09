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

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
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

        <div class="lg:col-span-1">
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Registrar nueva compra</h2>
                <p class="mt-1 text-xs text-gray-500">Completa los datos, agrega las líneas necesarias y se enviará a la API para actualizar inventario.</p>

                <form id="purchase-form" class="mt-4 space-y-5">
                    <div class="grid gap-4">
                        <div>
                            <label for="purchase-date" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Fecha</label>
                            <input type="date" id="purchase-date" name="date" value="{{ now()->toDateString() }}" required
                                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
                        </div>
                        <div>
                            <label for="purchase-category" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Categoría</label>
                            <input type="text" id="purchase-category" name="category" value="Papelería" list="purchase-category-options" required
                                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]"
                                   placeholder="Papelería, Diseño, ...">
                            <datalist id="purchase-category-options">
                                @foreach($categoryOptions as $option)
                                    <option value="{{ $option }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div>
                            <label for="purchase-supplier" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Proveedor (opcional)</label>
                            <input type="text" id="purchase-supplier" name="supplier"
                                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]"
                                   placeholder="Nombre proveedor">
                        </div>
                        <div>
                            <label for="purchase-note" class="block text-xs font-semibold uppercase tracking-wide text-gray-500">Nota (opcional)</label>
                            <input type="text" id="purchase-note" name="note"
                                   class="mt-1 w-full rounded-xl border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]"
                                   placeholder="Factura, orden, etc.">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="purchase-to-inventory" name="to_inventory" class="rounded border-gray-300 text-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]" checked>
                            <label for="purchase-to-inventory" class="text-sm text-gray-600">Agregar al inventario cuando exista ítem asociado.</label>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700">Líneas de compra</h3>
                            <button type="button" id="add-purchase-line"
                                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                                Agregar línea
                            </button>
                        </div>
                        <div class="mt-3 overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="text-gray-500">
                                    <tr class="text-left">
                                        <th class="px-2 py-2">Descripción</th>
                                        <th class="px-2 py-2">Cantidad</th>
                                        <th class="px-2 py-2">Costo total</th>
                                        <th class="px-2 py-2">Ítem inventario</th>
                                        <th class="px-2 py-2 text-right">Costo unidad</th>
                                        <th class="px-2 py-2 text-right"></th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-lines" class="divide-y divide-gray-100"></tbody>
                            </table>
                        </div>
                        <template id="purchase-line-template">
                            <tr class="line-row">
                                <td class="px-2 py-2">
                                    <input type="text" class="product w-full rounded-lg border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]"
                                           placeholder="Producto o servicio" required>
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" min="1" value="1" class="qty w-24 rounded-lg border-gray-200 text-right text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
                                </td>
                                <td class="px-2 py-2">
                                    <input type="number" min="0" value="0" class="cost w-28 rounded-lg border-gray-200 text-right text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
                                </td>
                                <td class="px-2 py-2">
                                    <select class="item-select w-44 rounded-lg border-gray-200 text-sm focus:border-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]">
                                        <option value="">— Sin inventario —</option>
                                        @foreach($itemsCatalog as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->name }} ({{ $sectorLabels[$item->sector] ?? ucfirst($item->sector) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="unit px-2 py-2 text-right font-semibold text-gray-600">0</td>
                                <td class="px-2 py-2 text-right">
                                    <button type="button" class="remove-line inline-flex items-center rounded-lg border border-gray-200 px-2 py-1 text-xs text-gray-600 hover:bg-gray-50">Quitar</button>
                                </td>
                            </tr>
                        </template>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl bg-gray-50 p-3 text-sm">
                            <span class="block text-xs uppercase tracking-wide text-gray-500">Líneas</span>
                            <span id="summary-lines" class="text-lg font-semibold text-gray-900">0</span>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3 text-sm">
                            <span class="block text-xs uppercase tracking-wide text-gray-500">Unidades</span>
                            <span id="summary-units" class="text-lg font-semibold text-gray-900">0</span>
                        </div>
                        <div class="rounded-xl bg-gray-50 p-3 text-sm">
                            <span class="block text-xs uppercase tracking-wide text-gray-500">Total estimado</span>
                            <span id="summary-amount" class="text-lg font-semibold text-gray-900">$0</span>
                        </div>
                    </div>

                    <div id="purchase-feedback" class="hidden rounded-xl border px-3 py-2 text-sm"></div>

                    <div class="flex flex-col gap-2">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[color:var(--dw-primary)] px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-90">
                            Guardar compra
                        </button>
                        <span class="text-xs text-gray-500">Se enviará a <code>/api/purchases</code> usando tu sesión actual.</span>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('purchase-form');
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
