{{-- resources/views/sales/partials/modal-create.blade.php --}}
{{-- Modal: Registrar venta --}}

<div id="saleModal" class="fixed inset-0 z-[70] hidden">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

  <div class="absolute inset-0 flex items-start justify-center p-4 overflow-y-auto">
    <div class="w-full max-w-4xl overflow-hidden rounded-dw-lg bg-dw-card shadow-dw-neon dw-hairline-neon">

      <div class="subtle-gradient flex items-center justify-between border-b px-4 py-3 dw-hairline">
        <div class="space-y-0.5">
          <h3 class="font-display text-lg font-bold text-dw-text">Registrar venta</h3>
          <p class="text-sm text-dw-muted">Cliente · productos · pago</p>
        </div>
        <button id="closeSaleModal"
                class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft hover:text-dw-text"
                aria-label="Cerrar modal">
          &times;
        </button>
      </div>

      <div class="max-h-[calc(100vh-7rem)] overflow-y-auto">
        <div class="p-4 grid grid-cols-1 lg:grid-cols-3 gap-4">

          <div class="lg:col-span-2 space-y-4">
            <div class="bg-dw-lilac-soft rounded-xl p-4 border border-dw-border">
              <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between mb-2">
              <div>
                <h4 class="font-semibold text-dw-text">Cliente</h4>
                <p class="text-xs text-dw-muted">Opcional. Busca por cédula o registra uno nuevo.</p>
              </div>
              <div id="customerInfoPill" class="text-xs px-3 py-1.5 rounded-full bg-dw-card border border-dw-border text-dw-muted min-w-[160px] text-center">
                Sin cliente obligatorio
              </div>
            </div>
            <input type="hidden" id="c_id">
            <input type="hidden" id="c_name">
            <input type="hidden" id="c_email">
            <input type="hidden" id="c_phone">
              <div class="space-y-2">
                <div>
                  <label class="block text-sm text-dw-muted mb-1" for="c_document">Cédula / ID</label>
                  <input id="c_document" type="text"
                         placeholder="Ej: 123456789"
                         class="dw-input">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full">
                  <button id="searchCustomer"
                          type="button"
                          class="dw-btn-secondary w-full text-sm">
                    Buscar
                  </button>
                  <button id="newCustomer"
                          type="button"
                          class="dw-btn-primary w-full text-sm">
                    Nuevo
                  </button>
                  <button id="clearCustomer"
                          type="button"
                          class="dw-btn-secondary w-full text-sm">
                    Limpiar
                  </button>
                </div>
              </div>
            </div>

            <div class="rounded-xl border border-dw-border bg-dw-card p-4 shadow-sm space-y-3">
              <div class="flex flex-col gap-1">
                <h4 class="font-semibold text-dw-text">Agregar productos</h4>
                <p class="text-xs text-dw-muted">Escribe y selecciona el producto, ajusta cantidad/valor y presiona Enter para agregar.</p>
              </div>

            <div class="flex justify-center">
              <span id="p_category_badge"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-dw-lilac-soft text-dw-text">
                Selecciona un producto
              </span>
            </div>

            <div class="grid grid-cols-12 gap-3 items-end">
              <div class="col-span-6 min-w-0">
                <label class="block text-sm text-dw-muted mb-1" for="p_product">Producto</label>
                <input id="p_product" type="text" list="product_list" placeholder="Escribe para filtrar"
                       class="dw-input">
                <datalist id="product_list"></datalist>
                <p id="stock_info" class="mt-1 text-xs text-dw-muted min-h-[1rem] invisible"></p>
              </div>

              <div class="col-span-2 min-w-0">
                <label class="block text-sm text-dw-muted mb-1" for="p_qty">Cantidad</label>
                <input id="p_qty" type="number" min="1" value="1"
                       class="dw-input">
                <p id="qty_error" class="mt-1 text-xs text-rose-600 hidden"></p>
                <p class="mt-1 text-xs text-transparent select-none">.</p>
              </div>

              <div class="col-span-2 min-w-0">
                <label class="block text-sm text-dw-muted mb-1" for="p_unit">Unidad</label>
                <input id="p_unit" type="text" inputmode="numeric" placeholder="0"
                       class="dw-input">
                <p class="mt-1 text-xs text-transparent select-none">.</p>
              </div>

              <div class="col-span-2 min-w-0">
                <label class="block text-sm text-dw-muted mb-1" for="p_total">Total</label>
                <input id="p_total" type="text" inputmode="numeric" placeholder="0"
                       class="dw-input bg-dw-lilac-soft" readonly>
                <p class="mt-1 text-xs text-transparent select-none">.</p>
              </div>
            </div>
          </div>

            <div class="rounded-xl border border-dw-border bg-dw-card p-4 shadow-sm">
              <div class="flex items-center justify-between mb-2">
                <div>
                  <h4 class="font-semibold text-dw-text">Resumen de productos</h4>
                  <p class="text-xs text-dw-muted">Edita cantidades y valores antes de registrar.</p>
                </div>
                <span id="saleItemsCounter" class="text-xs px-3 py-1 rounded-full bg-dw-lilac-soft text-dw-text">Sin productos</span>
              </div>
            <div class="hidden md:grid md:grid-cols-[4fr_2fr_1.5fr_1.5fr_1.5fr_1.5fr] gap-2 text-[11px] text-dw-muted pb-2 border-b border-dw-border">
              <div>Producto</div>
              <div>Categoría</div>
              <div class="text-center">Cantidad</div>
              <div class="text-center">Unidad</div>
              <div class="text-right">Total</div>
              <div class="text-right">Acciones</div>
            </div>
            <div id="saleItemsList" class="space-y-2 max-h-72 overflow-y-auto pr-1 pt-2">
              <p class="text-sm text-dw-muted">Aún no has agregado productos.</p>
            </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <div class="bg-dw-lilac-soft rounded-dw border-hairline border-dw-border p-4 space-y-3 shadow-dw-neon-sm lg:sticky lg:top-6">
              <div class="flex items-center justify-between">
                <h4 class="font-semibold text-dw-text">Pago</h4>
                <span class="dw-badge-primary text-xs">Paso 3</span>
              </div>

            <div class="rounded-dw border-hairline border-dw-border bg-dw-card p-3">
              <label class="dw-label mb-1" for="sale_total">Total venta</label>
              <input id="sale_total" type="text" inputmode="numeric" placeholder="0"
                     class="dw-input bg-dw-lilac-soft font-semibold text-lg" readonly>
            </div>

            <div class="space-y-3">
              <div>
                <label class="dw-label mb-1" for="pay_method">Metodo de pago</label>
                <select id="pay_method" class="dw-select">
                  <option value="cash">Efectivo</option>
                  <option value="transfer">Transferencia</option>
                  <option value="card">Tarjeta</option>
                  <option value="mixed">Mixto</option>
                  <option value="other">Otro</option>
                </select>
              </div>

              <div>
                <label class="dw-label mb-1" for="pay_given">Paga con (COP)</label>
                <input id="pay_given" type="text" inputmode="numeric" placeholder="0" class="dw-input">
              </div>

              <div>
                <label class="dw-label mb-1" for="pay_change">Devuelta (COP)</label>
                <input id="pay_change" type="text" inputmode="numeric" placeholder="0"
                       class="dw-input bg-dw-lilac-soft" readonly>
              </div>
            </div>

              <div class="pt-2 flex flex-col gap-2">
                <button id="saveSale" type="button" class="dw-btn-primary w-full">Registrar venta</button>
                <button id="cancelSale" type="button" class="dw-btn-secondary w-full">Cancelar</button>
                <p class="text-xs text-dw-muted text-center">Puedes registrar sin cliente si lo prefieres.</p>
              </div>
            </div>
          </div>

        </div>
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
const DEFAULT_BADGE = 'Selecciona un producto';
let productDataset = JSON.parse(JSON.stringify(DATASET));
const IS_ADMIN = @json(auth()->user()?->isAdmin() ?? false);

