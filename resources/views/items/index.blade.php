{{-- resources/views/items/index.blade.php
     Gestor de Productos - conectado a la API
     - Tabs por sector (Diseño, Impresión, Papelería)
     - Tabla con datos reales usando Axios sobre /api/items
     - Modal Crear/Editar con envíos POST/PUT y confirmación de borrado
--}}
@extends('layouts.admin')

@section('title','Ítems - DecoWandy')

@php
    /** Normaliza paginator para evitar errores si llega una colección plana */
    $paginated = $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator
        ? $items
        : new \Illuminate\Pagination\LengthAwarePaginator(
            $items->values(),
            $items->count(),
            $filters['per_page'] ?? 10,
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );

    $initialPayload = [
        'items' => $paginated->items(),
        'pagination' => [
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
        ],
        'filters' => $filters,
    ];

    $inventoryConfig = [
        'colors' => config('decowandy.inventory.colors', ['N/A']),
        'markup_percent' => (int) config('decowandy.inventory.markup_percent', 40),
    ];
@endphp

@section('content')

  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <x-dw-page-header title="Ítems" subtitle="Diseño e impresión se crean aquí. Papelería se registra en Compras." />
    <div class="flex flex-wrap gap-2">
      <button id="btnSheetLabels" type="button" class="dw-btn-secondary hidden text-sm">
        <span class="material-symbols-outlined text-base">picture_as_pdf</span>
        Etiquetas PDF
      </button>
      <x-dw-button id="btnNew" type="button">
        <span class="material-symbols-outlined text-base">add</span>
        Nuevo producto
      </x-dw-button>
    </div>
  </div>

  {{-- Inventario --}}
  <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Productos con stock</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ $inventoryStats['stockable'] }}</div>
      <div class="mt-1 text-xs text-dw-muted">Listos para controlar existencias</div>
    </div>
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Servicios</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ $inventoryStats['services'] }}</div>
      <div class="mt-1 text-xs text-dw-muted">Ítems sin stock</div>
    </div>
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Unidades en stock</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ number_format($inventoryStats['units'], 0, ',', '.') }}</div>
      <div class="mt-1 text-xs text-dw-muted">Suma de existencias actuales</div>
    </div>
    <div class="dw-card p-3.5">
      <div class="text-xs font-medium uppercase tracking-wide text-dw-muted">Bajo stock</div>
      <div class="mt-1 font-display text-xl font-bold text-dw-text">{{ $inventoryStats['low_stock'] }}</div>
      <div class="mt-1 text-xs text-dw-muted">Por debajo del mínimo</div>
    </div>
  </div>

  <div class="dw-card mb-4 p-4">
    <div class="flex items-center justify-between mb-3">
      <div>
        <h3 class="font-display text-sm font-semibold">Alertas de inventario</h3>
        <p class="text-xs text-dw-muted">Items con stock por debajo del mínimo configurado.</p>
      </div>
      <span class="text-xs text-dw-muted">Top 5</span>
    </div>
    <div class="overflow-x-auto">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Producto</th>
            <th class="py-2 pr-4">Sector</th>
            <th class="py-2 pr-4">Stock</th>
            <th class="py-2 pr-4">Mínimo</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lowStockItems as $item)
            <tr>
              <td class="py-2 pr-4">{{ $item->name }}</td>
              <td class="py-2 pr-4 text-dw-muted">{{ $sectors[$item->sector] ?? $item->sector }}</td>
              <td class="py-2 pr-4 text-rose-600 font-semibold">{{ $item->stock }}</td>
              <td class="py-2 pr-4 text-dw-muted">{{ $item->min_stock }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="py-3 text-center text-sm text-dw-muted">Sin alertas de inventario.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Buscador --}}
  <div class="mb-4 flex flex-wrap items-center gap-3">
    <div class="flex flex-wrap gap-2">
      @foreach($sectors as $key => $label)
        <button data-sector="{{ $key }}"
                type="button"
                class="tab-btn dw-tab"
                data-default="{{ $loop->first ? '1' : '0' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>
    <div class="relative ml-auto w-full max-w-xs">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-dw-muted text-base">search</span>
      <input id="searchBox"
             type="search"
             placeholder="Buscar por nombre o descripción"
             class="dw-input pl-9">
    </div>
  </div>

  <div id="papeleriaCatalogBanner" class="hidden mb-4 rounded-dw border-hairline border-dw-border bg-dw-lilac-soft px-4 py-3 text-sm text-dw-muted">
    Los productos de <strong class="text-dw-text">papelería</strong> se crean al registrar una compra.
    <a href="{{ route('purchases.index') }}" class="dw-link ml-1">Ir a Compras →</a>
    Aquí puedes consultar, editar precios y descargar etiquetas.
  </div>

  {{-- Tabla --}}
  <div class="dw-card p-4">
    <div class="overflow-x-auto">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left" id="itemsTableHead">
            <th class="py-2 pr-4">Producto</th>
            <th class="py-2 pr-4 papeleria-col hidden">Código</th>
            <th class="py-2 pr-4 papeleria-col hidden">Color</th>
            <th class="py-2 pr-4">Categoría</th>
            <th class="py-2 pr-4">Tipo</th>
            <th class="py-2 pr-4">Stock</th>
            <th class="py-2 pr-4">Mínimo</th>
            <th class="py-2 pr-4">Precio (COP)</th>
            <th class="py-2 pr-4">Visible</th>
            <th class="py-2 pr-2 w-32 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody id="itemsTableBody">
          {{-- filas generadas por JS --}}
        </tbody>
      </table>
    </div>

    <div id="paginationControls" class="mt-3 flex flex-wrap items-center justify-between gap-3 text-sm text-dw-muted">
      <div id="paginationInfo"></div>
      <div class="flex items-center gap-2">
        <button id="btnPrev"
                type="button"
                class="dw-btn-secondary px-3 py-1 disabled:cursor-not-allowed disabled:opacity-60">
          Anterior
        </button>
        <button id="btnNext"
                type="button"
                class="dw-btn-secondary px-3 py-1 disabled:cursor-not-allowed disabled:opacity-60">
          Siguiente
        </button>
      </div>
    </div>
  </div>

  {{-- Modal Crear/Editar --}}
  <div id="itemModal" class="hidden fixed inset-0 z-50 flex items-start justify-center">
    <div class="absolute inset-0 bg-black/30 z-40" data-close="true"></div>
    <div class="relative z-50 mx-auto mt-16 w-[min(720px,92%)] max-h-[90vh] overflow-y-auto rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
      <div class="sticky top-0 mb-4 flex items-center justify-between bg-dw-card">
        <h2 id="modalTitle" class="font-display text-lg font-semibold">Nuevo producto</h2>
        <button id="modalClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-dw-lilac-soft">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div id="modalAlert" class="hidden mb-4 rounded-xl border px-3 py-2 text-sm"></div>

      <form id="itemForm" class="space-y-5">
        @include('items.partials.form', ['item' => null, 'createSectors' => $createSectors])
        <div class="sticky bottom-0 flex items-center justify-end gap-2 bg-dw-card pt-2">
          <button id="modalCancel" type="button" class="dw-btn-secondary">Cancelar</button>
          <button id="modalSave" type="submit" class="dw-btn-primary">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Modal confirmación rentabilidad --}}
  <div id="priceConfirmModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center">
    <div class="absolute inset-0 bg-black/50" data-pc-close="true"></div>
    <div class="relative z-10 mx-auto w-[min(520px,92%)] rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
      <h3 class="mb-2 font-display text-lg font-semibold text-dw-text">Atención</h3>
      <p id="priceConfirmMessage" class="text-sm text-dw-muted"></p>
      <div class="mt-5 flex justify-end gap-2">
        <button id="priceConfirmCancel" type="button" class="dw-btn-secondary">Seguir editando</button>
        <button id="priceConfirmProceed" type="button" class="dw-btn-primary">Guardar de todas formas</button>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const axiosInstance = window.axios;
    if (!axiosInstance) {
      console.error('Axios no está disponible en la página.');
      return;
    }

    const initialPayload = @json($initialPayload, JSON_UNESCAPED_UNICODE);
    const sectorLabels = @json($sectors, JSON_UNESCAPED_UNICODE);
    const createSectors = @json($createSectors ?? [], JSON_UNESCAPED_UNICODE);
    const purchasesUrl = @json(route('purchases.index'));
    const inventoryConfig = @json($inventoryConfig, JSON_UNESCAPED_UNICODE);

    const tabButtons = Array.from(document.querySelectorAll('[data-sector]'));
    const papeleriaBanner = document.getElementById('papeleriaCatalogBanner');
    const searchBox = document.getElementById('searchBox');
    const tableBody = document.getElementById('itemsTableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const itemsAlert = document.getElementById('itemsAlert');
    const modal = document.getElementById('itemModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalAlert = document.getElementById('modalAlert');
    const modalSave = document.getElementById('modalSave');
    const modalCancel = document.getElementById('modalCancel');
    const modalClose = document.getElementById('modalClose');
    const btnNew = document.getElementById('btnNew');
    const btnSheetLabels = document.getElementById('btnSheetLabels');
    const form = document.getElementById('itemForm');
    const priceConfirmModal = document.getElementById('priceConfirmModal');
    const priceConfirmMsg = document.getElementById('priceConfirmMessage');
    const priceConfirmCancel = document.getElementById('priceConfirmCancel');
    const priceConfirmProceed = document.getElementById('priceConfirmProceed');

    const fields = {
      name: document.getElementById('f_name'),
      sector: document.getElementById('f_sector'),
      salePrice: document.getElementById('f_sale_price'),
      cost: document.getElementById('f_cost'),
      unit: document.getElementById('f_unit'),
      active: document.getElementById('f_active'),
      stockable: document.getElementById('f_stockable'),
      stock: document.getElementById('f_stock'),
      minStock: document.getElementById('f_min_stock'),
      description: document.getElementById('f_description'),
      stockFields: document.getElementById('stockFields'),
      priceWarning: document.getElementById('priceWarning'),
      papeleriaFields: document.getElementById('papeleriaBarcodeFields'),
      barcode: document.getElementById('f_barcode'),
      color: document.getElementById('f_color'),
      scanMode: document.getElementById('f_scan_mode'),
      packSize: document.getElementById('f_pack_size'),
      barcodeSource: document.getElementById('f_barcode_source'),
      btnGenBarcode: document.getElementById('btnGenBarcode'),
      btnScanBarcode: document.getElementById('btnScanBarcode'),
      labelActions: document.getElementById('labelActions'),
      btnLabelPng: document.getElementById('btnLabelPng'),
      btnLabelSheet: document.getElementById('btnLabelSheet'),
      suggestedPriceHint: document.getElementById('suggestedPriceHint'),
    };

    const state = {
      sector: initialPayload.filters?.sector || Object.keys(sectorLabels)[0],
      search: initialPayload.filters?.search || '',
      page: initialPayload.pagination?.current_page || 1,
      perPage: initialPayload.pagination?.per_page || 10,
      lastPage: initialPayload.pagination?.last_page || 1,
      total: initialPayload.pagination?.total || 0,
      items: Array.isArray(initialPayload.items) ? initialPayload.items : [],
      loading: false,
      editing: null,
      priceWarningAcknowledged: false,
      priceWarningSignature: null,
      priceWarningOpen: false,
      pendingPayload: null,
      costSuggested: false,
    };

    const currency = new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP',
      maximumFractionDigits: 0,
    });

    searchBox.value = state.search;

    function escapeHtml(value) {
      const div = document.createElement('div');
      div.innerText = value ?? '';
      return div.innerHTML;
    }

    function toggleTabs() {
      tabButtons.forEach((btn) => {
        btn.dataset.active = btn.dataset.sector === state.sector ? 'true' : 'false';
      });
    }

    function showPageAlert(type, message) {
      itemsAlert.textContent = message;
      itemsAlert.classList.remove('hidden', 'dw-alert-success', 'dw-alert-error');
      itemsAlert.classList.add(type === 'success' ? 'dw-alert-success' : 'dw-alert-error');
      setTimeout(() => {
        itemsAlert.classList.add('hidden');
      }, 4000);
    }

    function showModalAlert(type, message) {
      modalAlert.textContent = message;
      modalAlert.classList.remove('hidden', 'border-green-200', 'text-green-700', 'border-red-200', 'text-red-700', 'bg-green-50', 'bg-red-50');
      if (type === 'success') {
        modalAlert.classList.add('border-green-200', 'text-green-700', 'bg-green-50');
      } else {
        modalAlert.classList.add('border-red-200', 'text-red-700', 'bg-red-50');
      }
    }

    function clearModalAlert() {
      modalAlert.textContent = '';
      modalAlert.classList.add('hidden');
    }

    function defaultCreateSector() {
      const keys = Object.keys(createSectors);
      return keys[0] || state.sector;
    }

    function togglePapeleriaColumns() {
      const show = state.sector === 'papeleria';
      document.querySelectorAll('.papeleria-col').forEach((el) => {
        el.classList.toggle('hidden', !show);
      });
      btnSheetLabels?.classList.toggle('hidden', !show);
      papeleriaBanner?.classList.toggle('hidden', !show);
      btnNew?.classList.toggle('hidden', show);
    }

    function updatePapeleriaVisibility() {
      const isPapeleria = fields.sector.value === 'papeleria';
      const isProduct = fields.stockable.checked;
      const show = isPapeleria && isProduct;
      fields.papeleriaFields?.classList.toggle('hidden', !show);
      if (show && fields.color && window.dwInitColorCombobox) {
        window.dwInitColorCombobox(fields.color, { colors: inventoryConfig.colors });
      }
    }

    function suggestedPriceFromCost(cost) {
      const pct = Number(inventoryConfig.markup_percent || 40);
      if (!Number.isFinite(cost) || cost <= 0) return 0;
      return Math.round(cost * (1 + pct / 100));
    }

    function updateSuggestedPriceHint() {
      if (!fields.suggestedPriceHint || fields.sector.value !== 'papeleria') return;
      const cost = Number(fields.cost.value || 0);
      const suggested = suggestedPriceFromCost(cost);
      if (suggested > 0) {
        fields.suggestedPriceHint.textContent = `Sugerido (+${inventoryConfig.markup_percent}%): $${suggested.toLocaleString('es-CO')}`;
        fields.suggestedPriceHint.classList.remove('hidden');
      } else {
        fields.suggestedPriceHint.classList.add('hidden');
      }
    }

    function applySuggestedPriceFromCost() {
      if (fields.sector.value !== 'papeleria') return;
      const cost = Number(fields.cost.value || 0);
      const suggested = suggestedPriceFromCost(cost);
      if (suggested > 0 && (!fields.salePrice.value || state.costSuggested)) {
        fields.salePrice.value = suggested;
        state.costSuggested = true;
        updatePriceWarning();
      }
    }

    function updateLabelActions(itemId) {
      const hasItem = Boolean(itemId);
      fields.labelActions?.classList.toggle('hidden', !hasItem);
      if (hasItem && fields.btnLabelPng) {
        fields.btnLabelPng.href = `/api/items/${itemId}/label.png`;
      }
    }

    function updateStockVisibility() {
      const shouldShow = fields.stockable.checked;
      fields.stockFields.classList.toggle('hidden', !shouldShow);
      updatePapeleriaVisibility();
    }

    function shouldWarn(cost, sale) {
      const hasNumbers = Number.isFinite(cost) && Number.isFinite(sale);
      const hasMeaningfulValue = (cost !== 0 || sale !== 0);
      return hasNumbers && hasMeaningfulValue && sale <= cost;
    }

    function updatePriceWarning() {
      if (!fields.priceWarning) return;
      const cost = Number(fields.cost.value || 0);
      const sale = Number(fields.salePrice.value || 0);
      const signature = `${cost}|${sale}`;
      const valuesChanged = signature !== state.priceWarningSignature;

      if (valuesChanged) {
        state.priceWarningAcknowledged = false;
        state.priceWarningSignature = signature;
      }

      fields.priceWarning.classList.add('hidden');
      fields.priceWarning.textContent = '';

      if (sale < cost) {
        fields.priceWarning.textContent = 'Atención: este precio genera pérdidas (el costo es mayor que el precio de venta).';
        fields.priceWarning.classList.remove('hidden');
      } else if (sale === cost && sale !== 0) {
        fields.priceWarning.textContent = 'Atención: este precio no genera ganancia (precio igual al costo).';
        fields.priceWarning.classList.remove('hidden');
      }

      if (shouldWarn(cost, sale) && !state.priceWarningAcknowledged) {
        openPriceConfirm(cost, sale);
      } else if (!shouldWarn(cost, sale) && state.priceWarningOpen) {
        closePriceConfirm(true);
      }
    }

    function formatMoney(value) {
      const number = Number(value ?? 0);
      return currency.format(Number.isFinite(number) ? number : 0);
    }

    function tableColspan() {
      return state.sector === 'papeleria' ? 10 : 8;
    }

    function renderTable() {
      tableBody.innerHTML = '';
      togglePapeleriaColumns();

      if (state.loading) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="${tableColspan()}" class="py-6 text-center text-sm text-dw-muted">Cargando ítems…</td>`;
        tableBody.appendChild(row);
        return;
      }

      if (!state.items.length) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="${tableColspan()}" class="py-6 text-center text-sm text-dw-muted">Sin productos en este sector.</td>`;
        tableBody.appendChild(row);
        return;
      }

      const showPapeleria = state.sector === 'papeleria';

      state.items.forEach((item) => {
        const tr = document.createElement('tr');
        const activeBadge = item.active
          ? '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Sí</span>'
          : '<span class="dw-badge-warning">No</span>';

        const stockNumber = item.type === 'product' ? Number(item.stock ?? 0) : null;
        const minStock = item.type === 'product' ? Number(item.min_stock ?? 0) : null;
        const stockBadge = stockNumber === null
          ? '<span class="dw-badge-primary">Servicio</span>'
          : `<div class="font-semibold ${stockNumber <= minStock ? 'text-dw-rose' : 'text-dw-primary'}">${stockNumber}</div>`;
        const minStockLabel = stockNumber === null ? '-' : minStock;

        const papeleriaCols = showPapeleria
          ? `<td class="py-2 pr-4 font-mono text-xs">${escapeHtml(item.barcode || '—')}</td>
             <td class="py-2 pr-4 text-dw-muted">${escapeHtml(item.color && item.color !== 'N/A' ? item.color : '—')}</td>`
          : '';

        tr.innerHTML = `
          <td class="py-2 pr-4">
            <div class="font-medium text-[color:var(--dw-text)]">${escapeHtml(item.name)}</div>
            ${item.description ? `<div class="text-xs text-dw-muted mt-0.5">${escapeHtml(item.description)}</div>` : ''}
          </td>
          ${papeleriaCols}
          <td class="py-2 pr-4">${sectorLabels[item.sector] ?? item.sector}</td>
          <td class="py-2 pr-4">${item.type === 'product' ? 'Producto' : 'Servicio'}</td>
          <td class="py-2 pr-4">${stockBadge}</td>
          <td class="py-2 pr-4">${minStockLabel}</td>
          <td class="py-2 pr-4">${formatMoney(item.sale_price)}</td>
          <td class="py-2 pr-4">${activeBadge}</td>
          <td class="py-2 pr-2 text-right">
            <button type="button" class="text-[color:var(--dw-primary)] hover:underline mr-2" data-edit-id="${item.id}">Editar</button>
            <button type="button" class="text-rose-500 hover:underline" data-delete-id="${item.id}">Desactivar</button>
          </td>
        `;
        tableBody.appendChild(tr);
      });
    }

    function renderPagination() {
      if (!state.total) {
        paginationInfo.textContent = 'Sin resultados';
        btnPrev.disabled = true;
        btnNext.disabled = true;
        return;
      }

      const from = (state.page - 1) * state.perPage + 1;
      const to = Math.min(state.page * state.perPage, state.total);
      paginationInfo.textContent = `Mostrando ${from} - ${to} de ${state.total} ítems`;

      btnPrev.disabled = state.page <= 1;
      btnNext.disabled = state.page >= state.lastPage;
    }

    function extractErrorMessage(error) {
      if (error.response?.data?.message) {
        return error.response.data.message;
      }
      if (error.response?.data?.errors) {
        const firstKey = Object.keys(error.response.data.errors)[0];
        if (firstKey) {
          return error.response.data.errors[firstKey][0];
        }
      }
      return 'Ocurrió un error inesperado. Intenta de nuevo.';
    }

    async function loadItems(page = 1) {
      state.loading = true;
      renderTable();
      try {
        const { data } = await axiosInstance.get('/api/items', {
          params: {
            sector: state.sector,
            search: state.search,
            page,
          },
        });
        state.items = Array.isArray(data.data) ? data.data : [];
        state.page = data.pagination?.current_page ?? page;
        state.perPage = data.pagination?.per_page ?? state.perPage;
        state.lastPage = data.pagination?.last_page ?? state.lastPage;
        state.total = data.pagination?.total ?? state.total;
        state.loading = false;
        renderTable();
        renderPagination();
      } catch (error) {
        state.items = [];
        state.total = 0;
        state.loading = false;
        renderTable();
        renderPagination();
        showPageAlert('error', extractErrorMessage(error));
      }
    }

    function resetForm() {
      form.reset();
      fields.name.value = '';
      fields.sector.value = state.sector === 'papeleria' ? defaultCreateSector() : state.sector;
      fields.salePrice.value = '0';
      fields.cost.value = '';
      fields.unit.value = '';
      fields.active.checked = true;
      fields.stockable.checked = false;
      fields.stock.value = '';
      fields.minStock.value = '';
      fields.description.value = '';
      if (fields.barcode) fields.barcode.value = '';
      if (fields.color) fields.color.value = 'N/A';
      if (fields.scanMode) fields.scanMode.value = 'unit';
      if (fields.packSize) fields.packSize.value = '';
      if (fields.barcodeSource) fields.barcodeSource.value = state.sector === 'papeleria' ? 'internal' : 'manufacturer';
      state.editing = null;
      state.costSuggested = false;
      state.priceWarningAcknowledged = false;
      state.priceWarningSignature = null;
      state.priceWarningOpen = false;
      state.pendingPayload = null;
      updateLabelActions(null);
      updateStockVisibility();
      updateSuggestedPriceHint();
      clearModalAlert();
    }

    function openModal() {
      modal.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      clearModalAlert();
      closePriceConfirm(true);
      state.priceWarningAcknowledged = false;
      state.priceWarningOpen = false;
    }

    function openNew() {
      if (state.sector === 'papeleria') {
        window.location.href = purchasesUrl;
        return;
      }
      resetForm();
      modalTitle.textContent = 'Nuevo producto';
      openModal();
    }

    function fillForm(item) {
      state.editing = item;
      modalTitle.textContent = 'Editar producto';
      fields.name.value = item.name ?? '';
      fields.sector.value = item.sector ?? state.sector;
      fields.salePrice.value = Number(item.sale_price ?? 0);
      fields.cost.value = item.cost ?? '';
      fields.unit.value = item.unit ?? '';
      fields.active.checked = Boolean(item.active);
      fields.stockable.checked = item.type === 'product';
      fields.stock.value = item.stock ?? '';
      fields.minStock.value = item.min_stock ?? '';
      fields.description.value = item.description ?? '';
      if (fields.barcode) fields.barcode.value = item.barcode ?? '';
      if (fields.color) fields.color.value = item.color ?? 'N/A';
      if (fields.scanMode) fields.scanMode.value = item.scan_mode ?? 'unit';
      if (fields.packSize) fields.packSize.value = item.pack_size ?? '';
      if (fields.barcodeSource) fields.barcodeSource.value = item.barcode_source ?? 'manufacturer';
      state.costSuggested = false;
      state.priceWarningAcknowledged = false;
      updateLabelActions(item.id);
      updateStockVisibility();
      updatePriceWarning();
      updateSuggestedPriceHint();
      clearModalAlert();
      openModal();
    }

    function getPayloadFromForm() {
      const payload = {
        name: fields.name.value.trim(),
        sector: fields.sector.value,
        sale_price: Number(fields.salePrice.value || 0),
        cost: fields.cost.value !== '' ? Number(fields.cost.value) : null,
        unit: fields.unit.value.trim() || null,
        active: fields.active.checked,
        type: fields.stockable.checked ? 'product' : 'service',
        description: fields.description.value.trim() || null,
      };

      if (payload.type === 'product') {
        payload.stock = fields.stock.value !== '' ? Number(fields.stock.value) : 0;
        payload.min_stock = fields.minStock.value !== '' ? Number(fields.minStock.value) : 0;
      } else {
        payload.stock = 0;
        payload.min_stock = 0;
      }

      if (payload.sector === 'papeleria' && payload.type === 'product') {
        payload.barcode = fields.barcode?.value.trim() || null;
        payload.color = fields.color?.value.trim() || 'N/A';
        payload.scan_mode = fields.scanMode?.value || 'unit';
        payload.pack_size = fields.packSize?.value !== '' ? Number(fields.packSize.value) : null;
        payload.barcode_source = fields.barcodeSource?.value || null;
      }

      if (!payload.cost) {
        delete payload.cost;
      }
      if (!payload.unit) {
        delete payload.unit;
      }
      if (!payload.description) {
        delete payload.description;
      }

      return payload;
    }

    function openPriceConfirm(cost, sale) {
      if (!priceConfirmModal || !priceConfirmMsg) return;
      if (state.priceWarningOpen) return;

      priceConfirmMsg.textContent = sale < cost
        ? 'Atención: este precio genera pérdidas (el costo es mayor que el precio de venta). ¿Deseas continuar de todos modos?'
        : 'Atención: este precio no genera ganancia (precio igual al costo). ¿Deseas continuar de todos modos?';

      priceConfirmModal.classList.remove('hidden');
      state.priceWarningOpen = true;
    }

    function closePriceConfirm(resetPending = false) {
      if (!priceConfirmModal) return;
      priceConfirmModal.classList.add('hidden');
      state.priceWarningOpen = false;
      if (resetPending) {
        state.pendingPayload = null;
      }
    }

    async function saveItem(event) {
      event.preventDefault();
      clearModalAlert();

      const payload = getPayloadFromForm();
      const cost = Number(fields.cost.value || 0);
      const sale = Number(fields.salePrice.value || 0);
      state.pendingPayload = payload;

      if (!state.priceWarningAcknowledged && shouldWarn(cost, sale)) {
        openPriceConfirm(cost, sale);
        return;
      }

      if (!payload.name) {
        showModalAlert('error', 'El nombre es obligatorio.');
        return;
      }

      modalSave.disabled = true;
      modalSave.textContent = 'Guardando…';

      try {
        if (state.editing) {
          await axiosInstance.put(`/api/items/${state.editing.id}`, payload);
          showPageAlert('success', 'Ítem actualizado correctamente.');
        } else {
          await axiosInstance.post('/api/items', payload);
          showPageAlert('success', 'Ítem creado correctamente.');
        }
        closeModal();
        await loadItems(state.editing ? state.page : 1);
      } catch (error) {
        showModalAlert('error', extractErrorMessage(error));
      } finally {
        modalSave.disabled = false;
        modalSave.textContent = 'Guardar';
        state.priceWarningOpen = false;
        state.pendingPayload = null;
      }
    }

    async function deleteItem(id) {
      const item = state.items.find((it) => Number(it.id) === Number(id));
      if (!item) {
        return;
      }
      const confirmed = confirm(`¿Desactivar "${item.name}"?`);
      if (!confirmed) {
        return;
      }
      try {
        await axiosInstance.delete(`/api/items/${item.id}`);
        showPageAlert('success', 'Ítem desactivado.');
        const shouldGoBack = state.items.length === 1 && state.page > 1;
        await loadItems(shouldGoBack ? state.page - 1 : state.page);
      } catch (error) {
        showPageAlert('error', extractErrorMessage(error));
      }
    }

    // Eventos UI
    tabButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const sector = btn.dataset.sector;
        if (sector === state.sector) {
          return;
        }
        state.sector = sector;
        state.page = 1;
        toggleTabs();
        togglePapeleriaColumns();
        loadItems(1);
      });
    });

    let searchTimer = null;
    searchBox.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => {
        state.search = searchBox.value.trim();
        state.page = 1;
        loadItems(1);
      }, 350);
    });

    btnPrev.addEventListener('click', () => {
      if (state.page <= 1) {
        return;
      }
      const newPage = state.page - 1;
      loadItems(newPage);
    });

    btnNext.addEventListener('click', () => {
      if (state.page >= state.lastPage) {
        return;
      }
      const newPage = state.page + 1;
      loadItems(newPage);
    });

    tableBody.addEventListener('click', (event) => {
      const editBtn = event.target.closest('[data-edit-id]');
      if (editBtn) {
        const id = Number(editBtn.dataset.editId);
        const item = state.items.find((it) => Number(it.id) === id);
        if (item) {
          fillForm(item);
        }
        return;
      }
      const deleteBtn = event.target.closest('[data-delete-id]');
      if (deleteBtn) {
        const id = Number(deleteBtn.dataset.deleteId);
        deleteItem(id);
      }
    });

    fields.stockable.addEventListener('change', () => {
      updateStockVisibility();
    });

    fields.sector?.addEventListener('change', () => {
      updatePapeleriaVisibility();
      updateSuggestedPriceHint();
    });

    fields.salePrice.addEventListener('input', () => {
      state.costSuggested = false;
      updatePriceWarning();
    });
    fields.cost.addEventListener('input', () => {
      updatePriceWarning();
      updateSuggestedPriceHint();
      applySuggestedPriceFromCost();
    });

    fields.btnGenBarcode?.addEventListener('click', async () => {
      try {
        const { data } = await axiosInstance.get('/api/items/next-barcode');
        if (data?.barcode && fields.barcode) {
          fields.barcode.value = data.barcode;
          if (fields.barcodeSource) fields.barcodeSource.value = 'internal';
        }
      } catch (error) {
        showModalAlert('error', extractErrorMessage(error));
      }
    });

    fields.btnScanBarcode?.addEventListener('click', () => {
      if (!window.dwOpenBarcodeScanner) {
        showModalAlert('error', 'Escáner no disponible. Recarga la página.');
        return;
      }
      window.dwOpenBarcodeScanner({
        parentModal: modal,
        onDetected: (code) => {
          if (fields.barcode) fields.barcode.value = code;
          if (fields.barcodeSource && !/^DWY-/i.test(code)) {
            fields.barcodeSource.value = 'manufacturer';
          }
        },
        onError: () => {
          showModalAlert('error', 'No se pudo acceder a la cámara.');
          if (window.dwShowToast) window.dwShowToast('No se pudo acceder a la cámara.', 'error');
        },
        onScanError: (message) => {
          showModalAlert('error', message);
          if (window.dwShowToast) window.dwShowToast(message, 'error');
        },
      });
    });

    fields.btnLabelSheet?.addEventListener('click', () => {
      if (!state.editing?.id) return;
      window.open(`/api/items/labels/sheet?ids[]=${state.editing.id}`, '_blank');
    });

    btnSheetLabels?.addEventListener('click', () => {
      const ids = state.items.filter((item) => item.barcode).map((item) => item.id);
      if (!ids.length) {
        showPageAlert('error', 'No hay productos con código en esta página.');
        return;
      }
      const query = ids.map((id) => `ids[]=${id}`).join('&');
      window.open(`/api/items/labels/sheet?${query}`, '_blank');
    });

    btnNew.addEventListener('click', () => {
      openNew();
    });

    modalClose.addEventListener('click', () => {
      closeModal();
    });

    modalCancel.addEventListener('click', () => {
      closeModal();
    });

    modal.addEventListener('click', (event) => {
      if (event.target.dataset.close) {
        closeModal();
      }
    });

    priceConfirmCancel?.addEventListener('click', () => {
      closePriceConfirm(true);
      state.priceWarningAcknowledged = false;
      fields.salePrice?.focus();
    });
    priceConfirmModal?.addEventListener('click', (e) => {
      if (e.target.dataset.pcClose) {
        closePriceConfirm(true);
        state.priceWarningAcknowledged = false;
      }
    });
    priceConfirmProceed?.addEventListener('click', () => {
      state.priceWarningAcknowledged = true;
      closePriceConfirm(false);
      if (state.pendingPayload) {
        form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
      }
    });

    form.addEventListener('submit', saveItem);

    // Estado inicial
    toggleTabs();
    togglePapeleriaColumns();
    updateStockVisibility();
    loadItems(state.page);
  });
</script>
@endpush
