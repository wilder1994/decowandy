{{-- resources/views/purchases/index.blade.php
     Compras — UI (tres pestañas con filas dinámicas)
     - Diseño | Papelería | Impresión
     - Cada pestaña: tabla dinámica (Cantidad • Costo total • Costo por unidad calc)
     - Toggle "Agregar al inventario" en las tres
--}}
@extends('layouts.admin')

@section('title','Compras — DecoWandy')

@section('content')

  {{-- Encabezado --}}
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Compras</h1>
    <p class="text-sm text-gray-500">Registra compras por categoría. Puedes decidir si entran a inventario.</p>
  </div>

  {{-- Pestañas --}}
  <div class="mb-6 flex flex-wrap gap-2">
    <button data-mode="Diseno"    class="mode-btn rounded-xl px-4 py-2 text-sm font-semibold bg-indigo-600 text-white shadow">Diseño</button>
    <button data-mode="Papeleria" class="mode-btn rounded-xl px-4 py-2 text-sm font-semibold bg-white border hover:bg-slate-50">Papelería</button>
    <button data-mode="Impresion" class="mode-btn rounded-xl px-4 py-2 text-sm font-semibold bg-white border hover:bg-slate-50">Impresión</button>
  </div>

  <div class="space-y-6">
    {{-- ===================== DISEÑO (filas dinámicas) ===================== --}}
    <section id="form-Diseno" class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold flex items-center gap-2">
          <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:linear-gradient(90deg, var(--dw-primary), var(--dw-yellow))"></span>
          Compra para Diseño
        </h2>
        <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/30 text-[color:var(--dw-primary)]">Digital / Servicios</span>
      </div>

      {{-- Toggle inventario --}}
      <div class="mb-3">
        <label class="inline-flex items-center gap-2">
          <input id="d_to_stock" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
          <span class="text-sm text-gray-700">Agregar al inventario</span>
        </label>
        <span class="ml-2 text-xs text-gray-500">Útil si en el futuro ciertos diseños fueran ítems físicos.</span>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Fecha</label>
          <input type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Proveedor (opcional)</label>
          <input type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Nombre proveedor">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600 mb-1">Nota (opcional)</label>
          <input type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Licencia, fuente, imágenes, etc.">
        </div>
      </div>

      {{-- Tabla dinámica (como Papelería) --}}
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left">
              <th class="py-2 pr-3">Concepto / Producto</th>
              <th class="py-2 pr-3 w-28">Cantidad</th>
              <th class="py-2 pr-3 w-44">Costo total (COP)</th>
              <th class="py-2 pr-3 w-44">Costo por unidad (COP)</th>
              <th class="py-2 w-16"></th>
            </tr>
          </thead>
          <tbody id="d_lines" class="divide-y divide-gray-100">
            {{-- filas por JS --}}
          </tbody>
        </table>
      </div>

      <div class="mt-3 flex items-center gap-3">
        <button type="button" id="d_add_line"
                class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-2 border hover:bg-slate-50">
          Agregar línea
        </button>
        <span class="text-xs text-gray-500">Ej: paquete de íconos, mockups, tipografías, etc.</span>
      </div>

      {{-- Totales --}}
      <div class="mt-5 grid gap-4 md:grid-cols-3">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Unidades totales</label>
          <input id="d_total_units" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Líneas</label>
          <input id="d_total_lines" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Total compra (COP)</label>
          <input id="d_total_amount" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
      </div>

      <div class="mt-5 flex items-center gap-3">
        <button type="button" class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-700">Guardar (ensayo)</button>
        <span class="text-xs text-gray-500">Solo UI.</span>
      </div>
    </section>

    {{-- ===================== PAPELERÍA (filas dinámicas) ===================== --}}
    <section id="form-Papeleria" class="hidden rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold flex items-center gap-2">
          <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:linear-gradient(90deg, var(--dw-lilac), var(--dw-rose))"></span>
          Compra para Papelería
        </h2>
        <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/30 text-[color:var(--dw-primary)]">Muchos productos (manual)</span>
      </div>

      {{-- Toggle inventario --}}
      <div class="mb-3">
        <label class="inline-flex items-center gap-2">
          <input id="p_to_stock" type="checkbox" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
          <span class="text-sm text-gray-700">Agregar al inventario</span>
        </label>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Fecha</label>
          <input type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Proveedor (opcional)</label>
          <input type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600 mb-1">Nota (opcional)</label>
          <input type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: surtido escolar">
        </div>
      </div>

      {{-- Tabla dinámica --}}
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left">
              <th class="py-2 pr-3">Producto</th>
              <th class="py-2 pr-3 w-28">Cantidad</th>
              <th class="py-2 pr-3 w-44">Costo total (COP)</th>
              <th class="py-2 pr-3 w-44">Costo por unidad (COP)</th>
              <th class="py-2 w-16"></th>
            </tr>
          </thead>
          <tbody id="p_lines" class="divide-y divide-gray-100">
          </tbody>
        </table>
      </div>

      <div class="mt-3 flex items-center gap-3">
        <button type="button" id="p_add_line"
                class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-2 border hover:bg-slate-50">
          Agregar línea
        </button>
        <span class="text-xs text-gray-500">Cuadernos, lápices, borradores, carpetas, etc.</span>
      </div>

      {{-- Totales --}}
      <div class="mt-5 grid gap-4 md:grid-cols-3">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Unidades totales</label>
          <input id="p_total_units" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Líneas</label>
          <input id="p_total_lines" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Total compra (COP)</label>
          <input id="p_total_amount" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
      </div>

      <div class="mt-5 flex items-center gap-3">
        <button type="button" class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-700">Guardar (ensayo)</button>
        <span class="text-xs text-gray-500">Cada línea se agregará al inventario si está marcado.</span>
      </div>
    </section>

    {{-- ===================== IMPRESIÓN (filas dinámicas) ===================== --}}
    <section id="form-Impresion" class="hidden rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-5 shadow-sm">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="font-semibold flex items-center gap-2">
          <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:linear-gradient(90deg, var(--dw-lilac), var(--dw-primary))"></span>
          Compra para Impresión
        </h2>
        <span class="text-xs px-2 py-1 rounded-full bg-[color:var(--dw-lilac)]/30 text-[color:var(--dw-primary)]">Con o sin inventario</span>
      </div>

      {{-- Toggle inventario --}}
      <div class="mb-3">
        <label class="inline-flex items-center gap-2">
          <input id="i_to_stock" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
          <span class="text-sm text-gray-700">Agregar al inventario</span>
        </label>
        <span class="ml-2 text-xs text-gray-500">Desmarca para gastos como tinta/mantenimiento.</span>
      </div>

      <div class="grid gap-4 md:grid-cols-4">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Fecha</label>
          <input type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Proveedor (opcional)</label>
          <input type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600 mb-1">Nota (opcional)</label>
          <input type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: resmas, papel foto, etc.">
        </div>
      </div>

      {{-- Tabla dinámica --}}
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="text-gray-500">
            <tr class="text-left">
              <th class="py-2 pr-3">Producto / Tipo</th>
              <th class="py-2 pr-3 w-28">Cantidad</th>
              <th class="py-2 pr-3 w-44">Costo total (COP)</th>
              <th class="py-2 pr-3 w-44">Costo por unidad (COP)</th>
              <th class="py-2 w-16"></th>
            </tr>
          </thead>
          <tbody id="i_lines" class="divide-y divide-gray-100">
          </tbody>
        </table>
      </div>

      <div class="mt-3 flex items-center gap-3">
        <button type="button" id="i_add_line"
                class="rounded-xl bg-white text-slate-700 font-semibold px-4 py-2 border hover:bg-slate-50">
          Agregar línea
        </button>
        <span class="text-xs text-gray-500">Ej: “Resma carta (500)”, “Papel fotográfico (20)”, etc.</span>
      </div>

      {{-- Totales --}}
      <div class="mt-5 grid gap-4 md:grid-cols-3">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Unidades totales</label>
          <input id="i_total_units" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Líneas</label>
          <input id="i_total_lines" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Total compra (COP)</label>
          <input id="i_total_amount" type="text" class="w-full rounded-lg border-gray-300 bg-slate-100 text-slate-700" readonly>
        </div>
      </div>

      <div class="mt-5 flex items-center gap-3">
        <button type="button" class="rounded-xl bg-indigo-600 text-white font-semibold px-4 py-2 shadow hover:bg-indigo-700">Guardar (ensayo)</button>
        <span class="text-xs text-gray-500">Cada línea se agregará al inventario si está marcado.</span>
      </div>
    </section>
  </div>

