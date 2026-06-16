{{-- resources/views/sales/partials/modal-create.blade.php --}}
{{-- Modal: Registrar venta (POS pro) --}}

<div id="saleModal" class="fixed inset-0 z-[70] hidden">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-pos-dismiss></div>

  <div class="dw-pos-center">
    <div class="dw-pos-modal" role="dialog" aria-labelledby="saleModalTitle" aria-modal="true">

      <header class="dw-pos-header">
        <div class="flex items-center gap-2.5">
          <h3 id="saleModalTitle" class="font-display text-base font-bold text-dw-text">Registrar venta</h3>
          <span id="saleItemsCounter" class="dw-badge-primary text-[10px]">0 productos</span>
        </div>
        <button id="closeSaleModal" type="button" class="dw-pos-btn-square" aria-label="Cerrar">
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </header>

      <div class="dw-pos-body">
        <div class="dw-pos-work">
          <div class="dw-pos-toolbar">
            <div class="dw-pos-toolbar__row dw-pos-toolbar__row--meta">
              <div class="dw-pos-customer-zone">
                <button id="toggleCustomerBar" type="button" class="dw-pos-customer-toggle" aria-expanded="false" aria-controls="customerBarPanel">
                  <span class="material-symbols-outlined text-[18px]">person</span>
                  <span id="customerToggleLabel" class="max-w-[9rem] truncate">Cliente</span>
                  <span class="material-symbols-outlined text-[16px] text-dw-muted">expand_more</span>
                </button>
                <div id="customerBarPanel" class="dw-pos-customer-panel hidden">
                  <input type="hidden" id="c_id">
                  <input type="hidden" id="c_name">
                  <input type="hidden" id="c_email">
                  <input type="hidden" id="c_phone">
                  <input id="c_document" type="text" placeholder="Cédula" aria-label="Cédula del cliente"
                         class="dw-pos-input dw-pos-input--doc">
                  <button id="searchCustomer" type="button" class="dw-pos-btn-square" title="Buscar cliente">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                  </button>
                  <button id="newCustomer" type="button" class="dw-pos-btn-square dw-pos-btn-square--primary" title="Nuevo cliente">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                  </button>
                  <button id="clearCustomer" type="button" class="dw-pos-btn-square" title="Quitar cliente">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                  </button>
                </div>
              </div>

              <div class="dw-pos-sector-scroll">
                <div class="dw-pos-sector-filters" id="posSectorFilters" role="group" aria-label="Filtrar por sector">
                  <button type="button" class="dw-pos-segment is-active" data-pos-sector="all">Todos</button>
                  <button type="button" class="dw-pos-segment" data-pos-sector="impresion">Impresión</button>
                  <button type="button" class="dw-pos-segment" data-pos-sector="papeleria">Papelería</button>
                  <button type="button" class="dw-pos-segment" data-pos-sector="diseno">Diseño</button>
                </div>
              </div>
            </div>

            <div class="dw-pos-toolbar__entry">
            <div class="dw-pos-toolbar__row dw-pos-toolbar__row--search">
              <div class="dw-pos-product-wrap" id="productCombobox">
                <input id="p_product" type="text" placeholder="Buscar o escanear código…"
                       class="dw-pos-input dw-pos-input--product w-full" autocomplete="off" spellcheck="false"
                       role="combobox" aria-expanded="false" aria-controls="productComboboxList"
                       aria-autocomplete="list" aria-label="Buscar producto o escanear código">
                <span id="p_category_badge" class="dw-pos-input-badge hidden"></span>
                <ul id="productComboboxList" class="dw-pos-combobox hidden" role="listbox" aria-label="Productos"></ul>
              </div>
              <button id="btnScanProduct" type="button" class="dw-pos-btn-square dw-pos-btn-square--scan" title="Escanear código">
                <span class="material-symbols-outlined text-[18px]">qr_code_scanner</span>
              </button>
            </div>

            <div class="dw-pos-toolbar__row dw-pos-toolbar__row--amounts">
              <label class="dw-pos-field">
                <span class="dw-pos-field__label">Cant.</span>
                <input id="p_qty" type="number" min="1" value="1" aria-label="Cantidad"
                       class="dw-pos-input dw-pos-input--qty">
              </label>
              <label class="dw-pos-field">
                <span class="dw-pos-field__label">Valor u.</span>
                <input id="p_unit" type="text" inputmode="numeric" placeholder="0" aria-label="Valor unitario"
                       class="dw-pos-input dw-pos-input--money">
              </label>
              <label class="dw-pos-field">
                <span class="dw-pos-field__label">Subtotal</span>
                <input id="p_total" type="text" inputmode="numeric" placeholder="0" aria-label="Subtotal" readonly
                       class="dw-pos-input dw-pos-input--money dw-pos-input--money-readonly">
              </label>
              <button id="addProductLine" type="button" class="dw-pos-btn-add" title="Agregar (Enter)">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span class="dw-pos-btn-add__text">Agregar</span>
              </button>
            </div>
            </div>

            <p id="qty_error" class="dw-pos-toolbar-error hidden"></p>
            <p id="stock_info" class="sr-only" aria-live="polite"></p>
          </div>

          <div class="dw-pos-cart">
            <div id="saleCartEmpty" class="dw-pos-empty">
              <span class="material-symbols-outlined text-2xl text-dw-muted/60">shopping_cart</span>
              <p class="text-sm font-medium text-dw-text">Carrito vacío</p>
              <p class="mt-1 max-w-xs text-center text-xs leading-relaxed text-dw-muted">Busca un producto, escanea el código, ajusta cantidad y pulsa Agregar.</p>
            </div>
            <table id="saleCartTable" class="dw-pos-table hidden">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th class="w-20">Sector</th>
                  <th class="w-12 text-center">Cant.</th>
                  <th class="w-20 text-right">Valor</th>
                  <th class="w-24 text-right">Total</th>
                  <th class="w-8"></th>
                </tr>
              </thead>
              <tbody id="saleItemsList"></tbody>
            </table>
          </div>
        </div>

        <aside class="dw-pos-rail">
          <div>
            <p class="dw-label mb-1">Total venta</p>
            <p id="sale_total" class="dw-pos-total">$0</p>
          </div>

          <div>
            <p class="dw-label mb-1.5">Método</p>
            <div class="dw-pos-segments" id="payMethodSegments" role="group" aria-label="Método de pago">
              <button type="button" class="dw-pos-segment is-active" data-pay-method="cash">Efectivo</button>
              <button type="button" class="dw-pos-segment" data-pay-method="transfer">Transf.</button>
              <button type="button" class="dw-pos-segment" data-pay-method="card">Tarjeta</button>
            </div>
            <select id="pay_method_extra" class="dw-pos-input mt-1.5 w-full text-xs" aria-label="Otros métodos de pago">
              <option value="">Más opciones…</option>
              <option value="mixed">Mixto</option>
              <option value="other">Otro</option>
            </select>
            <select id="pay_method" class="sr-only" tabindex="-1" aria-hidden="true">
              <option value="cash">Efectivo</option>
              <option value="transfer">Transferencia</option>
              <option value="card">Tarjeta</option>
              <option value="mixed">Mixto</option>
              <option value="other">Otro</option>
            </select>
          </div>

          <div id="payCashFields" class="grid grid-cols-2 gap-2">
            <div>
              <label class="dw-label mb-1" for="pay_given">Recibido</label>
              <input id="pay_given" type="text" inputmode="numeric" placeholder="0"
                     class="dw-pos-input dw-pos-input--money w-full">
            </div>
            <div>
              <label class="dw-label mb-1" for="pay_change">Devuelta</label>
              <input id="pay_change" type="text" inputmode="numeric" placeholder="0" readonly
                     class="dw-pos-input dw-pos-input--money dw-pos-input--money-readonly w-full">
            </div>
          </div>

          <div class="dw-pos-rail-actions">
            <button id="saveSale" type="button" class="dw-btn-primary w-full py-2.5" disabled>Registrar venta</button>
            <button id="cancelSale" type="button" class="dw-btn-ghost w-full py-1.5 text-xs">Cancelar</button>
          </div>
        </aside>
      </div>
    </div>
  </div>
