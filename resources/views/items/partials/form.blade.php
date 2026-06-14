@php($item = $item ?? null)
@php($createSectors = $createSectors ?? config('decowandy.item_create_sectors', []))
@php($isPapeleriaItem = optional($item)->sector === 'papeleria')
<div class="grid gap-4 md:grid-cols-2">
  <div class="md:col-span-2">
    <label class="dw-label mb-1" for="f_name">Nombre</label>
    <input id="f_name" name="name" type="text" value="{{ old('name', optional($item)->name) }}" class="dw-input" placeholder="Ej: Copias B/N">
  </div>

  <div class="md:col-span-1">
    <label class="dw-label mb-1" for="f_sector">Categoría</label>
    @if($isPapeleriaItem)
      <input type="hidden" id="f_sector" name="sector" value="papeleria">
      <input type="text" class="dw-input bg-dw-lilac-soft" value="Papelería" readonly aria-readonly="true">
      <p class="mt-1 text-[11px] text-dw-muted">Alta desde Compras. Aquí solo edición.</p>
    @else
      <select id="f_sector" name="sector" class="dw-select">
        @foreach($createSectors as $key => $label)
          <option value="{{ $key }}" @selected(old('sector', optional($item)->sector ?? array_key_first($createSectors)) === $key)>{{ $label }}</option>
        @endforeach
      </select>
    @endif
  </div>

  <div class="md:col-span-1">
    <label class="dw-label mb-1" for="f_sale_price">Precio base (COP)</label>
    <input id="f_sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', optional($item)->sale_price) }}" class="dw-input" placeholder="0">
    <p class="mt-1 text-[11px] text-dw-muted">Se muestra en el POS; en pesos colombianos.</p>
  </div>

  <div class="md:col-span-1">
    <label class="dw-label mb-1" for="f_cost">Costo (opcional)</label>
    <input id="f_cost" name="cost" type="number" step="0.01" min="0" value="{{ old('cost', optional($item)->cost) }}" class="dw-input" placeholder="0">
  </div>

  <p id="priceWarning" class="dw-alert-error md:col-span-2 hidden"></p>

  <div class="md:col-span-1">
    <label class="dw-label mb-1" for="f_unit">Unidad (opcional)</label>
    <input id="f_unit" name="unit" type="text" value="{{ old('unit', optional($item)->unit) }}" class="dw-input" placeholder="unidad">
  </div>

  <div class="md:col-span-1 flex items-end">
    <label class="inline-flex items-center gap-2">
      <input id="f_active" name="active" type="checkbox" class="rounded border-dw-border text-dw-primary focus:ring-dw-primary" @checked(old('active', optional($item)->active ?? true))>
      <span class="text-sm text-dw-text">Visible en POS</span>
    </label>
  </div>

  <div class="md:col-span-1 flex items-end">
    <label class="inline-flex items-center gap-2">
      <input id="f_stockable" name="stockable" type="checkbox" class="rounded border-dw-border text-dw-primary focus:ring-dw-primary" @checked(old('type', optional($item)->type) === 'product')>
      <span class="text-sm text-dw-text">Este producto controla stock</span>
    </label>
  </div>

  <div id="stockFields" class="hidden md:col-span-2 rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-3">
    <div class="grid gap-4 md:grid-cols-2">
      <div>
        <label class="dw-label mb-1" for="f_stock">Stock actual / ajuste manual</label>
        <input id="f_stock" name="stock" type="number" min="0" value="{{ old('stock', optional($item)->stock) }}" class="dw-input" placeholder="0">
      </div>
      <div>
        <label class="dw-label mb-1" for="f_min_stock">Stock mínimo</label>
        <input id="f_min_stock" name="min_stock" type="number" min="0" value="{{ old('min_stock', optional($item)->min_stock) }}" class="dw-input" placeholder="0">
      </div>
    </div>
    <p class="mt-2 text-[11px] text-dw-muted">Al guardar se registra un ajuste trazable de inventario y el stock mínimo se usa para alertas.</p>
  </div>

  <div id="papeleriaBarcodeFields" class="hidden md:col-span-2 rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-4 space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-2">
      <h3 class="font-display text-sm font-semibold text-dw-text">Código de barras (papelería)</h3>
      <div id="labelActions" class="hidden flex flex-wrap gap-2">
        <a id="btnLabelPng" href="#" target="_blank" class="dw-btn-secondary text-xs py-1.5 px-2.5">Etiqueta PNG</a>
        <button id="btnLabelSheet" type="button" class="dw-btn-secondary text-xs py-1.5 px-2.5">Hoja PDF</button>
      </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
      <div class="md:col-span-2">
        <label class="dw-label mb-1" for="f_barcode">Código</label>
        <div class="flex flex-wrap gap-2">
          <input id="f_barcode" name="barcode" type="text" value="{{ old('barcode', optional($item)->barcode) }}" class="dw-input flex-1 min-w-[12rem]" placeholder="EAN / DWY-0001" autocomplete="off">
          <button id="btnGenBarcode" type="button" class="dw-btn-secondary whitespace-nowrap text-sm">Generar DWY</button>
          <button id="btnScanBarcode" type="button" class="dw-btn-secondary whitespace-nowrap text-sm" title="Escanear con cámara">
            <span class="material-symbols-outlined text-base align-middle">qr_code_scanner</span>
          </button>
        </div>
      </div>

      <div data-dw-color-combobox>
        <label class="dw-label mb-1" for="f_color">Color</label>
        <input id="f_color" name="color" type="text" value="{{ old('color', optional($item)->color ?? 'N/A') }}" class="dw-input" placeholder="N/A">
      </div>

      <div>
        <label class="dw-label mb-1" for="f_scan_mode">Modo escaneo</label>
        <select id="f_scan_mode" name="scan_mode" class="dw-select">
          <option value="unit" @selected(old('scan_mode', optional($item)->scan_mode ?? 'unit') === 'unit')>Unidad (escanear → cantidad)</option>
          <option value="pack" @selected(old('scan_mode', optional($item)->scan_mode) === 'pack')>Paquete (escanear → cantidad)</option>
        </select>
      </div>

      <div>
        <label class="dw-label mb-1" for="f_pack_size">Tamaño paquete (referencia)</label>
        <input id="f_pack_size" name="pack_size" type="number" min="1" value="{{ old('pack_size', optional($item)->pack_size) }}" class="dw-input" placeholder="Ej: 12">
        <p class="mt-1 text-[11px] text-dw-muted">Solo referencia (caja de lápices, etc.). El POS siempre pide cantidad.</p>
      </div>

      <div>
        <label class="dw-label mb-1" for="f_barcode_source">Fuente del código</label>
        <select id="f_barcode_source" name="barcode_source" class="dw-select">
          <option value="manufacturer" @selected(old('barcode_source', optional($item)->barcode_source) === 'manufacturer')>Fabricante</option>
          <option value="internal" @selected(old('barcode_source', optional($item)->barcode_source ?? 'internal') === 'internal')>Interno DWY</option>
        </select>
      </div>
    </div>

    <p id="suggestedPriceHint" class="text-[11px] text-dw-muted hidden"></p>
  </div>

  <div class="md:col-span-2">
    <label class="dw-label mb-1" for="f_description">Descripción (opcional)</label>
    <textarea id="f_description" name="description" rows="3" class="dw-input" placeholder="Detalle del producto o notas internas">{{ old('description', optional($item)->description) }}</textarea>
  </div>
</div>
