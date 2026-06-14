@php($item = $item ?? null)
<div class="grid gap-4 md:grid-cols-2">
  <div class="md:col-span-2">
    <label class="dw-label mb-1" for="f_name">Nombre</label>
    <input id="f_name" name="name" type="text" value="{{ old('name', optional($item)->name) }}" class="dw-input" placeholder="Ej: Copias B/N">
  </div>

  <div class="md:col-span-1">
    <label class="dw-label mb-1" for="f_sector">Categoría</label>
    <select id="f_sector" name="sector" class="dw-select">
      <option value="diseno" @selected(old('sector', optional($item)->sector) === 'diseno')>Diseño</option>
      <option value="impresion" @selected(old('sector', optional($item)->sector) === 'impresion')>Impresión</option>
      <option value="papeleria" @selected(old('sector', optional($item)->sector) === 'papeleria')>Papelería</option>
    </select>
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

  <div class="md:col-span-2">
    <label class="dw-label mb-1" for="f_description">Descripción (opcional)</label>
    <textarea id="f_description" name="description" rows="3" class="dw-input" placeholder="Detalle del producto o notas internas">{{ old('description', optional($item)->description) }}</textarea>
  </div>
</div>