</div>

{{-- Modal rápido: Crear cliente --}}
<div id="customerCreateModal" class="fixed inset-0 z-[80] hidden">
  <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="w-full max-w-lg space-y-4 rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-dw-text">Registrar cliente</h3>
        <button id="closeCustomerModal" class="text-dw-muted hover:text-dw-text">X</button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm text-dw-muted mb-1" for="nc_document">Cédula / ID</label>
          <input id="nc_document" type="text"
                 class="dw-input">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-dw-muted mb-1" for="nc_name">Nombre</label>
          <input id="nc_name" type="text"
                 class="dw-input">
        </div>
        <div>
          <label class="block text-sm text-dw-muted mb-1" for="nc_email">Correo</label>
          <input id="nc_email" type="email"
                 class="dw-input">
        </div>
        <div>
          <label class="block text-sm text-dw-muted mb-1" for="nc_phone">Teléfono</label>
          <input id="nc_phone" type="text"
                 class="dw-input">
        </div>
      </div>
      <div class="pt-3 flex gap-3 justify-end">
        <button id="cancelCustomerModal" type="button" class="dw-btn-secondary">Cancelar</button>
        <button id="saveCustomerModal" type="button" class="dw-btn-primary">Guardar cliente</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal rápido: Alta por código (solo admin) --}}
<div id="barcodeQuickCreateModal" class="fixed inset-0 z-[85] hidden">
  <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-bqc-dismiss></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="w-full max-w-md space-y-4 rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-dw-text">Registrar producto</h3>
        <button id="closeBarcodeQuickModal" type="button" class="text-dw-muted hover:text-dw-text">X</button>
      </div>
      <p class="text-sm text-dw-muted">Código no encontrado. Completa los datos para crear el ítem en papelería.</p>
      <input type="hidden" id="bqc_barcode">
      <div>
        <label class="dw-label mb-1" for="bqc_name">Nombre</label>
        <input id="bqc_name" type="text" class="dw-input">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="dw-label mb-1" for="bqc_cost">Costo</label>
          <input id="bqc_cost" type="number" min="0" step="0.01" class="dw-input">
        </div>
        <div>
          <label class="dw-label mb-1" for="bqc_price">Precio venta</label>
          <input id="bqc_price" type="number" min="0" step="0.01" class="dw-input">
        </div>
      </div>
      <div>
        <label class="dw-label mb-1" for="bqc_stock">Stock inicial</label>
        <input id="bqc_stock" type="number" min="0" value="0" class="dw-input">
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button id="cancelBarcodeQuickModal" type="button" class="dw-btn-secondary">Cancelar</button>
        <button id="saveBarcodeQuickModal" type="button" class="dw-btn-primary">Crear y cargar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function onlyDigits(str){ return (str || '').replace(/[^\d]/g,''); }
