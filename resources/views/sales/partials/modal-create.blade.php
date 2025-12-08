{{-- resources/views/sales/partials/modal-create.blade.php --}}
{{-- Modal: Registrar venta --}}

<div id="saleModal" class="fixed inset-0 z-[70] hidden">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden">

      <div class="flex items-center justify-between px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-white">
        <div class="space-y-1">
          <h3 class="text-xl font-bold text-slate-800">Registrar venta</h3>
          <p class="text-sm text-slate-500">Construye la venta en tres pasos: cliente, productos y pago.</p>
        </div>
        <button id="closeSaleModal"
                class="h-10 w-10 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 hover:text-slate-800 flex items-center justify-center"
                aria-label="Cerrar modal">
          ✕
        </button>
      </div>

      <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
          <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between mb-3">
              <div>
                <h4 class="font-semibold text-slate-800">Cliente</h4>
                <p class="text-xs text-slate-500">Opcional. Busca por cédula o registra uno nuevo.</p>
              </div>
              <div id="customerInfoPill" class="text-xs px-3 py-2 rounded-full bg-white border text-slate-600 min-w-[160px] text-center">
                Sin cliente obligatorio
              </div>
            </div>
            <input type="hidden" id="c_id">
            <input type="hidden" id="c_name">
            <input type="hidden" id="c_email">
            <input type="hidden" id="c_phone">
            <div class="space-y-2 mb-3">
              <div>
                <label class="block text-sm text-slate-600 mb-1" for="c_document">Cédula / ID</label>
                <input id="c_document" type="text"
                       placeholder="Ej: 123456789"
                       class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 w-full">
                <button id="searchCustomer"
                        type="button"
                        class="w-full rounded-lg border px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-white shadow-sm">
                  Buscar
                </button>
                <button id="newCustomer"
                        type="button"
                        class="w-full rounded-lg bg-indigo-600 text-white px-3 py-2 text-sm font-semibold shadow hover:bg-indigo-700">
                  Nuevo
                </button>
                <button id="clearCustomer"
                        type="button"
                        class="w-full rounded-lg border px-3 py-2 text-sm text-slate-600 hover:bg-white">
                  Limpiar
                </button>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border p-4 shadow-sm space-y-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h4 class="font-semibold text-slate-800">Agregar productos</h4>
                <p class="text-xs text-slate-500">Selecciona, ajusta cantidad/valor y agrega a la venta.</p>
              </div>
              <button id="addSaleItem"
                      type="button"
                      class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 text-white px-4 py-2 text-sm font-semibold shadow hover:bg-indigo-700">
                <span class="material-symbols-outlined text-base">add</span>
                Agregar línea
              </button>
            </div>

            <div class="flex justify-center">
              <span id="p_category_badge"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                Selecciona un producto
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_product">Producto</label>
                  <select id="p_product"
                          class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                  </select>
                  <p id="stock_info" class="mt-1 text-xs text-slate-500 hidden"></p>
                </div>

                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_qty">Cantidad</label>
                  <input id="p_qty" type="number" min="1" value="1"
                         class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                  <p id="qty_error" class="mt-1 text-xs text-rose-600 hidden"></p>
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_unit">Valor unidad (COP)</label>
                  <input id="p_unit" type="text" inputmode="numeric" placeholder="0"
                         class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                </div>

                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_total">Total linea (COP)</label>
                  <input id="p_total" type="text" inputmode="numeric" placeholder="0"
                         class="w-full rounded-lg border-slate-300 bg-slate-100 text-slate-700" readonly>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <div>
                <h4 class="font-semibold text-slate-800">Resumen de productos</h4>
                <p class="text-xs text-slate-500">Edita cantidades y valores antes de registrar.</p>
              </div>
              <span id="saleItemsCounter" class="text-xs px-3 py-1 rounded-full bg-slate-100 text-slate-700">Sin productos</span>
            </div>
            <div id="saleItemsList" class="space-y-2">
              <p class="text-sm text-slate-500">Aún no has agregado productos.</p>
            </div>
          </div>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-4 space-y-4 shadow-sm">
            <div class="flex items-center justify-between">
              <h4 class="font-semibold text-indigo-900">Pago</h4>
              <span class="text-xs text-indigo-700 bg-white px-3 py-1 rounded-full border border-indigo-100">Paso 3</span>
            </div>

            <div class="rounded-xl bg-white border border-indigo-100 p-3">
              <label class="block text-xs text-indigo-900 mb-1" for="sale_total">Total venta</label>
              <input id="sale_total" type="text" inputmode="numeric" placeholder="0"
                     class="w-full rounded-lg border-indigo-200 bg-slate-100 text-slate-800 font-semibold" readonly>
            </div>

            <div class="space-y-3">
              <div>
                <label class="block text-sm text-indigo-900 mb-1" for="pay_given">Paga con (COP)</label>
                <input id="pay_given" type="text" inputmode="numeric" placeholder="0"
                       class="w-full rounded-lg border-indigo-200 focus:border-indigo-500 focus:ring-indigo-500 bg-white">
              </div>

              <div>
                <label class="block text-sm text-indigo-900 mb-1" for="pay_change">Devuelta (COP)</label>
                <input id="pay_change" type="text" inputmode="numeric" placeholder="0"
                       class="w-full rounded-lg border-indigo-200 bg-slate-100 text-slate-700" readonly>
              </div>
            </div>

            <div class="pt-3 flex flex-col gap-2">
              <button id="saveSale"
                      class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-3 shadow hover:bg-indigo-700">
                Registrar venta
              </button>
              <button id="cancelSale"
                      class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-3 border hover:bg-slate-50">
                Cancelar
              </button>
              <p class="text-xs text-slate-500 text-center">Puedes registrar sin cliente si lo prefieres.</p>
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
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800">Registrar cliente</h3>
        <button id="closeCustomerModal" class="text-slate-500 hover:text-slate-700">X</button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="md:col-span-2">
          <label class="block text-sm text-slate-600 mb-1" for="nc_document">Cédula / ID</label>
          <input id="nc_document" type="text"
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-slate-600 mb-1" for="nc_name">Nombre</label>
          <input id="nc_name" type="text"
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-slate-600 mb-1" for="nc_email">Correo</label>
          <input id="nc_email" type="email"
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-slate-600 mb-1" for="nc_phone">Teléfono</label>
          <input id="nc_phone" type="text"
                 class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
      </div>
      <div class="pt-3 flex gap-3 justify-end">
        <button id="cancelCustomerModal"
                class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-2 border hover:bg-slate-50">
          Cancelar
        </button>
        <button id="saveCustomerModal"
                class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-700">
          Guardar cliente
        </button>
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

