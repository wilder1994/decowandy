@php($item = $item ?? null)
<div class="grid gap-4 md:grid-cols-2">
  <div class="md:col-span-2">
    <label class="block text-sm text-gray-600 mb-1">Nombre</label>
    <input id="f_name"
           name="name"
           type="text"
           value="{{ old('name', optional($item)->name) }}"
           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="Ej: Copias B/N">
  </div>

  <div class="md:col-span-1">
    <label class="block text-sm text-gray-600 mb-1">Categoría</label>
    <select id="f_sector"
            name="sector"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      <option value="diseno" @selected(old('sector', optional($item)->sector) === 'diseno')>Diseño</option>
      <option value="impresion" @selected(old('sector', optional($item)->sector) === 'impresion')>Impresión</option>
      <option value="papeleria" @selected(old('sector', optional($item)->sector) === 'papeleria')>Papelería</option>
    </select>
  </div>

  <div class="md:col-span-1">
    <label class="block text-sm text-gray-600 mb-1">Precio base (COP)</label>
    <input id="f_sale_price"
           name="sale_price"
           type="number"
           step="0.01"
           min="0"
           value="{{ old('sale_price', optional($item)->sale_price) }}"
           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="0">
    <p class="text-[11px] text-gray-500 mt-1">Se muestra en el POS; en pesos colombianos.</p>
  </div>

  <div class="md:col-span-1">
    <label class="block text-sm text-gray-600 mb-1">Costo (opcional)</label>
    <input id="f_cost"
           name="cost"
           type="number"
           step="0.01"
           min="0"
           value="{{ old('cost', optional($item)->cost) }}"
           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="0">
  </div>

  <p id="priceWarning" class="md:col-span-2 hidden text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2"></p>

  <div class="md:col-span-1">
    <label class="block text-sm text-gray-600 mb-1">Unidad (opcional)</label>
    <input id="f_unit"
           name="unit"
           type="text"
           value="{{ old('unit', optional($item)->unit) }}"
           class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
           placeholder="unidad">
  </div>

  <div class="md:col-span-1 flex items-end">
    <label class="inline-flex items-center gap-2">
      <input id="f_active"
             name="active"
             type="checkbox"
             class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
             @checked(old('active', optional($item)->active ?? true))>
      <span class="text-sm text-gray-700">Visible en POS</span>
    </label>
  </div>

  <div class="md:col-span-1 flex items-end">
    <label class="inline-flex items-center gap-2">
      <input id="f_stockable"
             name="stockable"
             type="checkbox"
             class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
             @checked(old('type', optional($item)->type) === 'product')>
      <span class="text-sm text-gray-700">Este producto controla stock</span>
    </label>
  </div>

  <div id="stockFields" class="md:col-span-2 rounded-xl border border-gray-100 p-3 bg-slate-50 hidden">
    <div class="grid gap-4 md:grid-cols-2">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Stock actual</label>
        <input id="f_stock"
               name="stock"
               type="number"
               min="0"
               value="{{ old('stock', optional($item)->stock) }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="0">
      </div>
      <div>
        <label class="block text-sm text-gray-600 mb-1">Stock mínimo</label>
        <input id="f_min_stock"
               name="min_stock"
               type="number"
               min="0"
               value="{{ old('min_stock', optional($item)->min_stock) }}"
               class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
               placeholder="0">
      </div>
    </div>
    <p class="text-[11px] text-gray-500 mt-2">Se usa para alertas de inventario y aplica únicamente cuando controla stock.</p>
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm text-gray-600 mb-1">Descripción (opcional)</label>
    <textarea id="f_description"
              name="description"
              rows="3"
              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Detalle del producto o notas internas">{{ old('description', optional($item)->description) }}</textarea>
  </div>
</div>