function toInt(str){ const s = onlyDigits(str); return s ? parseInt(s,10) : 0; }
function formatCOP(num){
  let n = Number(num || 0);
  if(!Number.isFinite(n)) n = 0;
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

@php $modalCatalogDataset = $catalogDataset ?? []; @endphp
const DATASET = @json($modalCatalogDataset, JSON_UNESCAPED_UNICODE);
const DEFAULT_BADGE = '';
let productDataset = JSON.parse(JSON.stringify(DATASET));
const IS_ADMIN = @json(auth()->user()?->isAdmin() ?? false);

const SECTOR_LABELS = {
  impresion: 'Impresión',
  papeleria: 'Papelería',
  diseno: 'Diseño',
};

const saleModal = document.getElementById('saleModal');
const openSaleButtons = document.querySelectorAll('#openSaleModal, .js-open-sale-modal');
const closeBtn  = document.getElementById('closeSaleModal');
const cancelBtn = document.getElementById('cancelSale');
const saveBtn   = document.getElementById('saveSale');

const selProduct = document.getElementById('p_product');
const productComboboxList = document.getElementById('productComboboxList');
const productComboboxRoot = document.getElementById('productCombobox');
const badgeCat   = document.getElementById('p_category_badge');
const inpQty     = document.getElementById('p_qty');
const inpUnit    = document.getElementById('p_unit');
const inpTotal   = document.getElementById('p_total');
const selPayMethod = document.getElementById('pay_method');
const inpGiven   = document.getElementById('pay_given');
const inpChange  = document.getElementById('pay_change');
const saleTotal  = document.getElementById('sale_total');
const saleCartEmpty = document.getElementById('saleCartEmpty');
const saleCartTable = document.getElementById('saleCartTable');
const payMethodSegments = document.getElementById('payMethodSegments');
const payMethodExtra = document.getElementById('pay_method_extra');
const payCashFields = document.getElementById('payCashFields');
const inpCustomerId = document.getElementById('c_id');
const inpCustomerDocument = document.getElementById('c_document');
const inpName    = document.getElementById('c_name');
const inpEmail   = document.getElementById('c_email');
const inpPhone   = document.getElementById('c_phone');
const stockInfo  = document.getElementById('stock_info');
const qtyError   = document.getElementById('qty_error');
const saleItemsList = document.getElementById('saleItemsList');
const saleItemsCounter = document.getElementById('saleItemsCounter');
const btnSearchCustomer = document.getElementById('searchCustomer');
const btnNewCustomer = document.getElementById('newCustomer');
const btnAddProductLine = document.getElementById('addProductLine');
const btnScanProduct = document.getElementById('btnScanProduct');
const btnClearCustomer = document.getElementById('clearCustomer');
const customerModal = document.getElementById('customerCreateModal');
const customerModalClose = document.getElementById('closeCustomerModal');
const customerModalCancel = document.getElementById('cancelCustomerModal');
const customerModalSave = document.getElementById('saveCustomerModal');
const ncDocument = document.getElementById('nc_document');
const ncName = document.getElementById('nc_name');
const ncEmail = document.getElementById('nc_email');
const ncPhone = document.getElementById('nc_phone');
const barcodeQuickModal = document.getElementById('barcodeQuickCreateModal');
const bqcBarcode = document.getElementById('bqc_barcode');
const bqcName = document.getElementById('bqc_name');
const bqcCost = document.getElementById('bqc_cost');
const bqcPrice = document.getElementById('bqc_price');
const bqcStock = document.getElementById('bqc_stock');

const MARKUP_PERCENT = 40;
let barcodeLookupBusy = false;

let saleToast = null;
let saleToastText = null;
let saleToastTimer = null;

let saleLines = [];
let saleLineId = 1;
let selectedCustomerId = null;
const customerToggle = document.getElementById('toggleCustomerBar');
const customerToggleLabel = document.getElementById('customerToggleLabel');
const customerBarPanel = document.getElementById('customerBarPanel');
let productIndex = [];
let productById = new Map();
let activeProductId = null;
let comboboxOpen = false;
let comboboxResults = [];
let comboboxHighlight = -1;
const COMBOBOX_LIMIT = 8;
let posSectorFilter = 'all';
const posSectorFilters = document.getElementById('posSectorFilters');

function showSaleToast(message) {
  if (!saleToast || !saleToastText) return;
  saleToastText.textContent = message;
  saleToast.classList.remove('hidden');

  if (saleToastTimer) {
    clearTimeout(saleToastTimer);
  }
  saleToastTimer = setTimeout(() => {
    saleToast.classList.add('hidden');
  }, 3500);
}

function setCategoryBadge(label) {
  if (!badgeCat) return;
  const text = (label || '').trim();
  if (!text) {
    badgeCat.textContent = '';
    badgeCat.classList.add('hidden');
    return;
  }
  badgeCat.textContent = text;
  badgeCat.classList.remove('hidden');
}

function setCustomerBarOpen(open) {
  if (!customerBarPanel || !customerToggle) return;
  customerBarPanel.classList.toggle('hidden', !open);
  customerToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
}

function toggleCustomerBar() {
  if (!customerBarPanel) return;
  setCustomerBarOpen(customerBarPanel.classList.contains('hidden'));
}

function updateCustomerToggle(){
  if (!customerToggleLabel || !customerToggle) return;
  if (selectedCustomerId) {
    const name = (inpName?.value || '').trim() || 'Cliente';
    const doc = (inpCustomerDocument?.value || '').trim();
    customerToggleLabel.textContent = doc ? `${name}` : name;
    customerToggle.classList.add('is-active');
  } else {
    customerToggleLabel.textContent = 'Cliente';
    customerToggle.classList.remove('is-active');
  }
}

function initCustomerBar() {
  customerToggle?.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleCustomerBar();
  });

  document.addEventListener('mousedown', (e) => {
    if (!customerBarPanel || customerBarPanel.classList.contains('hidden')) return;
    if (customerToggle?.contains(e.target) || customerBarPanel.contains(e.target)) return;
    setCustomerBarOpen(false);
  });
}

function setPayMethod(method) {
  const value = method || 'cash';
  if (selPayMethod) selPayMethod.value = value;

  const isSegment = ['cash', 'transfer', 'card'].includes(value);
  payMethodSegments?.querySelectorAll('[data-pay-method]').forEach((btn) => {
    btn.classList.toggle('is-active', btn.dataset.payMethod === value);
  });

  if (payMethodExtra) {
    payMethodExtra.value = isSegment ? '' : value;
  }

  const showCash = value === 'cash';
  if (payCashFields) {
    payCashFields.classList.toggle('hidden', !showCash);
  }
}

function initPayMethodControls() {
  payMethodSegments?.querySelectorAll('[data-pay-method]').forEach((btn) => {
    btn.addEventListener('click', () => setPayMethod(btn.dataset.payMethod));
  });

  payMethodExtra?.addEventListener('change', () => {
    const value = payMethodExtra.value;
    if (value) {
      setPayMethod(value);
    }
  });
}

function setCustomer(data){
  selectedCustomerId = data?.id ?? null;
  if (inpCustomerId) inpCustomerId.value = selectedCustomerId || '';
  if (inpCustomerDocument) inpCustomerDocument.value = data?.document || '';
  if (inpName) inpName.value = data?.name || '';
  if (inpEmail) inpEmail.value = data?.email || '';
  if (inpPhone) inpPhone.value = data?.phone || '';
  updateCustomerToggle();
  if (selectedCustomerId) setCustomerBarOpen(false);
}

function clearCustomer(){
  setCustomer({ id:null, document:'', name:'', email:'', phone:'' });
}

function looksLikeBarcode(value) {
  const code = (value || '').trim();
  if (code.length < 3) return false;
  return /^[A-Za-z0-9][A-Za-z0-9\-_.]+$/.test(code);
}

