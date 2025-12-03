{{-- resources/views/sales/partials/modal-create.blade.php --}}
{{-- Modal: Registrar venta --}}

<div id="saleModal" class="fixed inset-0 z-[70] hidden">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden">

      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h3 class="text-xl font-bold text-slate-800">Registrar venta</h3>
        <button id="closeSaleModal"
                class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700"
                aria-label="Cerrar modal">
          ✕
        </button>
      </div>

      <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">
          <div class="bg-slate-50 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
              <h4 class="font-semibold text-slate-700">Cliente (opcional)</h4>
              <span class="text-xs text-slate-500">No obligatorio</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="block text-sm text-slate-600 mb-1" for="c_name">Nombre</label>
                <input id="c_name" type="text"
                       class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
              </div>
              <div>
                <label class="block text-sm text-slate-600 mb-1" for="c_email">Correo</label>
                <input id="c_email" type="email"
                       class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
              </div>
              <div>
                <label class="block text-sm text-slate-600 mb-1" for="c_phone">Teléfono</label>
                <input id="c_phone" type="tel"
                       class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h4 class="font-semibold text-slate-700">Producto</h4>
            </div>

            <div class="mt-2 flex justify-center">
              <span id="p_category_badge"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-slate-100 text-slate-700">
                Selecciona un producto
              </span>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_product">Producto</label>
                  <select id="p_product"
                          class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                  </select>
                  <p id="stock_info" class="mt-1 text-xs text-slate-500 hidden"></p>
                </div>

                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_qty">Cantidad</label>
                  <input id="p_qty" type="number" min="1" value="1"
                         class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                  <p id="qty_error" class="mt-1 text-xs text-rose-600 hidden"></p>
                </div>
              </div>

              <div class="space-y-4">
                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_unit">Valor unidad (COP)</label>
                  <input id="p_unit" type="text" inputmode="numeric" placeholder="0"
                         class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_total">Total (COP)</label>
                  <input id="p_total" type="text" inputmode="numeric" placeholder="0"
                         class="w-full rounded-lg border-slate-300 bg-slate-100 text-slate-700" readonly>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-1">
          <div class="bg-indigo-50 rounded-xl border border-indigo-100 p-4">
            <h4 class="font-semibold text-indigo-800 mb-3">Pago</h4>

            <div class="space-y-4">
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

              <div class="pt-3 flex gap-3">
                <button id="saveSale"
                        class="flex-1 rounded-xl bg-indigo-600 text-white font-semibold px-4 py-3 shadow hover:bg-indigo-700">
                  Registrar venta
                </button>
                <button id="cancelSale"
                        class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-3 border hover:bg-slate-50">
                  Cancelar
                </button>
              </div>
              <p class="text-xs text-slate-500">La venta se registrará en el sistema.</p>
            </div>
          </div>
        </div>

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
const badgeCat   = document.getElementById('p_category_badge');
const inpQty     = document.getElementById('p_qty');
const inpUnit    = document.getElementById('p_unit');
const inpTotal   = document.getElementById('p_total');
const inpGiven   = document.getElementById('pay_given');
const inpChange  = document.getElementById('pay_change');
const inpName    = document.getElementById('c_name');
const inpEmail   = document.getElementById('c_email');
const inpPhone   = document.getElementById('c_phone');
const stockInfo  = document.getElementById('stock_info');
const qtyError   = document.getElementById('qty_error');

let saleToast = null;
let saleToastText = null;
let saleToastTimer = null;

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
    category: opt.dataset.category || '',
    unit: parseInt(opt.dataset.unit || "0", 10),
    stock: getAvailableStock(opt),
    type: opt.dataset.type || '',
  };
}

function resetSaleForm(){
  selProduct.selectedIndex = 0;
  if (badgeCat) badgeCat.textContent = DEFAULT_BADGE;

  inpQty.value = "";
  inpUnit.value = "";
  inpTotal.value = "";
  inpGiven.value = "";
  inpChange.value = "";

  if (inpName) inpName.value = "";
  if (inpEmail) inpEmail.value = "";
  if (inpPhone) inpPhone.value = "";
  if (stockInfo) { stockInfo.textContent = ''; stockInfo.classList.add('hidden'); stockInfo.classList.remove('text-rose-600'); }
  if (qtyError) { qtyError.textContent = ''; qtyError.classList.add('hidden'); }
}

function openModal(){
  resetSaleForm();
  saleModal.classList.remove('hidden');
  document.body.classList.add('overflow-hidden');
}
function closeModal(){ saleModal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
closeBtn?.addEventListener('click', closeModal);
cancelBtn?.addEventListener('click', closeModal);
saleModal.addEventListener('click', e => { if(e.target === saleModal) closeModal(); });
window.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });

