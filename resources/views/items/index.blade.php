{{-- resources/views/items/index.blade.php
     Gestor de Productos — UI (sin BD)
     - Tabs por categoría (Diseño por defecto)
     - Tabla con Nombre, Precio (COP), Visible, Acciones
     - Modal Crear/Editar (formatea COP, sin decimales)
--}}
@extends('layouts.admin')

@section('title','Ítems — DecoWandy')

@section('content')

  {{-- Header --}}
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Ítems</h1>
      <p class="text-sm text-gray-500">Gestiona los productos que aparecen en el POS.</p>
    </div>
    <button id="btnNew"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white brand-gradient shadow hover:opacity-90">
      <span class="material-symbols-outlined text-base">add</span>
      Nuevo producto
    </button>
  </div>

  {{-- Tabs de categoría --}}
  <div class="mb-4 flex flex-wrap gap-2">
    <button data-cat="Diseño"     class="tab-btn rounded-xl px-4 py-2 text-sm font-semibold bg-indigo-600 text-white shadow">Diseño</button>
    <button data-cat="Papelería"  class="tab-btn rounded-xl px-4 py-2 text-sm font-semibold bg-white border hover:bg-slate-50">Papelería</button>
    <button data-cat="Impresión"  class="tab-btn rounded-xl px-4 py-2 text-sm font-semibold bg-white border hover:bg-slate-50">Impresión</button>
  </div>

  {{-- Tabla --}}
  <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4 shadow-sm">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-500">
          <tr class="text-left">
            <th class="py-2 pr-4">Producto</th>
            <th class="py-2 pr-4">Categoría</th>
            <th class="py-2 pr-4">Precio (COP)</th>
            <th class="py-2 pr-4">Visible en POS</th>
            <th class="py-2 pr-2 w-28 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody id="itemsTable" class="divide-y divide-gray-100">
          {{-- filas por JS --}}
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal Crear/Editar --}}
  <div id="itemModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="relative mx-auto mt-16 w-[min(720px,92%)] rounded-2xl bg-white p-5 shadow-2xl">
      <div class="flex items-center justify-between mb-3">
        <h2 id="modalTitle" class="text-xl font-semibold">Nuevo producto</h2>
        <button id="modalClose" class="h-9 w-9 rounded-full hover:bg-gray-100 flex items-center justify-center">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-1">
          <label class="block text-sm text-gray-600 mb-1">Nombre</label>
          <input id="f_name" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Copias B/N">
        </div>
        <div class="md:col-span-1">
          <label class="block text-sm text-gray-600 mb-1">Categoría</label>
          <select id="f_category" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            <option>Diseño</option>
            <option>Papelería</option>
            <option>Impresión</option>
          </select>
        </div>
        <div class="md:col-span-1">
          <label class="block text-sm text-gray-600 mb-1">Precio base (COP)</label>
          <input id="f_price" type="text" inputmode="numeric" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
          <p class="text-[11px] text-gray-500 mt-1">Se muestra en el POS; sin decimales, con punto de miles.</p>
        </div>
        <div class="md:col-span-1 flex items-end">
          <label class="inline-flex items-center gap-2">
            <input id="f_visible" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" checked>
            <span class="text-sm text-gray-700">Visible en POS</span>
          </label>
        </div>

        {{-- Opcional: control de stock por tipo --}}
        <div class="md:col-span-2 rounded-xl border border-gray-100 p-3 bg-slate-50">
          <div class="flex items-center gap-3">
            <label class="inline-flex items-center gap-2">
              <input id="f_stockable" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
              <span class="text-sm text-gray-700">Este producto controla stock</span>
            </label>
            <span class="text-xs text-gray-500">Úsalo para Papelería y ciertos insumos de Impresión.</span>
          </div>
        </div>
      </div>

      <div class="mt-5 flex items-center justify-end gap-3">
        <button id="modalCancel" type="button" class="rounded-xl bg-white px-4 py-2 border hover:bg-slate-50">Cancelar</button>
        <button id="modalSave" type="button" class="rounded-xl bg-indigo-600 text-white px-4 py-2 shadow hover:bg-indigo-700">Guardar (ensayo)</button>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