function registerCatalogItem(apiItem) {
  const sector = apiItem.sector || 'papeleria';
  if (!productDataset[sector]) productDataset[sector] = [];
  const exists = productDataset[sector].some((p) => Number(p.id) === Number(apiItem.id));
  if (!exists) {
    productDataset[sector].push({
      id: apiItem.id,
      name: apiItem.name,
      unit: apiItem.sale_price ?? 0,
      stock: apiItem.type === 'product' ? (apiItem.stock ?? null) : null,
      type: apiItem.type,
    });
    renderProducts();
  }
}

function applyApiItemToPos(apiItem) {
  registerCatalogItem(apiItem);
  const meta = {
    id: apiItem.id,
    name: apiItem.name,
    category: apiItem.sector || 'papeleria',
    unit: apiItem.sale_price ?? 0,
    stock: apiItem.type === 'product' ? (apiItem.stock ?? null) : null,
    type: apiItem.type || 'product',
    label: apiItem.name,
  };
  productById.set(String(meta.id), meta);
  closeProductCombobox();
  applyProductMeta(meta);
  if (selProduct) selProduct.value = meta.name;
  requestAnimationFrame(() => {
    inpQty?.focus();
    inpQty?.select();
  });
}

async function lookupBarcode(code) {
  const trimmed = (code || '').trim();
  if (!trimmed || barcodeLookupBusy) return false;

  const axiosInstance = window.axios;
  if (!axiosInstance) {
    showSaleToast('No se encontró Axios en la página.');
    return false;
  }

  barcodeLookupBusy = true;
  try {
    const { data } = await axiosInstance.get(`/api/items/by-barcode/${encodeURIComponent(trimmed)}`);
    if (data?.ok && data.item) {
      applyApiItemToPos(data.item);
      showSaleToast('Producto cargado. Escribe la cantidad.');
      return true;
    }
  } catch (error) {
    if (error.response?.status === 404) {
      if (IS_ADMIN) {
        openBarcodeQuickModal(trimmed);
      } else {
        showSaleToast('Código no registrado. Consulta con un administrador.');
      }
      return false;
    }
    showSaleToast('No se pudo buscar el código.');
  } finally {
    barcodeLookupBusy = false;
  }
  return false;
}

function openBarcodeQuickModal(code) {
  if (!barcodeQuickModal) return;
  bqcBarcode.value = code;
  bqcName.value = '';
  bqcCost.value = '';
  bqcPrice.value = '';
  bqcStock.value = '0';
  barcodeQuickModal.classList.remove('hidden');
  bqcName?.focus();
}

function closeBarcodeQuickModal() {
  barcodeQuickModal?.classList.add('hidden');
}

function suggestedQuickPrice(cost) {
  const c = Number(cost || 0);
  if (!Number.isFinite(c) || c <= 0) return 0;
  return Math.round(c * (1 + MARKUP_PERCENT / 100));
}

async function saveBarcodeQuickModal() {
  const axiosInstance = window.axios;
  if (!axiosInstance) return;

  const name = (bqcName?.value || '').trim();
  if (!name) {
    showSaleToast('El nombre es obligatorio.');
    return;
  }

  const cost = Number(bqcCost?.value || 0);
  const price = Number(bqcPrice?.value || 0) || suggestedQuickPrice(cost);

  try {
    const payload = {
      name,
      sector: 'papeleria',
      type: 'product',
      sale_price: price,
      cost: cost > 0 ? cost : null,
      stock: Number(bqcStock?.value || 0),
      min_stock: 0,
      barcode: bqcBarcode?.value || null,
      barcode_source: /^DWY-/i.test(bqcBarcode?.value || '') ? 'internal' : 'manufacturer',
      scan_mode: 'unit',
      color: 'N/A',
      active: true,
    };
    const { data } = await axiosInstance.post('/api/items/papeleria/quick', payload);
    if (data?.item) {
      applyApiItemToPos(data.item);
      closeBarcodeQuickModal();
      showSaleToast('Producto creado. Escribe la cantidad.');
    }
  } catch (error) {
    const msg = error.response?.data?.errors
      ? Object.values(error.response.data.errors)[0]?.[0]
      : (error.response?.data?.message || 'No se pudo crear el producto.');
    showSaleToast(msg);
  }
}

function renderProducts(){
  productIndex = [];
  productById = new Map();

  Object.keys(productDataset).forEach(cat => {
    productDataset[cat].forEach(p => {
      const item = {
        id: p.id,
        name: p.name,
        category: cat,
        unit: p.unit,
        stock: p.stock ?? null,
        type: p.type || '',
        label: p.name,
      };
      productIndex.push(item);
      productById.set(String(p.id), item);
    });
  });

  productIndex.sort((a, b) => a.name.localeCompare(b.name, 'es'));
}

function normalizeSearch(value) {
  return (value || '').trim().toLowerCase();
}

function filterProducts(query) {
  const q = normalizeSearch(query);
  let pool = productIndex;

  if (posSectorFilter !== 'all') {
    pool = pool.filter((item) => item.category === posSectorFilter);
  }

  if (!q) {
    return pool.slice(0, COMBOBOX_LIMIT);
  }

  const starts = [];
  const contains = [];

  pool.forEach((item) => {
    const name = item.name.toLowerCase();
    if (name.startsWith(q)) {
      starts.push(item);
    } else if (name.includes(q)) {
      contains.push(item);
    }
  });

  return [...starts, ...contains].slice(0, COMBOBOX_LIMIT);
}

function updatePosSearchPlaceholder() {
  if (!selProduct) return;
  const placeholders = {
    all: 'Buscar o escanear código…',
    papeleria: 'Escanear o buscar papelería…',
    impresion: 'Buscar servicio de impresión…',
    diseno: 'Buscar servicio de diseño…',
  };
  selProduct.placeholder = placeholders[posSectorFilter] || placeholders.all;
}

function initPosSectorFilters() {
  posSectorFilters?.querySelectorAll('[data-pos-sector]').forEach((btn) => {
    btn.addEventListener('click', () => {
      posSectorFilter = btn.dataset.posSector || 'all';
      posSectorFilters.querySelectorAll('[data-pos-sector]').forEach((el) => {
        el.classList.toggle('is-active', el.dataset.posSector === posSectorFilter);
      });
      updatePosSearchPlaceholder();
      closeProductCombobox();
      if (posSectorFilter === 'papeleria') {
        btnScanProduct?.focus();
      } else {
        selProduct?.focus();
      }
    });
  });
  updatePosSearchPlaceholder();
}