const SECTOR_LABELS = {
  impresion: 'Impresión',
  papeleria: 'Papelería',
  diseno: 'Diseño',
};

const saleModal = document.getElementById('saleModal');
const openBtn   = document.getElementById('openSaleModal');
const closeBtn  = document.getElementById('closeSaleModal');
const cancelBtn = document.getElementById('cancelSale');
const saveBtn   = document.getElementById('saveSale');

const selProduct = document.getElementById('p_product');
const productList = document.getElementById('product_list');
const badgeCat   = document.getElementById('p_category_badge');
const inpQty     = document.getElementById('p_qty');
const inpUnit    = document.getElementById('p_unit');
const inpTotal   = document.getElementById('p_total');
const selPayMethod = document.getElementById('pay_method');
const inpGiven   = document.getElementById('pay_given');
const inpChange  = document.getElementById('pay_change');
const saleTotal  = document.getElementById('sale_total');
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
const btnClearCustomer = document.getElementById('clearCustomer');
const customerModal = document.getElementById('customerCreateModal');
const customerModalClose = document.getElementById('closeCustomerModal');
const customerModalCancel = document.getElementById('cancelCustomerModal');
const customerModalSave = document.getElementById('saveCustomerModal');
const ncDocument = document.getElementById('nc_document');
const ncName = document.getElementById('nc_name');
const ncEmail = document.getElementById('nc_email');
const ncPhone = document.getElementById('nc_phone');

