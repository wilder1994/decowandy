<form id="purchase-form" class="space-y-5">
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