function highlightQuery(text, query) {
  const q = normalizeSearch(query);
  if (!q || !text) return text;

  const lower = text.toLowerCase();
  const idx = lower.indexOf(q);
  if (idx === -1) return text;

  const before = text.slice(0, idx);
  const match = text.slice(idx, idx + q.length);
  const after = text.slice(idx + q.length);
  return `${before}<mark>${match}</mark>${after}`;
}

function formatProductMeta(item) {
  const parts = [getCategoryLabel(item.category)];
  parts.push('$' + formatCOP(item.unit || 0));

  if (item.stock === null || item.type === 'service') {
    parts.push('Servicio');
  } else if (item.stock <= 0) {
    parts.push('Sin stock');
  } else {
    parts.push(`Stock ${item.stock}`);
  }

  return parts.join(' · ');
}

function setComboboxAria(open) {
  if (!selProduct) return;
  selProduct.setAttribute('aria-expanded', open ? 'true' : 'false');
}

function closeProductCombobox() {
  comboboxOpen = false;
  comboboxHighlight = -1;
  comboboxResults = [];
  productComboboxList?.classList.add('hidden');
  productComboboxList && (productComboboxList.innerHTML = '');
  setComboboxAria(false);
}

function highlightComboboxItem(index) {
  comboboxHighlight = index;
  productComboboxList?.querySelectorAll('[data-combobox-option]').forEach((el, i) => {
    el.classList.toggle('is-highlighted', i === index);
    if (i === index) {
      el.scrollIntoView({ block: 'nearest' });
    }
  });
}

function renderProductCombobox(results) {
  if (!productComboboxList) return;

  comboboxResults = results;
  comboboxHighlight = results.length ? 0 : -1;
  productComboboxList.innerHTML = '';

  if (!results.length) {
    const empty = document.createElement('li');
    empty.className = 'dw-pos-combobox-empty';
    empty.textContent = selProduct?.value.trim() ? 'Sin coincidencias' : 'No hay productos activos';
    productComboboxList.appendChild(empty);
    comboboxOpen = true;
    productComboboxList.classList.remove('hidden');
    setComboboxAria(true);
    return;
  }

  const query = selProduct?.value || '';

  results.forEach((item, index) => {
    const option = document.createElement('li');
    option.className = 'dw-pos-combobox-option' + (index === 0 ? ' is-highlighted' : '');
    option.setAttribute('role', 'option');
    option.dataset.comboboxOption = '1';
    option.dataset.productId = String(item.id);

    const name = document.createElement('span');
    name.className = 'dw-pos-combobox-name';
    name.innerHTML = highlightQuery(item.name, query);

    const meta = document.createElement('span');
    meta.className = 'dw-pos-combobox-meta';
    meta.textContent = formatProductMeta(item);

    option.appendChild(name);
    option.appendChild(meta);
    option.addEventListener('mousedown', (e) => {
      e.preventDefault();
      selectProductById(item.id);
    });

    productComboboxList.appendChild(option);
  });

  comboboxOpen = true;
  productComboboxList.classList.remove('hidden');
  setComboboxAria(true);
}

function openProductCombobox(query) {
  renderProductCombobox(filterProducts(query));
}

function clearProductSelection() {
  activeProductId = null;
  setCategoryBadge('');
  inpUnit.value = '';
  inpQty.value = '1';
  inpTotal.value = '';
  if (stockInfo) {
    stockInfo.textContent = '';
    stockInfo.classList.add('invisible');
    stockInfo.classList.remove('text-rose-600');
  }
}

function applyProductMeta(meta) {
  if (!meta) {
    clearProductSelection();
    return;
  }

  activeProductId = meta.id;
  if (selProduct) selProduct.value = meta.name;

  const catLabel = getCategoryLabel(meta.category || '');
  setCategoryBadge(catLabel);
  inpUnit.value = formatCOP(meta.unit);

  const available = meta.stock;
  if (available !== null && stockInfo) {
    stockInfo.textContent = `Stock: ${available}`;
    stockInfo.classList.remove('invisible', 'text-rose-600');
  } else if (stockInfo) {
    stockInfo.textContent = 'Servicio';
    stockInfo.classList.remove('invisible', 'text-rose-600');
  }

  if (available !== null && available <= 0) {
    inpQty.value = '1';
    inpTotal.value = '';
    if (stockInfo) stockInfo.classList.add('text-rose-600');
    showSaleToast('Este producto no tiene stock disponible.');
    return;
  }

  if (!inpQty.value || parseInt(inpQty.value, 10) < 1) {
    inpQty.value = '1';
  }

  recalcLinePreview();
}

function selectProductById(id, options = {}) {
  const meta = productById.get(String(id));
  if (!meta) return;

  closeProductCombobox();
  applyProductMeta(meta);

  if (options.focusQty !== false) {
    requestAnimationFrame(() => {
      inpQty?.focus();
      inpQty?.select();
    });
  }
}

function getSelectedProductMeta(){
  if (activeProductId) {
    return productById.get(String(activeProductId)) || null;
  }

  const raw = (selProduct?.value || '').trim();
  if (!raw) return null;

  const normalized = raw.toLowerCase();
  const match = productIndex.find(item => item.name.toLowerCase() === normalized);
  return match || null;
}

function onProductInput() {
  const query = selProduct?.value || '';
  const selected = activeProductId ? productById.get(String(activeProductId)) : null;

  if (selected && query !== selected.name) {
    activeProductId = null;
    setCategoryBadge('');
    if (stockInfo) stockInfo.classList.add('invisible');
  }

  if (!query.trim()) {
    clearProductSelection();
  }

  openProductCombobox(query);
}

