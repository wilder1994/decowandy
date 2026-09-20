/* public/js/catalog-editor.js */
(function () {
  const $ = (id) => document.getElementById(id);

  const st = {
    activeCat: (window.CATALOG && window.CATALOG.defaultCategory) || 'Papelería',
    activeSlug: (window.CATALOG && window.CATALOG.defaultSlug) || 'papeleria',
    list: [],
    editing: null,
    file: null,
    clearImage: false,
    selectedItem: null,
    searchTimer: null,
  };

  const esc = (s) => (s || '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
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
    if (!res.ok) {
      let msg = 'HTTP ' + res.status;
      try {
        const body = await res.json();
        if (body && body.message) msg = body.message;
        if (body && body.errors) {
          msg = Object.values(body.errors).flat().join(' ');
        }
      } catch (_) {}
      throw new Error(msg);
    }
    return res.json();
  }

  function initTabs() {
    const tabs = Array.from(document.querySelectorAll('.tab-btn'));
    const setActive = (btn) => {
      st.activeCat = btn.dataset.cat;
      st.activeSlug = btn.dataset.slug;
      tabs.forEach((b) => {
        b.dataset.active = b.dataset.cat === st.activeCat ? 'true' : 'false';
      });
      renderCover();
      loadList();
    };
    tabs.forEach((b) => b.addEventListener('click', () => setActive(b)));
    const initial = tabs.find((b) => b.dataset.cat === st.activeCat) || tabs[0];
    if (initial) setActive(initial);
  }

  function titleCaseSlug(slug) {
    return (slug || '')
      .split('-')
      .map((p) => (p ? p.charAt(0).toUpperCase() + p.slice(1) : ''))
      .join(' ');
  }

  function renderCover() {
    const box = $('coverPreview');
    if (!box) return;

    const meta = (CATALOG.categories && CATALOG.categories[st.activeSlug]) || {
      name: st.activeCat,
      slug: st.activeSlug,
      cta_label: 'Ver más',
      tag_empty: 'Sin productos',
      card_background: null,
    };
    const url = (CATALOG.covers && CATALOG.covers[st.activeSlug]) || '';

    if ($('coverCtaLabel')) $('coverCtaLabel').textContent = meta.cta_label || 'Ver más';
    if ($('coverName')) $('coverName').textContent = meta.name || st.activeCat;
    if ($('coverSlug')) $('coverSlug').textContent = titleCaseSlug(meta.slug || st.activeSlug);
    if ($('coverEmpty')) {
      $('coverEmpty').textContent = st.list.length
        ? `${st.list.length} producto(s) publicados`
        : (meta.tag_empty || 'Sin productos');
    }

    const existingImg = box.querySelector('img.cover-img');
    const emptyHint = $('coverEmptyHint');

    if (url) {
      box.style.background = '';
      if (emptyHint) emptyHint.classList.add('hidden');
      if (existingImg) {
        existingImg.src = url;
      } else {
        const img = document.createElement('img');
        img.src = url;
        img.alt = meta.name || 'Portada';
        img.className = 'cover-img absolute inset-0 h-full w-full object-cover';
        box.appendChild(img);
      }
    } else {
      if (existingImg) existingImg.remove();
      if (emptyHint) emptyHint.classList.remove('hidden');
      box.style.background = meta.card_background || '';
    }
  }

  async function uploadCover(file) {
    const fd = new FormData();
    fd.append('cover', file);
    try {
      const data = await fetchJSON(`${CATALOG.routes.coverUpdate}/${encodeURIComponent(st.activeSlug)}/cover`, {
        method: 'POST',
        headers: headers(),
        body: fd
      });
      CATALOG.covers = CATALOG.covers || {};
      CATALOG.covers[st.activeSlug] = data.cover_image || '';
      renderCover();
    } catch (e) {
      console.error(e);
      alert(e.message || 'No se pudo subir la portada.');
    }
  }

  async function clearCover() {
    if (!confirm('¿Quitar la portada de esta categoría?')) return;
    try {
      await fetchJSON(`${CATALOG.routes.coverDestroy}/${encodeURIComponent(st.activeSlug)}/cover`, {
        method: 'DELETE',
        headers: headers()
      });
      CATALOG.covers = CATALOG.covers || {};
      CATALOG.covers[st.activeSlug] = null;
      renderCover();
    } catch (e) {
      console.error(e);
      alert('No se pudo quitar la portada.');
    }
  }

  async function loadList() {
    const url = `${CATALOG.routes.index}?category=${encodeURIComponent(st.activeCat)}`;
    try {
      const data = await fetchJSON(url, { headers: headers() });
      st.list = data.items || [];
      renderGrid();
      renderCover();
    } catch (e) {
      console.error(e);
      alert('No fue posible cargar los productos.');
    }
  }

  function cardHTML(x) {
    const price = x.show_price && x.price ? '$ ' + fmtCOP(x.price) : '<span class="text-dw-muted">$ —</span>';
    const stock = typeof x.stock_quantity === 'number' ? x.stock_quantity : 0;
    const stockLabel = stock > 0
      ? `<span class="text-xs text-emerald-700">${stock} disponibles</span>`
      : `<span class="text-xs text-dw-rose">Agotado</span>`;

    return `
      <div class="dw-card flex flex-col p-4">
        <div class="mb-3 flex h-36 items-center justify-center overflow-hidden rounded-dw bg-dw-lilac-soft">
          ${x.image_path ? `<img src="${esc(x.image_path)}" class="max-h-36 object-contain">` : `<div class="text-sm text-dw-muted">Sin imagen</div>`}
        </div>
        <div class="font-semibold text-dw-text">${esc(x.title)}</div>
        <div class="mt-0.5 text-sm text-dw-muted">${esc(x.description || '')}</div>
        <div class="mt-2 flex items-center justify-between gap-2 text-[15px] text-dw-text">
          <span>${price} ${x.featured ? '<span class="dw-badge-warning ml-1">Destacado</span>' : ''}</span>
          ${stockLabel}
        </div>
        <div class="mt-3 flex items-center justify-between">
          <div class="flex items-center gap-3 text-dw-muted">
            <button class="act moveUp hover:text-dw-primary" data-id="${x.id}" title="Subir orden">▲</button>
            <button class="act edit hover:text-dw-primary" data-id="${x.id}" title="Editar">✏️</button>
            <button class="act del text-dw-rose hover:underline" data-id="${x.id}" title="Eliminar">🗑️</button>
          </div>
          <span class="dw-badge-primary">${esc(x.category)}</span>
        </div>
      </div>
    `;
  }

  function renderGrid() {
    const grid = $('cardsGrid');
    grid.innerHTML = '';
    if (!st.list.length) {
      grid.innerHTML = `<div class="col-span-full py-10 text-center text-dw-muted">Sin productos publicados en esta categoría. Usa “Agregar producto”.</div>`;
      return;
    }
    grid.innerHTML = st.list.map(cardHTML).join('');
    grid.querySelectorAll('.edit').forEach((b) => b.addEventListener('click', () => openEdit(+b.dataset.id)));
    grid.querySelectorAll('.del').forEach((b) => b.addEventListener('click', () => delItem(+b.dataset.id)));
    grid.querySelectorAll('.moveUp').forEach((b) => b.addEventListener('click', () => moveUp(+b.dataset.id)));
  }

  function showModal(v) {
    $('itemModal').classList.toggle('hidden', !v);
  }

  function setSelectedItem(item) {
    st.selectedItem = item;
    $('f_item_id').value = item ? String(item.id) : '';
    const box = $('f_item_selected');
    if (!item) {
      box.classList.add('hidden');
      box.textContent = '';
      return;
    }
    box.classList.remove('hidden');
    box.textContent = `${item.name} · $ ${fmtCOP(item.sale_price)} · stock ${item.stock}`;
  }

  function openNew() {
    st.editing = null;
    st.clearImage = false;
    $('modalTitle').textContent = 'Agregar producto del inventario';
    $('f_category').value = st.activeCat;
    $('f_item_search').value = '';
    $('f_item_search').disabled = false;
    setSelectedItem(null);
    $('f_item_results').classList.add('hidden');
    $('f_item_results').innerHTML = '';
    $('f_desc').value = '';
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
    st.clearImage = false;
    $('modalTitle').textContent = 'Editar producto del catálogo';
    $('f_category').value = it.category;
    $('f_item_search').value = '';
    $('f_item_search').disabled = true;
    setSelectedItem({
      id: it.item_id,
      name: it.title,
      sale_price: it.price,
      stock: it.stock_quantity || 0,
    });
    $('f_item_results').classList.add('hidden');
    $('f_desc').value = it.description || '';
    $('f_showPrice').checked = !!it.show_price;
    $('f_visible').checked = !!it.visible;
    $('f_featured').checked = !!it.featured;
    $('f_image').value = '';
    $('f_preview').src = it.image_path || '';
    st.file = null;
    showModal(true);
  }

  async function searchInventory(q) {
    const results = $('f_item_results');
    if (!q || q.length < 1) {
      results.classList.add('hidden');
      results.innerHTML = '';
      return;
    }

    const except = st.editing ? `&except_catalog_id=${st.editing.id}` : '';
    const url = `${CATALOG.routes.inventory}?category=${encodeURIComponent($('f_category').value)}&q=${encodeURIComponent(q)}${except}`;
    try {
      const data = await fetchJSON(url, { headers: headers() });
      const items = data.items || [];
      if (!items.length) {
        results.innerHTML = `<div class="px-3 py-2 text-sm text-dw-muted">Sin coincidencias en inventario.</div>`;
        results.classList.remove('hidden');
        return;
      }
      results.innerHTML = items.map((it) => `
        <button type="button" class="block w-full px-3 py-2 text-left text-sm hover:bg-dw-lilac-soft" data-id="${it.id}"
          data-name="${esc(it.name)}" data-price="${it.sale_price}" data-stock="${it.stock}">
          <span class="font-medium text-dw-text">${esc(it.name)}</span>
          <span class="block text-xs text-dw-muted">$ ${fmtCOP(it.sale_price)} · ${it.stock} disp.</span>
        </button>
      `).join('');
      results.classList.remove('hidden');
      results.querySelectorAll('button').forEach((btn) => {
        btn.addEventListener('click', () => {
          setSelectedItem({
            id: +btn.dataset.id,
            name: btn.dataset.name,
            sale_price: +btn.dataset.price,
            stock: +btn.dataset.stock,
          });
          $('f_item_search').value = '';
          results.classList.add('hidden');
        });
      });
    } catch (e) {
      console.error(e);
    }
  }

  async function saveItem() {
    if (!st.selectedItem || !st.selectedItem.id) {
      alert('Selecciona un producto del inventario.');
      return;
    }

    const fd = new FormData();
    fd.append('category', $('f_category').value);
    fd.append('item_id', String(st.selectedItem.id));
    fd.append('description', $('f_desc').value.trim());
    fd.append('show_price', $('f_showPrice').checked ? '1' : '0');
    fd.append('visible', $('f_visible').checked ? '1' : '0');
    fd.append('featured', $('f_featured').checked ? '1' : '0');
    if (st.file) fd.append('image', st.file);
    if (st.clearImage) fd.append('clear_image', '1');

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
      alert(e.message || 'No se pudo guardar. Revisa los campos.');
    }
  }

  async function delItem(id) {
    if (!confirm('¿Quitar este producto del catálogo público?')) return;
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
    renderGrid();

    try {
      const ids = st.list.map((x) => x.id);
      await fetchJSON(CATALOG.routes.sort, {
        method: 'POST',
        headers: headers({ 'Content-Type': 'application/json' }),
        body: JSON.stringify({ category: st.activeCat, ids })
      });
      renderCover();
    } catch (e) {
      console.error(e);
      alert('No se pudo guardar el nuevo orden.');
      loadList();
    }
  }

  function initEvents() {
    $('btnAdd').addEventListener('click', openNew);
    $('modalClose').addEventListener('click', () => showModal(false));
    $('modalCancel').addEventListener('click', () => showModal(false));
    $('modalSave').addEventListener('click', saveItem);

    $('coverInput')?.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (file) uploadCover(file);
      e.target.value = '';
    });
    $('btnClearCover')?.addEventListener('click', clearCover);

    $('btnClearImg').addEventListener('click', () => {
      $('f_image').value = '';
      $('f_preview').src = '';
      st.file = null;
      st.clearImage = true;
    });
    $('f_image').addEventListener('change', (e) => {
      st.file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
      st.clearImage = false;
      if (st.file) {
        const r = new FileReader();
        r.onload = (ev) => ($('f_preview').src = ev.target.result);
        r.readAsDataURL(st.file);
      }
    });

    $('f_item_search').addEventListener('input', (e) => {
      clearTimeout(st.searchTimer);
      st.searchTimer = setTimeout(() => searchInventory(e.target.value.trim()), 250);
    });

    $('f_category').addEventListener('change', () => {
      if (!st.editing) {
        setSelectedItem(null);
        $('f_item_search').value = '';
        $('f_item_results').classList.add('hidden');
      }
    });

    document.addEventListener('click', (e) => {
      const results = $('f_item_results');
      const search = $('f_item_search');
      if (!results || !search) return;
      if (!results.contains(e.target) && e.target !== search) {
        results.classList.add('hidden');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initEvents();
  });
})();