let saleToast = null;
let saleToastText = null;
let saleToastTimer = null;

let saleLines = [];
let saleLineId = 1;
let selectedCustomerId = null;
const customerInfoPill = document.getElementById('customerInfoPill');
let productIndex = [];
let productById = new Map();

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

function updateCustomerPill(){
  if (!customerInfoPill) return;
  if (selectedCustomerId) {
    const name = (inpName?.value || '').trim() || 'Cliente cargado';
    const doc = (inpCustomerDocument?.value || '').trim();
    customerInfoPill.textContent = doc ? `${name} · ${doc}` : name;
    customerInfoPill.classList.remove('bg-dw-card', 'border', 'border-dw-border', 'text-dw-muted');
    customerInfoPill.classList.add('dw-badge-primary');
  } else {
    customerInfoPill.textContent = 'Sin cliente obligatorio';
    customerInfoPill.classList.remove('dw-badge-primary');
    customerInfoPill.classList.add('bg-dw-card', 'border', 'border-dw-border', 'text-dw-muted');
  }
}

function setCustomer(data){
  selectedCustomerId = data?.id ?? null;
  if (inpCustomerId) inpCustomerId.value = selectedCustomerId || '';
  if (inpCustomerDocument) inpCustomerDocument.value = data?.document || '';
  if (inpName) inpName.value = data?.name || '';
  if (inpEmail) inpEmail.value = data?.email || '';
  if (inpPhone) inpPhone.value = data?.phone || '';
  updateCustomerPill();
}

function clearCustomer(){
  setCustomer({ id:null, document:'', name:'', email:'', phone:'' });
  updateCustomerPill();
}

function renderProducts(){
  if (!productList) return;
  productList.innerHTML = '';
  productIndex = [];
  productById = new Map();

  Object.keys(productDataset).forEach(cat => {
    productDataset[cat].forEach(p => {
      const label = `${p.name}`;
      const item = {
        id: p.id,
        name: p.name,
        category: cat,
        unit: p.unit,
        stock: p.stock ?? null,
        type: p.type || '',
        label,
      };
      productIndex.push(item);
      productById.set(String(p.id), item);

      const opt = document.createElement('option');
      opt.value = label;
      productList.appendChild(opt);
    });
  });
}