const SECTOR_LABELS = {
  impresion: 'Impresion',
  papeleria: 'Papeleria',
  diseno: 'Diseno',
};

const saleModal = document.getElementById('saleModal');
const openBtn   = document.getElementById('openSaleModal');
const closeBtn  = document.getElementById('closeSaleModal');
const cancelBtn = document.getElementById('cancelSale');
const saveBtn   = document.getElementById('saveSale');

const selProduct = document.getElementById('p_product');
const badgeCat   = document.getElementById('p_category_badge');
const inpQty     = document.getElementById('p_qty');
const inpUnit    = document.getElementById('p_unit');
const inpTotal   = document.getElementById('p_total');
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
const addItemBtn = document.getElementById('addSaleItem');
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
    customerInfoPill.classList.remove('bg-white', 'border', 'text-slate-600');
    customerInfoPill.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-100');
  } else {
    customerInfoPill.textContent = 'Sin cliente obligatorio';
    customerInfoPill.classList.remove('bg-emerald-50', 'text-emerald-700', 'border-emerald-100');
    customerInfoPill.classList.add('bg-white', 'border', 'text-slate-600');
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
  selProduct.innerHTML = "";

  const placeholder = document.createElement('option');
  placeholder.value = "";
  placeholder.textContent = 'Selecciona un producto';
  placeholder.disabled = true;
  placeholder.selected = true;
  placeholder.hidden = true;
  selProduct.appendChild(placeholder);

  Object.keys(productDataset).forEach(cat => {
    productDataset[cat].forEach(p => {
      const opt = document.createElement('option');
      opt.value = String(p.id);
      opt.textContent = p.name;
      opt.dataset.category = cat;
      opt.dataset.unit = String(p.unit);
      opt.dataset.type = p.type || '';
      if (p.stock !== undefined && p.stock !== null) {
        opt.dataset.stock = String(p.stock);
      }
      selProduct.appendChild(opt);
    });
  });
}

