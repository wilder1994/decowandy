{{-- resources/views/finance/investments.blade.php
     Inversiones — CRUD real sobre la tabla investments
--}}
@extends('layouts.admin')

@section('title','Inversiones — DecoWandy')

@section('content')
  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Inversiones</h1>
      <p class="text-sm text-gray-500">Registra inversión inicial y nuevas inyecciones de capital.</p>
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
      <div id="inv_total" class="mt-1 text-2xl font-bold">${{ number_format($total, 0, ',', '.') }}</div>
      <div class="text-xs text-gray-500 mt-1">Datos guardados en la base.</div>
    </div>
    <div class="rounded-2xl bg-[color:var(--dw-card)] border border-gray-100 p-4">
      <div class="text-sm text-gray-500">Última actualización</div>
      <div class="mt-1 text-2xl font-bold" id="inv_last_update">{{ optional($investments->first())->date?->format('d/m/Y') ?? '-' }}</div>
      <div class="text-xs text-gray-500 mt-1">Ordenado por fecha y creación.</div>
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
    <div id="invAlert" class="hidden mt-4 rounded-xl border px-3 py-2 text-sm"></div>
  </div>

  {{-- Modal --}}
  <div id="invModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/30" data-close="1"></div>
    <div class="relative mx-auto mt-16 w-[min(680px,92%)] rounded-2xl bg-white p-5 shadow-2xl">
      <div class="flex items-center justify-between mb-3">
        <h2 id="invTitle" class="text-xl font-semibold">Nueva inversión</h2>
        <button id="invClose" class="h-9 w-9 rounded-full hover:bg-gray-100 flex items-center justify-center">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div id="invModalAlert" class="hidden mb-3 rounded-xl border px-3 py-2 text-sm"></div>

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
          <input id="f_amount" type="number" min="0" step="1" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
        </div>
        <div>
          <label class="block text-sm text-gray-600 mb-1">Notas (opcional)</label>
          <input id="f_notes" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>
      </div>

      <div class="mt-5 flex items-center justify-end gap-3">
        <button id="invCancel" type="button" class="rounded-xl bg-white px-4 py-2 border hover:bg-slate-50">Cancelar</button>
        <button id="invSave" type="button" class="rounded-xl bg-indigo-600 text-white px-4 py-2 shadow hover:bg-indigo-700">Guardar</button>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  const initialInvestments = @json($investments, JSON_UNESCAPED_UNICODE);
  const investmentsState = {
    list: Array.isArray(initialInvestments) ? initialInvestments : [],
    editingId: null,
    endpoints: {
      list: @json(route('api.investments.index')),
      base: @json(route('api.investments.store')),
    },
    csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
  };

  function fmt(number) {
    const formatter = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });
    const n = Number(number || 0);
    return formatter.format(Number.isFinite(n) ? n : 0);
  }

  function showAlert(box, type, message) {
    box.textContent = message;
    box.classList.remove('hidden', 'border-red-200', 'text-red-700', 'bg-red-50', 'border-emerald-200', 'text-emerald-700', 'bg-emerald-50');
    if (type === 'error') {
      box.classList.add('border-red-200', 'text-red-700', 'bg-red-50');
    } else {
      box.classList.add('border-emerald-200', 'text-emerald-700', 'bg-emerald-50');
    }
  }

  function resetModal() {
    document.getElementById('f_date').value = '';
    document.getElementById('f_concept').value = '';
    document.getElementById('f_amount').value = '';
    document.getElementById('f_notes').value = '';
    document.getElementById('invModalAlert').classList.add('hidden');
    investmentsState.editingId = null;
    document.getElementById('invTitle').textContent = 'Nueva inversión';
  }

  function openModal() {
    document.getElementById('invModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeModal() {
    document.getElementById('invModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }

  function renderInvestments() {
    const tb = document.getElementById('invTable');
    tb.innerHTML = '';
    let total = 0;

    investmentsState.list.forEach((item) => {
      total += Number(item.amount || 0);
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="py-2 pr-4">${formatDate(item.date)}</td>
        <td class="py-2 pr-4">${escapeHtml(item.concept)}</td>
        <td class="py-2 pr-4">$ ${fmt(item.amount)}</td>
        <td class="py-2 pr-4 text-gray-500">${escapeHtml(item.note || '')}</td>
        <td class="py-2 pr-2 text-right">
          <button class="text-[color:var(--dw-primary)] hover:underline mr-2" data-edit="${item.id}">Editar</button>
          <button class="text-rose-500 hover:underline" data-del="${item.id}">Eliminar</button>
        </td>`;
      tb.appendChild(tr);
    });

    document.getElementById('inv_total').textContent = '$' + fmt(total);
    const last = investmentsState.list[0];
    document.getElementById('inv_last_update').textContent = formatDate(last?.date) ?? '-';

    tb.querySelectorAll('[data-edit]').forEach(btn => btn.addEventListener('click', () => openEdit(Number(btn.dataset.edit))));
    tb.querySelectorAll('[data-del]').forEach(btn => btn.addEventListener('click', () => deleteInvestment(Number(btn.dataset.del))));
  }

  function escapeHtml(str) {
    return (str || '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));
  }

  function formatDate(value) {
    if (!value) return '-';
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleDateString('es-CO', { day:'2-digit', month:'2-digit', year:'numeric' });
  }

  function openNew() {
    resetModal();
    openModal();
  }

  function openEdit(id) {
    const item = investmentsState.list.find(x => Number(x.id) === Number(id));
    if (!item) return;
    investmentsState.editingId = item.id;
    document.getElementById('invTitle').textContent = 'Editar inversión';
    document.getElementById('f_date').value = item.date;
    document.getElementById('f_concept').value = item.concept;
    document.getElementById('f_amount').value = item.amount;
    document.getElementById('f_notes').value = item.note || '';
    document.getElementById('invModalAlert').classList.add('hidden');
    openModal();
  }

  async function loadInvestments() {
    const alertBox = document.getElementById('invAlert');
    try {
      const response = await fetch(investmentsState.endpoints.list, { headers:{ 'Accept':'application/json' }});
      const data = await response.json();
      if (data?.ok && Array.isArray(data.data)) {
        investmentsState.list = data.data;
        renderInvestments();
        alertBox.classList.add('hidden');
      } else {
        showAlert(alertBox, 'error', 'No se pudieron cargar las inversiones.');
      }
    } catch (error) {
      console.error(error);
      showAlert(alertBox, 'error', 'No se pudieron cargar las inversiones.');
    }
  }

  function getPayload() {
    return {
      date: document.getElementById('f_date').value,
      concept: document.getElementById('f_concept').value.trim(),
      amount: Number(document.getElementById('f_amount').value || 0),
      note: document.getElementById('f_notes').value.trim() || null,
    };
  }

  async function saveInvestment() {
    const payload = getPayload();
    const modalAlert = document.getElementById('invModalAlert');
    if (!payload.date || !payload.concept || payload.amount <= 0) {
      showAlert(modalAlert, 'error', 'Completa fecha, concepto y monto mayor a cero.');
      return;
    }

    const method = investmentsState.editingId ? 'PUT' : 'POST';
    const url = investmentsState.editingId
      ? `${investmentsState.endpoints.base}/${investmentsState.editingId}`
      : investmentsState.endpoints.base;

    try {
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': investmentsState.csrf,
        },
        body: JSON.stringify(payload),
      });

      if (!response.ok) {
        const data = await response.json().catch(() => null);
        const message = data?.message || 'No se pudo guardar la inversión.';
        showAlert(modalAlert, 'error', message);
        return;
      }

      await loadInvestments();
      closeModal();
      showAlert(document.getElementById('invAlert'), 'success', 'Inversión guardada correctamente.');
    } catch (error) {
      console.error(error);
      showAlert(modalAlert, 'error', 'Ocurrió un error al guardar.');
    }
  }

  async function deleteInvestment(id) {
    if (!confirm('¿Eliminar inversión?')) return;
    const alertBox = document.getElementById('invAlert');
    try {
      const response = await fetch(`${investmentsState.endpoints.base}/${id}` , {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': investmentsState.csrf,
        },
      });

      if (!response.ok) {
        showAlert(alertBox, 'error', 'No se pudo eliminar la inversión.');
        return;
      }

      await loadInvestments();
      showAlert(alertBox, 'success', 'Inversión eliminada.');
    } catch (error) {
      console.error(error);
      showAlert(alertBox, 'error', 'No se pudo eliminar la inversión.');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btnNewInv').addEventListener('click', openNew);
    document.getElementById('invSave').addEventListener('click', saveInvestment);
    document.getElementById('invCancel').addEventListener('click', () => { closeModal(); resetModal(); });
    document.getElementById('invClose').addEventListener('click', () => { closeModal(); resetModal(); });
    document.getElementById('invModal').addEventListener('click', (event) => {
      if (event.target.dataset.close) {
        closeModal();
        resetModal();
      }
    });

    renderInvestments();
    loadInvestments();
  });
</script>
@endpush