function getSelectedProductMeta(){
  const raw = (selProduct?.value || '').trim();
  if (!raw) return null;

  let id = null;
  const normalized = raw.toLowerCase();
  const match = productIndex.find(item =>
    item.name.toLowerCase() === normalized || item.label.toLowerCase() === normalized
  );
  if (match) id = String(match.id);

  if (!id) return null;
  return productById.get(String(id)) || null;
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

  if (!saleLines.length) {
    const empty = document.createElement('p');
    empty.className = 'text-sm text-dw-muted';
    empty.textContent = 'Aún no has agregado productos.';
    saleItemsList.appendChild(empty);
  } else {
    saleLines.forEach(line => {
      const row = document.createElement('div');
      row.className = 'rounded-dw border-hairline border-dw-border p-3 grid grid-cols-1 md:grid-cols-[4fr_2fr_1.5fr_1.5fr_1.5fr_1.5fr] gap-2 items-center text-xs text-dw-text';

      const nameWrap = document.createElement('div');
      nameWrap.className = '';
      const nameEl = document.createElement('div');
      nameEl.className = 'text-dw-text';
      nameEl.textContent = line.name;
      nameWrap.appendChild(nameEl);

      const categoryWrap = document.createElement('div');
      categoryWrap.className = '';
      const categoryText = document.createElement('div');
      categoryText.className = 'text-dw-text';
      categoryText.textContent = getCategoryLabel(line.category);
      categoryWrap.appendChild(categoryText);

      const qtyWrap = document.createElement('div');
      qtyWrap.className = 'text-center';
      const qtyValue = document.createElement('div');
      qtyValue.className = 'w-full rounded-lg bg-dw-lilac-soft text-dw-text px-2 py-2 text-xs text-center';
      qtyValue.textContent = String(line.quantity);
      qtyWrap.appendChild(qtyValue);

      const unitWrap = document.createElement('div');
      unitWrap.className = 'text-center';
      const unitValue = document.createElement('div');
      unitValue.className = 'w-full rounded-lg bg-dw-lilac-soft text-dw-text px-2 py-2 text-xs text-center';
      unitValue.textContent = formatCOP(line.unitPrice);
      unitWrap.appendChild(unitValue);

      const totalWrap = document.createElement('div');
      totalWrap.className = 'text-right';
      const totalValue = document.createElement('div');
      totalValue.className = 'text-dw-text';
      totalValue.textContent = '$' + formatCOP(line.quantity * line.unitPrice);
      totalWrap.appendChild(totalValue);

      const actionsWrap = document.createElement('div');
      actionsWrap.className = 'text-right';
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'text-dw-text hover:underline';
      removeBtn.textContent = 'Quitar';
      removeBtn.addEventListener('click', () => removeLine(line.id));
      actionsWrap.appendChild(removeBtn);

      row.appendChild(nameWrap);
      row.appendChild(categoryWrap);
      row.appendChild(qtyWrap);
      row.appendChild(unitWrap);
      row.appendChild(totalWrap);
      row.appendChild(actionsWrap);

      saleItemsList.appendChild(row);
    });
  }

  updateItemsCounter();
  updateSaleTotals();
}

function updateItemsCounter(){
  if (!saleItemsCounter) return;
  if (!saleLines.length) {
    saleItemsCounter.textContent = 'Sin productos';
    return;
  }
  const totalUnits = saleLines.reduce((sum, line) => sum + line.quantity, 0);
  saleItemsCounter.textContent = `${saleLines.length} producto${saleLines.length === 1 ? '' : 's'} (${totalUnits} unidades)`;
}

function updateSaleTotals(){
  const total = calcSaleTotal();
  if (saleTotal) saleTotal.value = formatCOP(total);
  recalcChange(total);
  if (saveBtn) saveBtn.disabled = !saleLines.length;
}