/* ===== Utilidades COP ===== */
function onlyDigits(s){return (s||'').replace(/[^\d]/g,'');}
function toInt(s){const z=onlyDigits(s);return z?parseInt(z,10):0;}
function fmt(n){n=Number(n||0);if(!Number.isFinite(n))n=0;return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');}

/* ===== Estado (demo en memoria; sin BD) ===== */
const state = {
  activeCat: 'Diseño',
  items: [
    // Demo inicial (solo para que veas)
    {id:1, name:'Copias B/N',        category:'Impresión', price:200,  visible:true,  stockable:false},
    {id:2, name:'Copias Color',      category:'Impresión', price:600,  visible:true,  stockable:false},
    {id:3, name:'Escáner',           category:'Impresión', price:500,  visible:true,  stockable:false},
    {id:4, name:'Resma Carta',       category:'Impresión', price:25000,visible:true,  stockable:true},
    {id:5, name:'Lápiz HB',          category:'Papelería', price:1000, visible:true,  stockable:true},
    {id:6, name:'Logo básico',       category:'Diseño',    price:30000,visible:true,  stockable:false},
  ],
  nextId: 7,
  editingId: null,
};

/* ===== Tabs ===== */
document.addEventListener('DOMContentLoaded', () => {
  const btns=[...document.querySelectorAll('.tab-btn')];
  function setActive(cat){
    state.activeCat=cat;
    btns.forEach(b=>{
      const active=b.dataset.cat===cat;
      b.className='tab-btn rounded-xl px-4 py-2 text-sm font-semibold '+(active?'bg-indigo-600 text-white shadow':'bg-white border hover:bg-slate-50');
    });
    renderTable();
  }
  btns.forEach(b=>b.addEventListener('click',()=>setActive(b.dataset.cat)));
  setActive('Diseño'); // por defecto
});

/* ===== Render tabla ===== */
function renderTable(){
  const tbody=document.getElementById('itemsTable');
  tbody.innerHTML='';
  const rows=state.items.filter(it=>it.category===state.activeCat);
  if(rows.length===0){
    tbody.innerHTML=`<tr><td colspan="5" class="py-6 text-center text-sm text-gray-500">Sin productos en esta categoría.</td></tr>`;
    return;
  }
  rows.forEach(it=>{
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td class="py-2 pr-4">${escapeHtml(it.name)}</td>
      <td class="py-2 pr-4">${it.category}</td>
      <td class="py-2 pr-4">$ ${fmt(it.price)}</td>
      <td class="py-2 pr-4">
        <span class="px-2 py-1 rounded-full text-xs ${it.visible?'bg-green-100 text-green-700':'bg-gray-100 text-gray-700'}">
          ${it.visible?'Sí':'No'}
        </span>
      </td>
      <td class="py-2 pr-2 text-right">
        <button class="text-[color:var(--dw-primary)] hover:underline mr-2" data-edit="${it.id}">Editar</button>
        <button class="text-rose-500 hover:underline" data-del="${it.id}">Eliminar</button>
      </td>`;
    tbody.appendChild(tr);
  });

  // wire acciones
  tbody.querySelectorAll('[data-edit]').forEach(b=>b.addEventListener('click',()=>openEdit(+b.dataset.edit)));
  tbody.querySelectorAll('[data-del]').forEach(b=>b.addEventListener('click',()=>delItem(+b.dataset.del)));
}

/* ===== Modal ===== */
function qs(id){return document.getElementById(id);}
function openNew(){
  state.editingId=null;
  qs('modalTitle').textContent='Nuevo producto';
  qs('f_name').value='';
  qs('f_category').value=state.activeCat;
  qs('f_price').value='0';
  qs('f_visible').checked=true;
  qs('f_stockable').checked=(state.activeCat!=='Diseño'); // por defecto
  showModal(true);
}
function openEdit(id){
  const it=state.items.find(x=>x.id===id);
  if(!it) return;
  state.editingId=id;
  qs('modalTitle').textContent='Editar producto';
  qs('f_name').value=it.name;
  qs('f_category').value=it.category;
  qs('f_price').value=fmt(it.price);
  qs('f_visible').checked=it.visible;
  qs('f_stockable').checked=it.stockable;
  showModal(true);
}
function saveItem(){
  const name=qs('f_name').value.trim();
  const category=qs('f_category').value;
  const price=toInt(qs('f_price').value);
  const visible=qs('f_visible').checked;
  const stockable=qs('f_stockable').checked;
  if(!name){ alert('Escribe un nombre.'); return; }

  if(state.editingId){
    const it=state.items.find(x=>x.id===state.editingId);
    if(!it) return;
    it.name=name; it.category=category; it.price=price; it.visible=visible; it.stockable=stockable;
  }else{
    state.items.push({id:state.nextId++, name, category, price, visible, stockable});
  }
  showModal(false);
  renderTable();
}
function delItem(id){
  if(!confirm('¿Eliminar este producto?')) return;
  state.items = state.items.filter(x=>x.id!==id);
  renderTable();
}
function showModal(v){
  const m=qs('itemModal');
  m.classList.toggle('hidden', !v);
}
function escapeHtml(s){return (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}

document.addEventListener('DOMContentLoaded', () => {
  qs('btnNew').addEventListener('click', openNew);
  qs('modalClose').addEventListener('click', ()=>showModal(false));
  qs('modalCancel').addEventListener('click', ()=>showModal(false));
  qs('modalSave').addEventListener('click', saveItem);
  qs('f_price').addEventListener('input', e=> e.target.value = fmt(toInt(e.target.value)));
});

</script>
@endpush
