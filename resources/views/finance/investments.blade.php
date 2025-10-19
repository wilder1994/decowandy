{{-- resources/views/finance/investments.blade.php
     Inversiones — Solo diseño (UI demo con CRUD en memoria)
--}}
@extends('layouts.admin')

@section('title','Inversiones — DecoWandy')

@section('content')
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Inversiones</h1>
      <p class="text-sm text-gray-500">Registra inversión inicial y nuevas inversiones.</p>
    </div>
    <button id="btnNewInv"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white brand-gradient shadow hover:opacity-90">
      <span class="material-symbols-outlined text-base">add</span> Nueva inversión
    </button>
  </div>

  {{-- Resumen --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Invertido acumulado</div>
      <div id="inv_total" class="mt-1 text-2xl font-bold">$ 0</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Flujo neto acumulado</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">* Demo</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">% recuperado</div>
      <div class="mt-1 text-2xl font-bold">0%</div>
      <div class="text-xs text-gray-500 mt-1">* Demo</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Por recuperar</div>
      <div class="mt-1 text-2xl font-bold">$ 0</div>
      <div class="text-xs text-gray-500 mt-1">* Demo</div>
    </div>
  </div>

  {{-- Tabla --}}
  <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-500">
          <tr class="text-left">
            <th class="py-2 pr-4">Fecha</th>
            <th class="py-2 pr-4">Concepto</th>
            <th class="py-2 pr-4">Monto (COP)</th>
            <th class="py-2 pr-4">Notas</th>
            <th class="py-2 w-28 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody id="invTable" class="divide-y divide-gray-100">
          {{-- filas por JS --}}
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal --}}
  <div id="invModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="relative mx-auto mt-16 w-[min(680px,92%)] rounded-2xl bg-white p-5 shadow-2xl">
      <div class="flex items-center justify-between mb-3">
        <h2 id="invTitle" class="text-xl font-semibold">Nueva inversión</h2>
        <button id="invClose" class="h-9 w-9 rounded-full hover:bg-gray-100 flex items-center justify-center">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="block text-sm text-gray-600 mb-1">Fecha</label>
          <input id="f_date" type="date" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Concepto</label>
          <input id="f_concept" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Computador, impresora...">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Monto (COP)</label>
          <input id="f_amount" type="text" inputmode="numeric" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Notas (opcional)</label>
          <input id="f_notes" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
      </div>

      <div class="mt-5 flex items-center justify-end gap-3">
        <button id="invCancel" type="button" class="rounded-xl bg-white px-4 py-2 border hover:bg-slate-50">Cancelar</button>
        <button id="invSave" type="button" class="rounded-xl bg-indigo-600 text-white px-4 py-2 shadow hover:bg-indigo-700">Guardar (ensayo)</button>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
function onlyDigits(s){return (s||'').replace(/[^\d]/g,'');}
function toInt(s){const z=onlyDigits(s);return z?parseInt(z,10):0;}
function fmt(n){n=Number(n||0);if(!Number.isFinite(n))n=0;return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');}

const invState = {
  list: [
    // demo
    {id:1, date:'2025-10-01', concept:'Computador', amount:2500000, notes:'Equipo principal'},
    {id:2, date:'2025-10-05', concept:'Impresora', amount:1800000, notes:'Multifunción'},
  ],
  nextId:3,
  editingId:null,
};

function renderInv(){
  const tb=document.getElementById('invTable');
  tb.innerHTML='';
  let total=0;
  invState.list.forEach(x=>{
    total += x.amount;
    const tr=document.createElement('tr');
    tr.innerHTML = `
      <td class="py-2 pr-4">${x.date}</td>
      <td class="py-2 pr-4">${escapeHtml(x.concept)}</td>
      <td class="py-2 pr-4">$ ${fmt(x.amount)}</td>
      <td class="py-2 pr-4 text-gray-500">${escapeHtml(x.notes||'')}</td>
      <td class="py-2 pr-2 text-right">
        <button class="text-[color:var(--dw-primary)] hover:underline mr-2" data-edit="${x.id}">Editar</button>
        <button class="text-rose-500 hover:underline" data-del="${x.id}">Eliminar</button>
      </td>`;
    tb.appendChild(tr);
  });
  document.getElementById('inv_total').textContent = '$ ' + fmt(total);

  tb.querySelectorAll('[data-edit]').forEach(b=>b.addEventListener('click',()=>openEdit(+b.dataset.edit)));
  tb.querySelectorAll('[data-del]').forEach(b=>b.addEventListener('click',()=>delInv(+b.dataset.del)));
}
function escapeHtml(s){return (s||'').replace(/[&<>"']/g,m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));}

function openNew(){
  invState.editingId=null;
  document.getElementById('invTitle').textContent='Nueva inversión';
  document.getElementById('f_date').value='';
  document.getElementById('f_concept').value='';
  document.getElementById('f_amount').value='0';
  document.getElementById('f_notes').value='';
  showModal(true);
}
function openEdit(id){
  const it = invState.list.find(x=>x.id===id);
  if(!it) return;
  invState.editingId=id;
  document.getElementById('invTitle').textContent='Editar inversión';
  document.getElementById('f_date').value=it.date;
  document.getElementById('f_concept').value=it.concept;
  document.getElementById('f_amount').value=fmt(it.amount);
  document.getElementById('f_notes').value=it.notes||'';
  showModal(true);
}
function saveInv(){
  const date = document.getElementById('f_date').value;
  const concept = document.getElementById('f_concept').value.trim();
  const amount = toInt(document.getElementById('f_amount').value);
  const notes  = document.getElementById('f_notes').value.trim();
  if(!date || !concept || amount<=0){ alert('Completa fecha, concepto y monto.'); return; }

  if(invState.editingId){
    const it = invState.list.find(x=>x.id===invState.editingId);
    if(!it) return;
    it.date=date; it.concept=concept; it.amount=amount; it.notes=notes;
  }else{
    invState.list.push({id:invState.nextId++, date, concept, amount, notes});
  }
  showModal(false);
  renderInv();
}
function delInv(id){
  if(!confirm('¿Eliminar inversión?')) return;
  invState.list = invState.list.filter(x=>x.id!==id);
  renderInv();
}
function showModal(v){
  document.getElementById('invModal').classList.toggle('hidden', !v);
}

document.addEventListener('DOMContentLoaded', ()=>{
  renderInv();
  document.getElementById('btnNewInv').addEventListener('click', openNew);
  document.getElementById('invSave').addEventListener('click', saveInv);
  document.getElementById('invCancel').addEventListener('click', ()=>showModal(false));
  document.getElementById('invClose').addEventListener('click', ()=>showModal(false));
  document.getElementById('f_amount').addEventListener('input', e=>e.target.value = fmt(toInt(e.target.value)));
});
</script>
@endpush