function recalcTotal(){
  const meta = getSelectedProductMeta();

  if (!meta) {
    inpTotal.value = "";
    recalcChange();
    return;
  }

  const available = meta.stock;
  const isService = meta.type === 'service' || available === null;

  if (!isService && available !== null && available <= 0) {
    inpTotal.value = "";
    recalcChange();
    if (stockInfo) {
      stockInfo.textContent = 'Sin stock disponible.';
      stockInfo.classList.remove('hidden');
      stockInfo.classList.add('text-rose-600');
    }
    return;
  }

  let qty = Math.max(1, parseInt(inpQty.value || "1", 10));

  if (!isService && available !== null && qty > available) {
    qty = available;
    inpQty.value = String(available);
    showSaleToast(`Solo hay ${available} unidad${available === 1 ? '' : 'es'} disponibles.`);
    if (qtyError) {
      qtyError.textContent = `Cantidad no disponible. Solo hay ${available} unidad${available === 1 ? '' : 'es'} en inventario.`;
      qtyError.classList.remove('hidden');
    }
    if (saveBtn) saveBtn.disabled = true;
  }
  else {
    if (qtyError) {
      qtyError.textContent = '';
      qtyError.classList.add('hidden');
    }
    if (saveBtn) saveBtn.disabled = false;
  }

  const unit = toInt(inpUnit.value);
  const total = qty * unit;
  inpTotal.value = formatCOP(total);
  recalcChange();
}
function recalcChange(){
  const total = toInt(inpTotal.value);
  const given = toInt(inpGiven.value);
  const change = Math.max(0, given - total);
  inpChange.value = formatCOP(change);
}

async function submitSale(event){
  event.preventDefault();

  const axiosInstance = window.axios;
  if (!axiosInstance) {
    showSaleToast('No se encontró Axios en la página.');
    return;
  }

  const meta = getSelectedProductMeta();
  if (!meta) {
    showSaleToast('Selecciona un producto.');
    return;
  }

  const itemId = meta.id;
  const qty    = Math.max(1, parseInt(inpQty.value || "1", 10));
  const unit   = toInt(inpUnit.value);
  const total  = qty * unit;
  const given  = toInt(inpGiven.value);

  const available = meta.stock;
  const isService = meta.type === 'service' || available === null;

  if (!isService && available !== null) {
    if (available <= 0) {
      showSaleToast('Este producto no tiene stock disponible.');
      return;
    }

    if (qty > available) {
      showSaleToast(`Solo hay ${available} unidad${available === 1 ? '' : 'es'} disponibles.`);
      inpQty.value = String(available);
      recalcTotal();
      return;
    }
  }

  if (!itemId) {
    showSaleToast('Producto inválido.');
    return;
  }
  if (unit <= 0 || total <= 0) {
    showSaleToast('El valor unitario y el total deben ser mayores que cero.');
    return;
  }

  const payload = {
    customer_name: document.getElementById('c_name').value || null,
    customer_email: document.getElementById('c_email').value || null,
    customer_phone: document.getElementById('c_phone').value || null,
    payment_method: 'cash',
    amount_received: given || 0,
    items: [
      {
        item_id: itemId,
        quantity: qty,
        unit_price: unit,
      },
    ],
  };

  try {
    saveBtn.disabled = true;
    saveBtn.textContent = 'Guardando...';

    const response = await axiosInstance.post("{{ route('sales.store') }}", payload);

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
    inpQty.value = "";
    inpTotal.value = "";
    if (stockInfo) stockInfo.classList.add('hidden');
    recalcChange();
    return;
  }

  const catCode  = meta.category || "";
  const unit     = meta.unit;
  const available = meta.stock;
  const catLabel = SECTOR_LABELS[catCode] || catCode || "";

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
    inpQty.value = "";
    inpTotal.value = "";
    showSaleToast('Este producto no tiene stock disponible.');
    if (stockInfo) stockInfo.classList.add('text-rose-600');
    recalcChange();
    return;
  }

  if (!inpQty.value || parseInt(inpQty.value, 10) < 1) {
    const defaultQty = available !== null ? Math.min(1, available) : 1;
    inpQty.value = defaultQty > 0 ? defaultQty : "";
  }

  recalcTotal();
}

function formatOnUnit(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcTotal(); }
function formatOnGiven(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcChange(); }

document.addEventListener('DOMContentLoaded', () => {
  saleToast     = document.getElementById('saleToast');
  saleToastText = document.getElementById('saleToastText');

  renderProducts();
  resetSaleForm();

  selProduct.addEventListener('change', onProductChange);
  inpQty.addEventListener('input', recalcTotal);
  inpUnit.addEventListener('input', formatOnUnit);
  inpGiven.addEventListener('input', formatOnGiven);

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
