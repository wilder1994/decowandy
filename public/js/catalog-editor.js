/* public/js/catalog-editor.js */
(function () {
  const $ = (id) => document.getElementById(id);

  const st = {
    activeCat: (window.CATALOG && window.CATALOG.defaultCategory) || 'Papelería',
    list: [],
    editing: null,
    file: null
  };

  // utils
  const esc = (s) => (s || '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
  const onlyDigits = (s) => (s || '').replace(/[^\d]/g, '');
  const toInt = (s) => {
    const d = onlyDigits(s);
    return d ? parseInt(d, 10) : 0;
  };
  const fmtCOP = (n) => String(Math.max(0, Number(n || 0))).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  function headers(extra = {}) {
    return {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': (window.CATALOG && window.CATALOG.csrf) || '',
      ...extra
    };
  }

  async function fetchJSON(url, opts = {}) {
    const res = await fetch(url, opts);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  }

  // tabs
  function initTabs() {
    const tabs = Array.from(document.querySelectorAll('.tab-btn'));
    const setActive = (cat) => {
      st.activeCat = cat;
      tabs.forEach((b) => {
        b.dataset.active = b.dataset.cat === cat ? 'true' : 'false';
      });
      loadList();
    };
    tabs.forEach((b) => b.addEventListener('click', () => setActive(b.dataset.cat)));
    setActive(st.activeCat);
  }

  // list
  async function loadList() {
    const url = `${CATALOG.routes.index}?category=${encodeURIComponent(st.activeCat)}`;
    try {
      const data = await fetchJSON(url, { headers: headers() });
      st.list = data.items || [];
      renderGrid();
      await refreshPreview();
    } catch (e) {
      console.error(e);
      alert('No fue posible cargar los ítems.');
    }
  }

  async function refreshPreview() {
    const card = $('previewCard');
    const list = $('previewList');
    if (!card || !list || !CATALOG.routes.preview) return;

    const url = `${CATALOG.routes.preview}?category=${encodeURIComponent(st.activeCat)}`;

    try {
      const res = await fetch(url, { headers: headers({ Accept: 'application/json' }) });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      if (data.card) card.innerHTML = data.card;
      if (data.list) list.innerHTML = data.list;
    } catch (e) {
      console.error(e);
    }
  }

  function cardHTML(x) {
    const price = x.show_price && x.price ? '$ ' + fmtCOP(x.price) : '<span class="text-dw-muted">$ —</span>';
    const badge =
      x.category === 'Papelería'
        ? 'dw-badge-primary'
        : x.category === 'Impresión'
        ? 'dw-badge-danger'
        : 'dw-badge-warning';

    return `
      <div class="dw-card flex flex-col p-4">
        <div class="mb-3 flex h-36 items-center justify-center overflow-hidden rounded-dw bg-dw-lilac-soft">
          ${x.image_path ? `<img src="${esc(x.image_path)}" class="max-h-36 object-contain">` : `<div class="text-sm text-dw-muted">Sin imagen</div>`}
        </div>
        <div class="font-semibold text-dw-text">${esc(x.title)}</div>
        <div class="mt-0.5 text-sm text-dw-muted">${esc(x.description || '')}</div>
        <div class="mt-2 text-[15px] text-dw-text">${price} ${x.featured ? '<span class="dw-badge-warning ml-2">Destacado</span>' : ''}</div>
        <div class="mt-3 flex items-center justify-between">
          <div class="flex items-center gap-3 text-dw-muted">
            <button class="act moveUp hover:text-dw-primary" data-id="${x.id}" title="Subir orden">▲</button>
            <button class="act edit hover:text-dw-primary" data-id="${x.id}" title="Editar">✏️</button>
            <button class="act del text-dw-rose hover:underline" data-id="${x.id}" title="Eliminar">🗑️</button>
          </div>
          <span class="${badge}">${x.category}</span>
        </div>
      </div>
    `;
  }

  function renderGrid() {
    const grid = $('cardsGrid');
    grid.innerHTML = '';
    if (!st.list.length) {
      grid.innerHTML = `<div class="col-span-full py-10 text-center text-dw-muted">Sin ítems en esta categoría.</div>`;
      return;
    }
    grid.innerHTML = st.list.map(cardHTML).join('');

    grid.querySelectorAll('.edit').forEach((b) => b.addEventListener('click', () => openEdit(+b.dataset.id)));
    grid.querySelectorAll('.del').forEach((b) => b.addEventListener('click', () => delItem(+b.dataset.id)));
    grid.querySelectorAll('.moveUp').forEach((b) => b.addEventListener('click', () => moveUp(+b.dataset.id)));
  }

  // modal
  function showModal(v) {
    $('itemModal').classList.toggle('hidden', !v);
  }

  function openNew() {
    st.editing = null;
    $('modalTitle').textContent = 'Nuevo ítem del catálogo';
    $('f_category').value = st.activeCat;
    $('f_title').value = '';
    $('f_desc').value = '';
    $('f_price').value = '0';
    $('f_showPrice').checked = true;
    $('f_visible').checked = true;
    $('f_featured').checked = false;
    $('f_image').value = '';
    $('f_preview').src = '';
    st.file = null;
    showModal(true);
  }

  function openEdit(id) {
    const it = st.list.find((x) => x.id === id);
    if (!it) return;
    st.editing = it;
    $('modalTitle').textContent = 'Editar ítem del catálogo';
    $('f_category').value = it.category;
    $('f_title').value = it.title;
    $('f_desc').value = it.description || '';
    $('f_price').value = fmtCOP(it.price || 0);
    $('f_showPrice').checked = !!it.show_price;
    $('f_visible').checked = !!it.visible;
    $('f_featured').checked = !!it.featured;
    $('f_image').value = '';
    $('f_preview').src = it.image_path || '';
    st.file = null;
    showModal(true);
  }

  async function saveItem() {
    const fd = new FormData();
    fd.append('category', $('f_category').value);
    fd.append('title', $('f_title').value.trim());
    fd.append('description', $('f_desc').value.trim());
    const price = toInt($('f_price').value);
    if (price > 0) fd.append('price', String(price));
    fd.append('show_price', $('f_showPrice').checked ? '1' : '0');
    fd.append('visible', $('f_visible').checked ? '1' : '0');
    fd.append('featured', $('f_featured').checked ? '1' : '0');
    if (st.file) fd.append('image', st.file);

    const isEdit = !!st.editing;
    const url = isEdit ? `${CATALOG.routes.update}/${st.editing.id}` : CATALOG.routes.store;

    try {
      const res = await fetchJSON(url, {
        method: 'POST',
        headers: headers({}),
        body: fd
      });
      if (!res || res.ok !== true) throw new Error('Respuesta no válida');
      showModal(false);
      await loadList();
    } catch (e) {
      console.error(e);
      alert('No se pudo guardar. Revisa los campos.');
    }
  }

  async function delItem(id) {
    if (!confirm('¿Eliminar este ítem?')) return;
    try {
      const url = `${CATALOG.routes.destroy}/${id}/delete`;
      const res = await fetchJSON(url, {
        method: 'POST',
        headers: headers()
      });
      if (!res.ok) throw new Error();
      await loadList();
    } catch (e) {
      console.error(e);
      alert('No se pudo eliminar.');
    }
  }

  async function moveUp(id) {
    const idx = st.list.findIndex((x) => x.id === id);
    if (idx <= 0) return;
    const arr = [...st.list];
    [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]];
    st.list = arr;
    renderGrid(); // feedback

    try {
      const ids = st.list.map((x) => x.id);
      await fetchJSON(CATALOG.routes.sort, {
        method: 'POST',
        headers: headers({ 'Content-Type': 'application/json' }),
        body: JSON.stringify({ category: st.activeCat, ids })
      });
      await refreshPreview();
    } catch (e) {
      console.error(e);
      alert('No se pudo guardar el nuevo orden.');
      loadList();
    }
  }

  // events
  function initEvents() {
    $('btnAdd').addEventListener('click', openNew);
    $('modalClose').addEventListener('click', () => showModal(false));
    $('modalCancel').addEventListener('click', () => showModal(false));
    $('modalSave').addEventListener('click', saveItem);
    $('f_price').addEventListener('input', (e) => (e.target.value = fmtCOP(toInt(e.target.value))));
    $('btnClearImg').addEventListener('click', () => {
      $('f_image').value = '';
      $('f_preview').src = '';
      st.file = null;
    });
    $('f_image').addEventListener('change', (e) => {
      st.file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
      if (st.file) {
        const r = new FileReader();
        r.onload = (ev) => ($('f_preview').src = ev.target.result);
        r.readAsDataURL(st.file);
      }
    });
  }

  // boot
  document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initEvents();
  });
})();