function resetSaleForm(){
  if (selProduct) selProduct.value = '';
  if (badgeCat) badgeCat.textContent = DEFAULT_BADGE;

  saleLines = [];
  saleLineId = 1;
  renderSaleItems();

  clearCustomer();
  inpQty.value = "1";
  if (!IS_ADMIN && inpUnit) {
    inpUnit.readOnly = true;
    inpUnit.classList.add('bg-dw-lilac-soft', 'text-dw-muted');
  }
  inpUnit.value = "";
  inpTotal.value = "";
  if (selPayMethod) selPayMethod.value = 'cash';
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
}
function closeModal(){ saleModal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
closeBtn?.addEventListener('click', closeModal);
cancelBtn?.addEventListener('click', closeModal);
saleModal.addEventListener('click', e => { if(e.target === saleModal) closeModal(); });
window.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });

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
  inpQty.value = "1";
  inpUnit.value = "";
  inpTotal.value = "";
  if (selProduct) selProduct.value = "";
  if (badgeCat) badgeCat.textContent = DEFAULT_BADGE;
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

  const given  = toInt(inpGiven.value);
  const total = calcSaleTotal();
  if (given < total) {
    showSaleToast('El pago debe cubrir el total de la venta.');
    return;
  }

  const payload = {
    customer_id: selectedCustomerId || null,
    customer_name: inpName?.value || null,
    customer_email: inpEmail?.value || null,
    customer_phone: inpPhone?.value || null,
    payment_method: selPayMethod?.value || 'cash',
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

function onProductChange(){
  const meta = getSelectedProductMeta();

  if(!meta){
    if (badgeCat) badgeCat.textContent = DEFAULT_BADGE;
    inpUnit.value = "";
    inpQty.value = "1";
    inpTotal.value = "";
    if (stockInfo) stockInfo.classList.add('invisible');
    return;
  }

  const catCode  = meta.category || "";
  const unit     = meta.unit;
  const available = meta.stock;
  const catLabel = getCategoryLabel(catCode);

  if (badgeCat) badgeCat.textContent = catLabel;
  inpUnit.value = formatCOP(unit);

  if (available !== null && stockInfo) {
    stockInfo.textContent = `Stock disponible: ${available} unidad${available === 1 ? '' : 'es'}`;
    stockInfo.classList.remove('invisible');
    stockInfo.classList.remove('text-rose-600');
  } else if (stockInfo) {
    stockInfo.textContent = 'Servicio (sin control de stock)';
    stockInfo.classList.remove('invisible');
    stockInfo.classList.remove('text-rose-600');
  }

  if (available !== null && available <= 0) {
    inpQty.value = "1";
    inpTotal.value = "";
    showSaleToast('Este producto no tiene stock disponible.');
    if (stockInfo) stockInfo.classList.add('text-rose-600');
    return;
  }

  if (!inpQty.value || parseInt(inpQty.value, 10) < 1) {
    const defaultQty = available !== null ? Math.min(1, available) : 1;
    inpQty.value = defaultQty > 0 ? defaultQty : "1";
  }

  recalcLinePreview();
  inpQty?.focus();
  inpQty?.select();
}

function formatOnUnit(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcLinePreview(); }
function formatOnGiven(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcChange(); }
function handleAddOnEnter(e){
  if (e.key === 'Enter') {
    e.preventDefault();
    addLineToSale();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  saleToast     = document.getElementById('saleToast');
  saleToastText = document.getElementById('saleToastText');

  renderProducts();
  resetSaleForm();

  selProduct.addEventListener('change', onProductChange);
  selProduct.addEventListener('input', onProductChange);
  selProduct.addEventListener('keydown', handleAddOnEnter);
  inpQty.addEventListener('input', recalcLinePreview);
  inpQty.addEventListener('keydown', handleAddOnEnter);
  if (IS_ADMIN) {
    inpUnit.addEventListener('input', formatOnUnit);
    inpUnit.addEventListener('keydown', handleAddOnEnter);
  }
  inpGiven.addEventListener('input', formatOnGiven);
  btnSearchCustomer?.addEventListener('click', searchCustomerByDocument);
  btnNewCustomer?.addEventListener('click', openCustomerModal);
  btnClearCustomer?.addEventListener('click', clearCustomer);
  customerModalClose?.addEventListener('click', closeCustomerModal);
  customerModalCancel?.addEventListener('click', closeCustomerModal);
  customerModalSave?.addEventListener('click', saveCustomerModal);

  if (saveBtn) {
    saveBtn.addEventListener('click', submitSale);
  }

  openBtn?.addEventListener('click', async () => {
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
  });
});
</script>
@endpush
