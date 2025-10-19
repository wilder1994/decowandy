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
                <button id="saveSaleDraft"
                        class="flex-1 rounded-xl bg-indigo-600 text-white font-semibold px-4 py-3 shadow hover:bg-indigo-700">
                  Guardar (ensayo)
                </button>
                <button id="cancelSale"
                        class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-3 border hover:bg-slate-50">
                  Cancelar
                </button>
              </div>
              <p class="text-xs text-slate-500">* Ensayo visual: no guarda en base de datos.</p>
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

/* ===== Dataset demo (reemplazar luego por datos reales) ===== */
const DATASET = {
  "Impresión": [
    { id: 1, name: "Copias B/N", unit: 200 },
    { id: 2, name: "Copias Color", unit: 600 },
    { id: 3, name: "Escáner", unit: 1000 }
  ],
  "Papelería": [
    { id: 4, name: "Resma Carta", unit: 25000 },
    { id: 5, name: "Lápiz HB", unit: 1000 }
  ],
  "Diseño": [
    { id: 6, name: "Logo básico", unit: 150000 },
    { id: 7, name: "Afiche A3 diseño", unit: 50000 }
  ]
};

/* ===== Refs ===== */
const saleModal = document.getElementById('saleModal');
const openBtn   = document.getElementById('openSaleModal');
const closeBtn  = document.getElementById('closeSaleModal');
const cancelBtn = document.getElementById('cancelSale');

const selProduct = document.getElementById('p_product');
const badgeCat   = document.getElementById('p_category_badge');
const inpQty     = document.getElementById('p_qty');
const inpUnit    = document.getElementById('p_unit');
const inpTotal   = document.getElementById('p_total');
const inpGiven   = document.getElementById('pay_given');
const inpChange  = document.getElementById('pay_change');

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

/* ===== Al cambiar producto: actualizar categoría (badge) y precio unitario ===== */
function onProductChange(){
  const sel = selProduct.options[selProduct.selectedIndex];
  if(!sel) return;
  const cat  = sel.dataset.category || "";
  const unit = parseInt(sel.dataset.unit || "0", 10);
  if(badgeCat) badgeCat.textContent = cat || "—";
  inpUnit.value = formatCOP(unit);
  if(!inpQty.value || parseInt(inpQty.value,10) < 1) inpQty.value = 1;
  recalcTotal();
}

/* ===== Formateo COP en inputs visibles ===== */
function formatOnUnit(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcTotal(); }
function formatOnGiven(e){ e.target.value = formatCOP(toInt(e.target.value)); recalcChange(); }

/* ===== Init ===== */
document.addEventListener('DOMContentLoaded', () => {
  renderProducts();
  if(selProduct.options.length > 0){
    selProduct.selectedIndex = 0;
    onProductChange();
  }
  selProduct.addEventListener('change', onProductChange);
  inpQty.addEventListener('input', recalcTotal);
  inpUnit.addEventListener('input', formatOnUnit);
  inpGiven.addEventListener('input', formatOnGiven);
});
</script>
@endpush