function onProductKeydown(e) {
  if (comboboxOpen && comboboxResults.length) {
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      const next = comboboxHighlight < comboboxResults.length - 1 ? comboboxHighlight + 1 : 0;
      highlightComboboxItem(next);
      return;
    }

    if (e.key === 'ArrowUp') {
      e.preventDefault();
      const prev = comboboxHighlight > 0 ? comboboxHighlight - 1 : comboboxResults.length - 1;
      highlightComboboxItem(prev);
      return;
    }

    if (e.key === 'Enter') {
      e.preventDefault();
      const query = (selProduct?.value || '').trim();
      if (looksLikeBarcode(query) && !getSelectedProductMeta()) {
        lookupBarcode(query);
        return;
      }
      const pick = comboboxHighlight >= 0 ? comboboxResults[comboboxHighlight] : comboboxResults[0];
      if (pick) selectProductById(pick.id);
      return;
    }

    if (e.key === 'Escape') {
      e.preventDefault();
      e.stopPropagation();
      closeProductCombobox();
      return;
    }
  }

  if (e.key === 'Enter') {
    e.preventDefault();
    const query = (selProduct?.value || '').trim();
    if (looksLikeBarcode(query) && !getSelectedProductMeta()) {
      lookupBarcode(query);
      return;
    }
    if (getSelectedProductMeta()) {
      inpQty?.focus();
      inpQty?.select();
    } else {
      openProductCombobox(selProduct?.value || '');
    }
    return;
  }

  if (e.key === 'ArrowDown' && !comboboxOpen) {
    e.preventDefault();
    openProductCombobox(selProduct?.value || '');
  }
}

function initProductCombobox() {
  selProduct?.addEventListener('input', onProductInput);
  selProduct?.addEventListener('keydown', onProductKeydown);
  selProduct?.addEventListener('focus', () => openProductCombobox(selProduct.value || ''));

  document.addEventListener('mousedown', (e) => {
    if (!comboboxOpen) return;
    if (productComboboxRoot?.contains(e.target)) return;
    closeProductCombobox();
  });
}

function getCategoryLabel(code){
  return SECTOR_LABELS[code] || code || '';
}

function getUsedQty(itemId, excludeId = null){
  return saleLines.reduce((sum, line) => {
    if (line.itemId !== itemId) return sum;
    if (excludeId && line.id === excludeId) return sum;
    return sum + line.quantity;
  }, 0);
}

function calcSaleTotal(){
  return saleLines.reduce((sum, line) => sum + (line.quantity * line.unitPrice), 0);
}

function renderSaleItems(){
  if (!saleItemsList) return;
  saleItemsList.innerHTML = '';

  const hasItems = saleLines.length > 0;
  saleCartEmpty?.classList.toggle('hidden', hasItems);
  saleCartTable?.classList.toggle('hidden', !hasItems);

  if (!hasItems) {
    updateItemsCounter();
    updateSaleTotals();
    return;
  }

  saleLines.forEach(line => {
    const row = document.createElement('tr');

    const nameCell = document.createElement('td');
    nameCell.className = 'font-medium text-dw-text';
    nameCell.textContent = line.name;

    const sectorCell = document.createElement('td');
    sectorCell.className = 'text-xs text-dw-muted';
    sectorCell.textContent = getCategoryLabel(line.category);

    const qtyCell = document.createElement('td');
    qtyCell.className = 'text-center';
    qtyCell.textContent = String(line.quantity);

    const unitCell = document.createElement('td');
    unitCell.className = 'text-right text-dw-muted';
    unitCell.textContent = '$' + formatCOP(line.unitPrice);

    const totalCell = document.createElement('td');
    totalCell.className = 'text-right font-semibold text-dw-text';
    totalCell.textContent = '$' + formatCOP(line.quantity * line.unitPrice);

    const actionsCell = document.createElement('td');
    actionsCell.className = 'text-right';
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'text-dw-muted hover:text-rose-600';
    removeBtn.title = 'Quitar';
    removeBtn.innerHTML = '<span class="material-symbols-outlined text-[16px]">delete</span>';
    removeBtn.addEventListener('click', () => removeLine(line.id));
    actionsCell.appendChild(removeBtn);

    row.appendChild(nameCell);
    row.appendChild(sectorCell);
    row.appendChild(qtyCell);
    row.appendChild(unitCell);
    row.appendChild(totalCell);
    row.appendChild(actionsCell);

    saleItemsList.appendChild(row);
  });

  updateItemsCounter();
  updateSaleTotals();
}

function updateItemsCounter(){
  if (!saleItemsCounter) return;
  if (!saleLines.length) {
    saleItemsCounter.textContent = '0 productos';
    return;
  }
  const totalUnits = saleLines.reduce((sum, line) => sum + line.quantity, 0);
  saleItemsCounter.textContent = `${saleLines.length} prod. · ${totalUnits} u.`;
}

function updateSaleTotals(){
  const total = calcSaleTotal();
  if (saleTotal) saleTotal.textContent = total > 0 ? '$' + formatCOP(total) : '$0';
  recalcChange(total);
  if (saveBtn) saveBtn.disabled = !saleLines.length;
}

function resetSaleForm(){
  closeProductCombobox();
  setCustomerBarOpen(false);
  activeProductId = null;
  if (selProduct) selProduct.value = '';
  clearProductSelection();

  saleLines = [];
  saleLineId = 1;
  renderSaleItems();

  clearCustomer();
  inpQty.value = "1";
  if (!IS_ADMIN && inpUnit) {
    inpUnit.readOnly = true;
    inpUnit.classList.add('dw-pos-input--money-readonly');
  }
  inpUnit.value = "";
  inpTotal.value = "";
  setPayMethod('cash');
  inpGiven.value = "";
  inpChange.value = "";
  if (stockInfo) { stockInfo.textContent = ''; stockInfo.classList.add('invisible'); stockInfo.classList.remove('text-rose-600'); }
  if (qtyError) { qtyError.textContent = ''; qtyError.classList.add('hidden'); }
}

function openModal(){
  resetSaleForm();
  closeCustomerModal();
  saleModal.classList.remove('hidden');
  document.body.classList.add('overflow-hidden');
  requestAnimationFrame(() => selProduct?.focus());
}
function closeModal(){
  closeProductCombobox();
  saleModal.classList.add('hidden');
  document.body.classList.remove('overflow-hidden');
}
closeBtn?.addEventListener('click', closeModal);
cancelBtn?.addEventListener('click', closeModal);
saleModal.querySelector('[data-pos-dismiss]')?.addEventListener('click', closeModal);
document.querySelector('.dw-pos-center')?.addEventListener('click', (e) => {
  if (e.target.classList.contains('dw-pos-center')) closeModal();
});
document.querySelector('.dw-pos-modal')?.addEventListener('click', (e) => e.stopPropagation());
window.addEventListener('keydown', e => {
  if (e.key !== 'Escape' || saleModal.classList.contains('hidden')) return;
  if (comboboxOpen) {
    closeProductCombobox();
    return;
  }
  closeModal();
});

