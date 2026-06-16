<form id="purchase-form" class="space-y-5">
    <div id="purchase-feedback" class="dw-purchase-feedback-sticky hidden rounded-dw border-hairline px-3 py-2 text-sm"></div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="purchase-date" class="dw-label mb-1">Fecha</label>
            <input type="date" id="purchase-date" name="date" value="{{ now()->toDateString() }}" required class="dw-input">
        </div>
        <div>
            <label for="purchase-category" class="dw-label mb-1">Categoría</label>
            <input type="text" id="purchase-category" name="category" value="Papelería" list="purchase-category-options" required
                   class="dw-input" placeholder="Papelería, Diseño, ...">
            <datalist id="purchase-category-options">
                @foreach($categoryOptions as $option)
                    <option value="{{ $option }}"></option>
                @endforeach
            </datalist>
        </div>
        <div>
            <label for="purchase-supplier" class="dw-label mb-1">Proveedor (opcional)</label>
            <input type="text" id="purchase-supplier" name="supplier" class="dw-input" placeholder="Nombre proveedor">
        </div>
        <div>
            <label for="purchase-note" class="dw-label mb-1">Nota (opcional)</label>
            <input type="text" id="purchase-note" name="note" class="dw-input" placeholder="Factura, orden, etc.">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="purchase-to-inventory" name="to_inventory" class="rounded border-gray-300 text-[color:var(--dw-primary)] focus:ring-[color:var(--dw-primary)]" checked>
        <label for="purchase-to-inventory" class="text-sm text-dw-muted">Agregar al inventario cuando exista ítem asociado.</label>
    </div>

    <div id="papeleriaPurchaseHint" class="rounded-dw border-hairline border-dw-border bg-dw-lilac-soft px-3 py-2 text-xs text-dw-muted">
        <strong class="text-dw-text">Papelería:</strong> escanea el código o genera DWY. Si el producto no existe, se crea al guardar la compra con el stock de esta línea.
    </div>

    <div>
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-sm font-semibold text-dw-text">Líneas de compra</h3>
            <button type="button" id="add-purchase-line" class="dw-btn-secondary text-xs">Agregar línea</button>
        </div>
        <div class="mt-3 overflow-x-auto">
            <table class="dw-table dw-purchase-lines-table min-w-full text-xs" id="purchase-lines-table">
                <thead id="purchase-lines-head">
                    <tr class="text-left purchase-head-papeleria">
                        <th class="px-2 py-2">Código</th>
                        <th class="px-2 py-2">Producto</th>
                        <th class="px-2 py-2">Cant.</th>
                        <th class="px-2 py-2">Costo total</th>
                        <th class="px-2 py-2">Precio venta</th>
                        <th class="px-2 py-2">Color</th>
                        <th class="px-2 py-2 text-right">Costo u.</th>
                        <th class="px-2 py-2"></th>
                    </tr>
                    <tr class="text-left purchase-head-standard hidden">
                        <th class="px-2 py-2">Descripción</th>
                        <th class="px-2 py-2">Cantidad</th>
                        <th class="px-2 py-2">Costo total</th>
                        <th class="px-2 py-2">Ítem inventario</th>
                        <th class="px-2 py-2 text-right">Costo unidad</th>
                        <th class="px-2 py-2"></th>
                    </tr>
                </thead>
                <tbody id="purchase-lines" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
    </div>

    <template id="purchase-line-papeleria">
        <tr class="line-row line-row--papeleria">
            <td class="px-2 py-2 align-top" data-label="Código">
                <input type="hidden" class="item-id" value="">
                <div class="flex min-w-[9rem] flex-col gap-1">
                    <div class="flex gap-1">
                        <input type="text" class="barcode dw-input text-xs font-mono" placeholder="Escanear…" autocomplete="off">
                        <button type="button" class="scan-barcode dw-btn-secondary shrink-0 px-2" title="Escanear">
                            <span class="material-symbols-outlined text-base">qr_code_scanner</span>
                        </button>
                    </div>
                    <button type="button" class="gen-barcode dw-btn-ghost text-[10px]">Generar DWY</button>
                    <span class="line-status text-[10px] text-dw-muted"></span>
                </div>
            </td>
            <td class="px-2 py-2 align-top" data-label="Producto">
                <input type="text" class="product dw-input text-sm" placeholder="Nombre del producto" required>
            </td>
            <td class="px-2 py-2 align-top" data-label="Cantidad">
                <input type="number" min="1" value="1" class="qty dw-input w-16 text-right text-sm">
            </td>
            <td class="px-2 py-2 align-top" data-label="Costo total">
                <input type="number" min="0" value="0" class="cost dw-input w-24 text-right text-sm">
            </td>
            <td class="px-2 py-2 align-top" data-label="Precio venta">
                <input type="number" min="0" value="0" class="sale-price dw-input w-24 text-right text-sm" placeholder="Sugerido">
            </td>
            <td class="px-2 py-2 align-top" data-label="Color" data-dw-color-combobox>
                <input type="text" class="color dw-input w-24 text-sm" value="N/A" placeholder="N/A">
            </td>
            <td class="unit px-2 py-2 text-right font-semibold text-gray-600 align-top" data-label="Costo unitario">0</td>
            <td class="px-2 py-2 text-right align-top" data-label="Acción">
                <button type="button" class="remove-line dw-btn-ghost text-xs">Quitar</button>
            </td>
        </tr>
    </template>

    <template id="purchase-line-standard">
        <tr class="line-row line-row--standard">
            <td class="px-2 py-2">
                <input type="text" class="product dw-input text-sm" placeholder="Producto o servicio" required>
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
                        @if($item->sector !== 'papeleria')
                            <option value="{{ $item->id }}">
                                {{ $item->name }} ({{ $sectorLabels[$item->sector] ?? ucfirst($item->sector) }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </td>
            <td class="unit px-2 py-2 text-right font-semibold text-gray-600">0</td>
            <td class="px-2 py-2 text-right">
                <button type="button" class="remove-line dw-btn-ghost text-xs">Quitar</button>
            </td>
        </tr>
    </template>

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

    <div class="flex flex-col gap-2">
        <x-dw-button type="submit">Guardar compra</x-dw-button>
        <span class="text-xs text-dw-muted">Papelería crea o actualiza productos automáticamente al guardar.</span>
    </div>
</form>
