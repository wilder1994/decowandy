<form id="purchase-form" class="space-y-5">
    <div class="grid gap-4">
        <div>
            <label for="purchase-date" class="dw-label mb-1">Fecha</label>
            <input type="date" id="purchase-date" name="date" value="{{ now()->toDateString() }}" required
                   class="dw-input">
        </div>
        <div>
            <label for="purchase-category" class="dw-label mb-1">Categoría</label>
            <input type="text" id="purchase-category" name="category" value="Papelería" list="purchase-category-options" required
                   class="dw-input"
                   placeholder="Papelería, Diseño, ...">
            <datalist id="purchase-category-options">
                @foreach($categoryOptions as $option)
                    <option value="{{ $option }}"></option>
                @endforeach
            </datalist>
        </div>
        <div>
            <label for="purchase-supplier" class="dw-label mb-1">Proveedor (opcional)</label>
            <input type="text" id="purchase-supplier" name="supplier"
                   class="dw-input"
                   placeholder="Nombre proveedor">
        </div>
        <div>
            <label for="purchase-note" class="dw-label mb-1">Nota (opcional)</label>
            <input type="text" id="purchase-note" name="note"
                   class="dw-input"
                   placeholder="Factura, orden, etc.">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" id="purchase-to-inventory" name="to_inventory" class="rounded border-gray-300 text-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]" checked>
            <label for="purchase-to-inventory" class="text-sm text-dw-muted">Agregar al inventario cuando exista ítem asociado.</label>
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-dw-text">Líneas de compra</h3>
            <button type="button" id="add-purchase-line" class="dw-btn-secondary text-xs">Agregar línea</button>
        </div>
        <div class="mt-3 overflow-x-auto">
            <table class="dw-table min-w-full text-xs">
                <thead>
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
                    <input type="text" class="product dw-input text-sm"
                           placeholder="Producto o servicio" required>
                </td>
                <td class="px-2 py-2">
                    <input type="number" min="1" value="1" class="qty dw-input w-24 text-right text-sm">
                </td>
                <td class="px-2 py-2">
                    <input type="number" min="0" value="0" class="cost dw-input w-28 text-right text-sm">
                </td>
                <td class="px-2 py-2">
                    <select class="item-select dw-select w-44 text-sm">
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
                    <button type="button" class="remove-line dw-btn-ghost text-xs">Quitar</button>
                </td>
            </tr>
        </template>
    </div>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-3 text-sm">
            <span class="block text-xs uppercase tracking-wide text-dw-muted">Líneas</span>
            <span id="summary-lines" class="font-display text-lg font-semibold text-dw-text">0</span>
        </div>
        <div class="rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-3 text-sm">
            <span class="block text-xs uppercase tracking-wide text-dw-muted">Unidades</span>
            <span id="summary-units" class="font-display text-lg font-semibold text-dw-text">0</span>
        </div>
        <div class="rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-3 text-sm">
            <span class="block text-xs uppercase tracking-wide text-dw-muted">Total estimado</span>
            <span id="summary-amount" class="font-display text-lg font-semibold text-dw-text">$0</span>
        </div>
    </div>

    <div id="purchase-feedback" class="hidden rounded-dw border-hairline px-3 py-2 text-sm"></div>

    <div class="flex flex-col gap-2">
        <x-dw-button type="submit">Guardar compra</x-dw-button>
        <span class="text-xs text-dw-muted">Se enviará a <code>/api/purchases</code> usando tu sesión actual.</span>
    </div>
</form>