function recalcLinePreview(){
  const meta = getSelectedProductMeta();

  if (!meta) {
    inpTotal.value = "";
    if (qtyError) { qtyError.textContent = ''; qtyError.classList.add('hidden'); }
    return;
  }

  const available = meta.stock;
  const alreadyAdded = getUsedQty(meta.id);
  const isService = meta.type === 'service' || available === null;

  if (!isService && available !== null && available <= 0) {
    inpTotal.value = "";
    if (stockInfo) {
      stockInfo.textContent = 'Sin stock disponible.';
      stockInfo.classList.remove('invisible');
      stockInfo.classList.add('text-rose-600');
    }
    return;
  }

  let qty = Math.max(1, parseInt(inpQty.value || "1", 10));
  if (!isService && available !== null) {
    const maxForLine = Math.max(0, available - alreadyAdded);
    if (qty > maxForLine) {
      qty = maxForLine || 1;
      inpQty.value = String(qty);
      showSaleToast(`Solo hay ${maxForLine} unidad${maxForLine === 1 ? '' : 'es'} disponibles después de las líneas agregadas.`);
      if (qtyError) {
        qtyError.textContent = `Solo puedes agregar ${maxForLine} unidad${maxForLine === 1 ? '' : 'es'} más.`;
        qtyError.classList.remove('hidden');
      }
    } else if (qtyError) {
      qtyError.textContent = '';
      qtyError.classList.add('hidden');
    }
  }

  const unit = toInt(inpUnit.value);
  const total = qty * unit;
  inpUnit.value = formatCOP(unit);
  inpTotal.value = formatCOP(total);
}

function recalcChange(totalOverride){
  const total = typeof totalOverride === 'number' ? totalOverride : calcSaleTotal();
  const given = toInt(inpGiven.value);
  const change = Math.max(0, given - total);
  inpChange.value = formatCOP(change);
}

async function searchCustomerByDocument(){
  const doc = (inpCustomerDocument?.value || '').trim();
  if (!doc) {
    showSaleToast('Ingresa una cédula para buscar.');
    return;
  }
  const axiosInstance = window.axios;
  if (!axiosInstance) {
    showSaleToast('No se encontró Axios en la página.');
    return;
  }
  try {
    const { data } = await axiosInstance.get('/api/customers', { params: { q: doc } });
    const match = (data.data || []).find(c => (c.document || '').startsWith(doc));
    if (match) {
      setCustomer(match);
      showSaleToast('Cliente cargado.');
    } else {
      showSaleToast('No se encontró un cliente con esa cédula.');
    }
  } catch (e) {
    console.error(e);
    showSaleToast('No se pudo buscar el cliente.');
  }
}

function openCustomerModal(){
  if (!customerModal) return;
  customerModal.classList.remove('hidden');
  ncDocument.value = inpCustomerDocument?.value || '';
  ncName.value = inpName?.value || '';
  ncEmail.value = inpEmail?.value || '';
  ncPhone.value = inpPhone?.value || '';
}

function closeCustomerModal(){
  if (!customerModal) return;
  customerModal.classList.add('hidden');
}

async function saveCustomerModal(){
  const axiosInstance = window.axios;
  if (!axiosInstance) {
    showSaleToast('No se encontró Axios en la página.');
    return;
  }
  try {
    const payload = {
      document: ncDocument.value || null,
      name: ncName.value || '',
      email: ncEmail.value || null,
      phone: ncPhone.value || null,
    };
    const { data } = await axiosInstance.post('/api/customers', payload);
    if (data?.data) {
      setCustomer(data.data);
      closeCustomerModal();
      showSaleToast('Cliente creado y cargado.');
    }
  } catch (e) {
    console.error(e);
    showSaleToast('No se pudo crear el cliente.');
  }
}

function addLineToSale(){
  const meta = getSelectedProductMeta();
  if (!meta) {
    showSaleToast('Selecciona un producto.');
    return;
  }

  const qty    = Math.max(1, parseInt(inpQty.value || "1", 10));
  const unit   = IS_ADMIN ? toInt(inpUnit.value) : (meta.unit || 0);
  const available = meta.stock;
  const alreadyAdded = getUsedQty(meta.id);
  const isService = meta.type === 'service' || available === null;

  if (!isService && available !== null) {
    if (available <= 0) {
      showSaleToast('Este producto no tiene stock disponible.');
      return;
    }
    const remaining = available - alreadyAdded;
    if (remaining <= 0) {
      showSaleToast('Ya usaste todo el stock disponible en esta venta.');
      return;
    }
    if (qty > remaining) {
      showSaleToast(`Solo puedes agregar ${remaining} unidad${remaining === 1 ? '' : 'es'} más.`);
      return;
    }
  }

  if (!meta.id) {
    showSaleToast('Producto inválido.');
    return;
  }
  if (unit <= 0) {
    showSaleToast('El valor unitario debe ser mayor que cero.');
    return;
  }

  saleLines.push({
    id: saleLineId++,
    itemId: meta.id,
    name: meta.name,
    category: meta.category,
    quantity: qty,
    unitPrice: unit,
    stock: meta.stock,
    type: meta.type,
  });

  renderSaleItems();
  closeProductCombobox();
  activeProductId = null;
  inpQty.value = "1";
  inpUnit.value = "";
  inpTotal.value = "";
  if (selProduct) selProduct.value = "";
  setCategoryBadge('');
  if (stockInfo) { stockInfo.textContent = ''; stockInfo.classList.add('invisible'); stockInfo.classList.remove('text-rose-600'); }
  selProduct?.focus();
  recalcChange();
}

function removeLine(lineId){
  saleLines = saleLines.filter(line => line.id !== lineId);
  renderSaleItems();
}

