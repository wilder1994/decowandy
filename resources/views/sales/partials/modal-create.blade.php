{{-- resources/views/sales/partials/modal-create.blade.php --}}
{{-- Modal: Registrar venta (solo UI; sin guardar en BD) --}}

<!-- ============ MODAL REGISTRAR VENTA ============ -->
<div id="saleModal" class="fixed inset-0 z-[70] hidden">
  <!-- Fondo -->
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

  <!-- Contenedor -->
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden">

      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b">
        <h3 class="text-xl font-bold text-slate-800">Registrar venta</h3>
        <button id="closeSaleModal"
                class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 hover:text-slate-700"
                aria-label="Cerrar modal">
          ✕
        </button>
      </div>

      <!-- Body -->
      <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna izquierda: Cliente + Producto (ocupa 2) -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Cliente (opcional) -->
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

          <!-- Producto (categoría centrada + campos separados) -->
          <div class="bg-white rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h4 class="font-semibold text-slate-700">Producto</h4>
            </div>

            <!-- Categoría (badge centrado) -->
            <div class="mt-2 flex justify-center">
              <span id="p_category_badge"
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-slate-100 text-slate-700">
                Selecciona un producto
              </span>
            </div>

            <!-- Campos: dos columnas -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Izquierda -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_product">Producto</label>
                  <select id="p_product"
                          class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <!-- Opciones generadas por JS (solo nombre) -->
                  </select>
                </div>

                <div>
                  <label class="block text-sm text-slate-600 mb-1" for="p_qty">Cantidad</label>
                  <input id="p_qty" type="number" min="1" value="1"
                         class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>
              </div>

              <!-- Derecha -->
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

        <!-- Columna derecha: Pago -->
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

      </div><!-- /Body -->
    </div>
  </div>
</div>
<!-- ============ /MODAL REGISTRAR VENTA ============ -->

@push('scripts')
<script>
/* ===== Utilidades ===== */
function onlyDigits(str){ return (str || '').replace(/[^\d]/g,''); }
function toInt(str){ const s = onlyDigits(str); return s ? parseInt(s,10) : 0; }
function formatCOP(num){
  let n = Number(num || 0);
  if(!Number.isFinite(n)) n = 0;
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/* ===== Dataset real desde la BD (items) ===== */
@php
    // Si NO viene $catalogDataset (por ejemplo en el dashboard), usamos un arreglo vacío
    $modalCatalogDataset = $catalogDataset ?? [];
@endphp
const DATASET = @json($modalCatalogDataset, JSON_UNESCAPED_UNICODE);


/* Etiquetas bonitas para las categorías */
const SECTOR_LABELS = {
  impresion: 'Impresión',
  papeleria: 'Papelería',
  diseno: 'Diseño',
};

/* ===== Refs ===== */
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

/* Toast global para mensajes de venta */
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

/* ===== Render de opciones (solo nombre; guardamos categoría/precio en data-*) ===== */
function renderProducts(){
  selProduct.innerHTML = "";
  Object.keys(DATASET).forEach(cat => {
    DATASET[cat].forEach(p => {
      const opt = document.createElement('option');
      opt.value = String(p.id);
      opt.textContent = p.name;
      opt.dataset.category = cat;
      opt.dataset.unit = String(p.unit);
      selProduct.appendChild(opt);
    });
  });
}

/* ===== Abrir / Cerrar modal ===== */
function openModal(){ saleModal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); }
function closeModal(){ saleModal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
openBtn?.addEventListener('click', openModal);
closeBtn?.addEventListener('click', closeModal);
cancelBtn?.addEventListener('click', closeModal);
saleModal.addEventListener('click', e => { if(e.target === saleModal) closeModal(); });
window.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });

/* ===== Cálculos ===== */
function recalcTotal(){
  const qty  = Math.max(1, parseInt(inpQty.value || "1", 10));
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

/* ===== Enviar venta al backend ===== */
async function submitSale(event){
  event.preventDefault();

  const axiosInstance = window.axios;
  if (!axiosInstance) {
    showSaleToast('No se encontró Axios en la página.');
    return;
  }

  const sel = selProduct.options[selProduct.selectedIndex];
  if (!sel) {
    showSaleToast('Selecciona un producto.');
    return;
  }

  const itemId = parseInt(sel.value || "0", 10);
  const qty    = Math.max(1, parseInt(inpQty.value || "1", 10));
  const unit   = toInt(inpUnit.value);
  const total  = qty * unit;
  const given  = toInt(inpGiven.value);

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

/* ===== Al cambiar producto: actualizar categoría (badge) y precio unitario ===== */
function onProductChange(){
  const sel = selProduct.options[selProduct.selectedIndex];
  if(!sel) return;
  const catCode  = sel.dataset.category || "";
  const unit     = parseInt(sel.dataset.unit || "0", 10);
  const catLabel = SECTOR_LABELS[catCode] || catCode || "—";
  if (badgeCat) badgeCat.textContent = catLabel;
  inpUnit.value = formatCOP(unit);
  if (!inpQty.value || parseInt(inpQty.value, 10) < 1) inpQty.value = 1;
  recalcTotal();
}

/* ===== Formateo COP en inputs visibles ===== */
function formatOnUnit(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcTotal(); }
function formatOnGiven(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcChange(); }

/* ===== Init ===== */
document.addEventListener('DOMContentLoaded', () => {
  saleToast     = document.getElementById('saleToast');
  saleToastText = document.getElementById('saleToastText');

  renderProducts();

  if(selProduct.options.length > 0){
    selProduct.selectedIndex = 0;
    onProductChange();
  }

  selProduct.addEventListener('change', onProductChange);
  inpQty.addEventListener('input', recalcTotal);
  inpUnit.addEventListener('input', formatOnUnit);
  inpGiven.addEventListener('input', formatOnGiven);

  if (saveBtn) {
    saveBtn.addEventListener('click', submitSale);
  }
});
</script>
@endpush