@endsection

@push('scripts')
<script>
/* ===== Utilidades COP ===== */
function onlyDigits(s){return (s||'').replace(/[^\d]/g,'');}
function toInt(s){const z=onlyDigits(s);return z?parseInt(z,10):0;}
function fmt(n){n=Number(n||0);if(!Number.isFinite(n))n=0;return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');}

/* ===== Pestañas ===== */
document.addEventListener('DOMContentLoaded', () => {
  const modes=['Diseno','Papeleria','Impresion'];
  const btns=[...document.querySelectorAll('.mode-btn')];
  function show(mode){
    modes.forEach(m=>{
      document.getElementById('form-'+m).classList.toggle('hidden', m!==mode);
    });
    btns.forEach(b=>{
      const active=b.dataset.mode===mode;
      b.className = 'mode-btn rounded-xl px-4 py-2 text-sm font-semibold ' + (active?'bg-indigo-600 text-white shadow':'bg-white border hover:bg-slate-50');
    });
  }
  btns.forEach(b=>b.addEventListener('click',()=>show(b.dataset.mode)));
  show('Diseno'); // por defecto
});

/* ===== Constructor de tablas dinámicas (reutilizable) ===== */
function makeLinesTable(prefix){
  const tbody = document.getElementById(prefix+'_lines');
  const addBtn = document.getElementById(prefix+'_add_line');
  const totalUnits = document.getElementById(prefix+'_total_units');
  const totalLines = document.getElementById(prefix+'_total_lines');
  const totalAmount = document.getElementById(prefix+'_total_amount');

  function rowTemplate(){
    const tr=document.createElement('tr');
    tr.innerHTML = `
      <td class="py-2 pr-3">
        <input type="text" placeholder="Descripción / Producto"
               class="name w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
      </td>
      <td class="py-2 pr-3">
        <input type="number" min="1" value="1"
               class="qty w-24 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-right">
      </td>
      <td class="py-2 pr-3">
        <input type="text" inputmode="numeric" placeholder="0"
               class="total w-40 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-right">
      </td>
      <td class="py-2 pr-3">
        <input type="text" readonly
               class="unit w-40 rounded-lg border-gray-300 bg-slate-100 text-slate-700 text-right">
      </td>
      <td class="py-2">
        <button type="button" class="del px-2 py-1 rounded-lg border hover:bg-slate-50">Quitar</button>
      </td>
    `;
    return tr;
  }

  function recalc(){
    let units=0, amount=0, lines=0;
    tbody.querySelectorAll('tr').forEach(tr=>{
      const q = Math.max(1, parseInt(tr.querySelector('.qty').value||'1',10));
      const t = toInt(tr.querySelector('.total').value);
      const u = q>0 ? Math.floor(t/q) : 0;
      tr.querySelector('.unit').value = fmt(u);
      units += q;
      amount += t;
      lines++;
    });
    if(totalUnits) totalUnits.value = fmt(units);
    if(totalLines) totalLines.value = fmt(lines);
    if(totalAmount) totalAmount.value = fmt(amount);
  }

  function wire(tr){
    const qty  = tr.querySelector('.qty');
    const total = tr.querySelector('.total');
    const del  = tr.querySelector('.del');
    total.addEventListener('input', e=>{ e.target.value=fmt(toInt(e.target.value)); recalc(); });
    qty.addEventListener('input', recalc);
    del.addEventListener('click', ()=>{ tr.remove(); recalc(); });
  }

  addBtn?.addEventListener('click', ()=>{
    const tr=rowTemplate();
    tbody.appendChild(tr);
    wire(tr);
    recalc();
  });

  // una fila por defecto
  addBtn?.click();

  // API mínima para otros usos (si hiciera falta)
  return { recalc };
}

/* ===== Inicializar tablas de las tres pestañas ===== */
document.addEventListener('DOMContentLoaded', () => {
  makeLinesTable('d'); // Diseño
  makeLinesTable('p'); // Papelería
  makeLinesTable('i'); // Impresión
});
</script>
@endpush