function updateLine(lineId, changes){
  const idx = saleLines.findIndex(line => line.id === lineId);
  if (idx === -1) return;

  const current = saleLines[idx];
  let quantity = changes.quantity !== undefined ? Math.max(1, changes.quantity) : current.quantity;
  let unitPrice = IS_ADMIN && changes.unitPrice !== undefined ? Math.max(0, changes.unitPrice) : current.unitPrice;

  if (current.stock !== null) {
    const usedElse = getUsedQty(current.itemId, current.id);
    const maxForLine = Math.max(0, current.stock - usedElse);
    if (maxForLine <= 0) {
      showSaleToast('No hay stock disponible para este producto.');
      quantity = 0;
    } else if (quantity > maxForLine) {
      quantity = maxForLine;
      showSaleToast(`Solo hay ${maxForLine} unidad${maxForLine === 1 ? '' : 'es'} disponibles para este producto.`);
    }
  }

  if (quantity < 1) {
    removeLine(lineId);
    return;
  }

  saleLines[idx] = { ...current, quantity, unitPrice };
  renderSaleItems();
}

async function submitSale(event){
  event.preventDefault();

  const axiosInstance = window.axios;
  if (!axiosInstance) {
    showSaleToast('No se encontró Axios en la página.');
    return;
  }

  if (!saleLines.length) {
    showSaleToast('Agrega al menos un producto a la venta.');
    return;
  }

  const payMethod = selPayMethod?.value || 'cash';
  const total = calcSaleTotal();
  const given = payMethod === 'cash' ? toInt(inpGiven.value) : total;
  if (payMethod === 'cash' && given < total) {
    showSaleToast('El pago debe cubrir el total de la venta.');
    return;
  }

  const payload = {
    customer_id: selectedCustomerId || null,
    customer_name: inpName?.value || null,
    customer_email: inpEmail?.value || null,
    customer_phone: inpPhone?.value || null,
    payment_method: payMethod,
    amount_received: given || 0,
    items: saleLines.map(line => ({
      item_id: line.itemId,
      quantity: line.quantity,
      unit_price: line.unitPrice,
    })),
  };

  try {
    saveBtn.disabled = true;
    saveBtn.textContent = 'Guardando...';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrf) {
      axiosInstance.defaults.headers.common['X-CSRF-TOKEN'] = csrf;
    }
    const response = await axiosInstance.post("{{ route('sales.store') }}", payload, { withCredentials: true });

    showSaleToast('Venta registrada. Código: ' + (response.data.sale_code || 'N/A'));
    closeModal();

    setTimeout(() => {
      window.location.reload();
    }, 1000);

  } catch (error) {
    console.error(error);
    let msg = 'No se pudo registrar la venta.';

    if (error.response?.data?.message) {
      msg = error.response.data.message;
    }

    showSaleToast(msg);

  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = 'Registrar venta';
  }
}

function formatOnUnit(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcLinePreview(); }

function formatOnGiven(e){
  e.target.value = formatCOP(toInt(e.target.value));
  recalcChange();
}

function suggestCashReceived() {
  if (selPayMethod?.value !== 'cash') return;
  const total = calcSaleTotal();
  if (total <= 0 || toInt(inpGiven.value) > 0) return;
  inpGiven.value = formatCOP(total);
  recalcChange(total);
}

function handleAddOnEnter(e){
  if (e.key === 'Enter') {
    e.preventDefault();
    addLineToSale();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  saleToast     = document.getElementById('saleToast');
  saleToastText = document.getElementById('saleToastText');

  initPayMethodControls();
  initCustomerBar();
  initPosSectorFilters();
  initProductCombobox();
  renderProducts();
  resetSaleForm();

  inpQty.addEventListener('input', recalcLinePreview);
  inpQty.addEventListener('keydown', handleAddOnEnter);
  if (IS_ADMIN) {
    inpUnit.addEventListener('input', formatOnUnit);
    inpUnit.addEventListener('keydown', handleAddOnEnter);
  }
  inpGiven.addEventListener('input', formatOnGiven);
  inpGiven.addEventListener('focus', suggestCashReceived);
  btnSearchCustomer?.addEventListener('click', searchCustomerByDocument);
  btnNewCustomer?.addEventListener('click', openCustomerModal);
  btnClearCustomer?.addEventListener('click', clearCustomer);
  btnAddProductLine?.addEventListener('click', addLineToSale);
  btnScanProduct?.addEventListener('click', () => {
    if (!window.dwOpenBarcodeScanner) {
      showSaleToast('Escáner no disponible.');
      return;
    }
    window.dwOpenBarcodeScanner({
      parentModal: document.getElementById('saleModal'),
      onDetected: (code) => lookupBarcode(code),
      onError: () => showSaleToast('No se pudo acceder a la cámara.'),
      onScanError: (message) => showSaleToast(message),
    });
  });
  document.getElementById('closeBarcodeQuickModal')?.addEventListener('click', closeBarcodeQuickModal);
  document.getElementById('cancelBarcodeQuickModal')?.addEventListener('click', closeBarcodeQuickModal);
  document.getElementById('saveBarcodeQuickModal')?.addEventListener('click', saveBarcodeQuickModal);
  barcodeQuickModal?.querySelector('[data-bqc-dismiss]')?.addEventListener('click', closeBarcodeQuickModal);
  bqcCost?.addEventListener('input', () => {
    if (!bqcPrice?.value) {
      bqcPrice.value = suggestedQuickPrice(bqcCost.value);
    }
  });
  customerModalClose?.addEventListener('click', closeCustomerModal);
  customerModalCancel?.addEventListener('click', closeCustomerModal);
  customerModalSave?.addEventListener('click', saveCustomerModal);

  if (saveBtn) {
    saveBtn.addEventListener('click', submitSale);
  }

  const handleOpenSale = async () => {
    try {
      const axiosInstance = window.axios;
      if (axiosInstance) {
        const { data } = await axiosInstance.get('/api/items', {
          params: { per_page: 200, active: 1 },
        });
        const grouped = {};
        (data.data || []).forEach((item) => {
          const sector = item.sector || 'otros';
          if (!grouped[sector]) grouped[sector] = [];
          grouped[sector].push({
            id: item.id,
            name: item.name,
            unit: item.sale_price ?? 0,
            stock: item.type === 'product' ? (item.stock ?? null) : null,
            type: item.type,
          });
        });
        productDataset = grouped;
        renderProducts();
      }
    } catch (e) {
      console.warn('No se pudo actualizar el catálogo en vivo', e);
    }
    openModal();
  };

  openSaleButtons.forEach((btn) => {
    btn.addEventListener('click', handleOpenSale);
  });
});
</script>
@endpush
