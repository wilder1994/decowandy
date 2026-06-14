{{-- resources/views/finance/investments.blade.php
     Inversiones — CRUD real sobre la tabla investments
--}}
@extends('layouts.admin')

@section('title','Inversiones — DecoWandy')

@section('content')
  <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <x-dw-page-header title="Inversiones" subtitle="Registra inversión inicial y nuevas inyecciones de capital." />
    <x-dw-button id="btnNewInv" type="button">
      <span class="material-symbols-outlined text-base">add</span>
      Nueva inversión
    </x-dw-button>
  </div>

  <div class="mb-6 grid gap-4 sm:grid-cols-2">
    <x-dw-card hover class="p-3.5">
      <div class="flex items-center justify-between gap-2">
        <span class="text-xs font-medium uppercase tracking-wide text-dw-muted">Invertido acumulado</span>
        <span class="material-symbols-outlined text-base text-dw-lilac">savings</span>
      </div>
      <div id="inv_total" class="mt-1.5 font-display text-xl font-bold text-dw-text">${{ number_format($total, 0, ',', '.') }}</div>
      <div class="mt-1 text-xs text-dw-muted">Datos guardados en la base.</div>
    </x-dw-card>
    <x-dw-card hover class="p-3.5">
      <div class="flex items-center justify-between gap-2">
        <span class="text-xs font-medium uppercase tracking-wide text-dw-muted">Última actualización</span>
        <span class="material-symbols-outlined text-base text-dw-yellow">update</span>
      </div>
      <div id="inv_last_update" class="mt-1.5 font-display text-xl font-bold text-dw-text">{{ optional($investments->first())->date?->format('d/m/Y') ?? '-' }}</div>
      <div class="mt-1 text-xs text-dw-muted">Ordenado por fecha y creación.</div>
    </x-dw-card>
  </div>

  <x-dw-card padding="p-0" class="overflow-hidden">
    <div class="overflow-x-auto px-4 py-3">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Fecha</th>
            <th class="py-2 pr-4">Concepto</th>
            <th class="py-2 pr-4">Monto (COP)</th>
            <th class="py-2 pr-4">Notas</th>
            <th class="py-2 w-28 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody id="invTable"></tbody>
      </table>
    </div>
    <div id="invAlert" class="mx-4 mb-4 hidden rounded-dw border-hairline px-3 py-2 text-sm"></div>
  </x-dw-card>

  <div id="invModal" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close="1"></div>
    <div class="relative mx-auto mt-16 w-[min(680px,92%)] rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
      <div class="mb-3 flex items-center justify-between">
        <h2 id="invTitle" class="font-display text-xl font-semibold text-dw-text">Nueva inversión</h2>
        <button id="invClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>

      <div id="invModalAlert" class="mb-3 hidden rounded-dw border-hairline px-3 py-2 text-sm"></div>

      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="dw-label mb-1" for="f_date">Fecha</label>
          <input id="f_date" type="date" class="dw-input">
        </div>
        <div>
          <label class="dw-label mb-1" for="f_concept">Concepto</label>
          <input id="f_concept" type="text" class="dw-input" placeholder="Computador, impresora...">
        </div>
        <div>
          <label class="dw-label mb-1" for="f_amount">Monto (COP)</label>
          <input id="f_amount" type="text" inputmode="numeric" class="dw-input" placeholder="0">
        </div>
        <div>
          <label class="dw-label mb-1" for="f_notes">Notas (opcional)</label>
          <input id="f_notes" type="text" class="dw-input">
        </div>
      </div>

      <div class="mt-5 flex items-center justify-end gap-2">
        <x-dw-button id="invCancel" type="button" variant="secondary">Cancelar</x-dw-button>
        <x-dw-button id="invSave" type="button">Guardar</x-dw-button>
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

  function fmtInput(value) {
    const digits = String(value ?? '').replace(/\D/g, '');
    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function showAlert(box, type, message) {
    box.textContent = message;
    box.classList.remove('hidden', 'dw-alert-success', 'dw-alert-error');
    box.classList.add(type === 'error' ? 'dw-alert-error' : 'dw-alert-success');
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

  function openInvModal() {
    document.getElementById('invModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }

  function closeInvModal() {
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
        <td class="py-2 pr-4 text-dw-text">${formatDate(item.date)}</td>
        <td class="py-2 pr-4 font-medium text-dw-text">${escapeHtml(item.concept)}</td>
        <td class="py-2 pr-4 font-semibold text-dw-text">$ ${fmt(item.amount)}</td>
        <td class="py-2 pr-4 text-dw-muted">${escapeHtml(item.note || '')}</td>
        <td class="py-2 pr-2 text-right">
          <button class="dw-link mr-2" data-edit="${item.id}">Editar</button>
          <button class="text-xs font-semibold text-dw-rose hover:underline" data-del="${item.id}">Eliminar</button>
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
    openInvModal();
  }

  function openEdit(id) {
    const item = investmentsState.list.find(x => Number(x.id) === Number(id));
    if (!item) return;
    investmentsState.editingId = item.id;
    document.getElementById('invTitle').textContent = 'Editar inversión';
    document.getElementById('f_date').value = item.date;
    document.getElementById('f_concept').value = item.concept;
    document.getElementById('f_amount').value = fmtInput(item.amount);
    document.getElementById('f_notes').value = item.note || '';
    document.getElementById('invModalAlert').classList.add('hidden');
    openInvModal();
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
    const rawAmount = document.getElementById('f_amount').value || '';
    return {
      date: document.getElementById('f_date').value,
      concept: document.getElementById('f_concept').value.trim(),
      amount: Number((rawAmount.match(/\d+/g) || []).join('')) || 0,
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
      closeInvModal();
      resetModal();
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
    document.getElementById('invCancel').addEventListener('click', () => { closeInvModal(); resetModal(); });
    document.getElementById('invClose').addEventListener('click', () => { closeInvModal(); resetModal(); });
    document.getElementById('f_amount').addEventListener('input', (e) => {
      const cursor = e.target.selectionStart;
      const formatted = fmtInput(e.target.value);
      e.target.value = formatted;
      e.target.setSelectionRange(cursor, cursor);
    });
    document.getElementById('invModal').addEventListener('click', (event) => {
      if (event.target.dataset.close) {
        closeInvModal();
        resetModal();
      }
    });

    renderInvestments();
    loadInvestments();
  });
</script>
@endpush