function getSelectedOption(){
  const opt = selProduct.options[selProduct.selectedIndex];
  return opt && opt.value ? opt : null;
}

function getAvailableStock(opt){
  if (!opt) return null;
  const raw = opt.dataset.stock;
  if (raw === undefined || raw === null || raw === '') return null;
  const parsed = parseInt(raw, 10);
  return Number.isNaN(parsed) ? null : parsed;
}

function getSelectedProductMeta(){
  const opt = getSelectedOption();
  if (!opt) return null;
  return {
    id: parseInt(opt.value || "0", 10),
    name: opt.textContent || 'Producto',
    category: opt.dataset.category || '',
    unit: parseInt(opt.dataset.unit || "0", 10),
    stock: getAvailableStock(opt),
    type: opt.dataset.type || '',
  };
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
    empty.className = 'text-sm text-slate-500';
    empty.textContent = 'Aun no has agregado productos.';
    saleItemsList.appendChild(empty);
  } else {
    saleLines.forEach(line => {
      const row = document.createElement('div');
      row.className = 'rounded-lg border border-slate-200 p-3 flex flex-col gap-3 md:flex-row md:items-center md:gap-4';

      const info = document.createElement('div');
      info.className = 'flex-1';

      const title = document.createElement('div');
      title.className = 'flex items-center gap-2 justify-between md:justify-start';
      const nameEl = document.createElement('span');
      nameEl.className = 'font-semibold text-slate-800';
      nameEl.textContent = line.name;
      const badge = document.createElement('span');
      badge.className = 'px-2 py-1 rounded-full text-xs bg-slate-100 text-slate-700';
      badge.textContent = getCategoryLabel(line.category);
      title.appendChild(nameEl);
      title.appendChild(badge);

      const stockText = document.createElement('div');
      stockText.className = 'text-xs text-slate-500 mt-1';
      if (line.stock === null) {
        stockText.textContent = 'Servicio sin control de stock';
      } else {
        stockText.textContent = `Stock disponible: ${line.stock} unidad${line.stock === 1 ? '' : 'es'}`;
      }

      info.appendChild(title);
      info.appendChild(stockText);

      const qtyWrap = document.createElement('div');
      qtyWrap.className = 'flex items-center gap-2';
      const qtyLabel = document.createElement('span');
      qtyLabel.className = 'text-xs text-slate-500';
      qtyLabel.textContent = 'Cantidad';
      const qtyInput = document.createElement('input');
      qtyInput.type = 'number';
      qtyInput.min = '1';
      qtyInput.value = String(line.quantity);
      qtyInput.className = 'w-24 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500';
      qtyInput.addEventListener('input', () => {
        const nextQty = Math.max(1, parseInt(qtyInput.value || '1', 10));
        updateLine(line.id, { quantity: nextQty });
      });
      qtyWrap.appendChild(qtyLabel);
      qtyWrap.appendChild(qtyInput);

      const unitWrap = document.createElement('div');
      unitWrap.className = 'flex items-center gap-2';
      const unitLabel = document.createElement('span');
      unitLabel.className = 'text-xs text-slate-500';
      unitLabel.textContent = 'Valor';
      const unitInput = document.createElement('input');
      unitInput.type = 'text';
      unitInput.inputMode = 'numeric';
      unitInput.value = formatCOP(line.unitPrice);
      unitInput.className = 'w-28 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500';
      unitInput.addEventListener('input', () => {
        const nextUnit = Math.max(0, toInt(unitInput.value));
        updateLine(line.id, { unitPrice: nextUnit });
      });
      unitWrap.appendChild(unitLabel);
      unitWrap.appendChild(unitInput);

      const totalWrap = document.createElement('div');
      totalWrap.className = 'text-right';
      const totalLabel = document.createElement('div');
      totalLabel.className = 'text-xs text-slate-500';
      totalLabel.textContent = 'Total';
      const totalValue = document.createElement('div');
      totalValue.className = 'font-semibold text-slate-900';
      totalValue.textContent = '$' + formatCOP(line.quantity * line.unitPrice);
      totalWrap.appendChild(totalLabel);
      totalWrap.appendChild(totalValue);

      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'text-rose-600 text-sm hover:underline';
      removeBtn.textContent = 'Quitar';
      removeBtn.addEventListener('click', () => removeLine(line.id));

      const controls = document.createElement('div');
      controls.className = 'flex flex-wrap items-center gap-4';
      controls.appendChild(qtyWrap);
      controls.appendChild(unitWrap);
      controls.appendChild(totalWrap);
      controls.appendChild(removeBtn);

      row.appendChild(info);
      row.appendChild(controls);

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
  selProduct.selectedIndex = 0;
  if (badgeCat) badgeCat.textContent = DEFAULT_BADGE;

  saleLines = [];
  saleLineId = 1;
  renderSaleItems();

  clearCustomer();
  inpQty.value = "1";
  inpUnit.value = "";
  inpTotal.value = "";
  inpGiven.value = "";
  inpChange.value = "";
  if (stockInfo) { stockInfo.textContent = ''; stockInfo.classList.add('hidden'); stockInfo.classList.remove('text-rose-600'); }
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
      stockInfo.classList.remove('hidden');
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
      showSaleToast(`Solo hay ${maxForLine} unidad${maxForLine === 1 ? '' : 'es'} disponibles despues de las lineas agregadas.`);
      if (qtyError) {
        qtyError.textContent = `Solo puedes agregar ${maxForLine} unidad${maxForLine === 1 ? '' : 'es'} mas.`;
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
  const unit   = toInt(inpUnit.value);
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
      showSaleToast(`Solo puedes agregar ${remaining} unidad${remaining === 1 ? '' : 'es'} mas.`);
      return;
    }
  }

  if (!meta.id) {
    showSaleToast('Producto invalido.');
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
  inpTotal.value = formatCOP(unit);
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
  let unitPrice = changes.unitPrice !== undefined ? Math.max(0, changes.unitPrice) : current.unitPrice;

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
    showSaleToast('No se encontro Axios en la pagina.');
    return;
  }

  if (!saleLines.length) {
    showSaleToast('Agrega al menos un producto a la venta.');
    return;
  }

  const given  = toInt(inpGiven.value);

  const payload = {
    customer_id: selectedCustomerId || null,
    customer_name: inpName?.value || null,
    customer_email: inpEmail?.value || null,
    customer_phone: inpPhone?.value || null,
    payment_method: 'cash',
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

    showSaleToast('Venta registrada. Codigo: ' + (response.data.sale_code || 'N/A'));
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
    if (stockInfo) stockInfo.classList.add('hidden');
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
    stockInfo.classList.remove('hidden');
    stockInfo.classList.remove('text-rose-600');
  } else if (stockInfo) {
    stockInfo.textContent = 'Servicio (sin control de stock)';
    stockInfo.classList.remove('hidden');
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
}

function formatOnUnit(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcLinePreview(); }
function formatOnGiven(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcChange(); }

document.addEventListener('DOMContentLoaded', () => {
  saleToast     = document.getElementById('saleToast');
  saleToastText = document.getElementById('saleToastText');

  renderProducts();
  resetSaleForm();

  selProduct.addEventListener('change', onProductChange);
  inpQty.addEventListener('input', recalcLinePreview);
  inpUnit.addEventListener('input', formatOnUnit);
  inpGiven.addEventListener('input', formatOnGiven);
  addItemBtn?.addEventListener('click', addLineToSale);
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
      console.warn('No se pudo actualizar el catalogo en vivo', e);
    }
    openModal();
  });
});
</script>
@endpush
