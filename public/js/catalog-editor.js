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
    cropper: null,
    cropObjectUrl: null,
    previewObjectUrl: null,
  };

  const CROP_ASPECT = 16 / 9;

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
          ${x.image_path ? `<img src="${esc(x.image_path)}" class="h-full w-full object-cover">` : `<div class="text-sm text-dw-muted">Sin imagen</div>`}
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
      grid.innerHTML = `<div class="col-span-full py-10 text-center text-dw-muted">Sin ítems en esta categoría. Usa “Agregar al catálogo”.</div>`;
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

  function syncImageUi() {
    const preview = $('f_preview');
    const empty = $('imageDropEmpty');
    const clearBtn = $('btnClearImg');
    const hasImg = !!(preview && preview.src && !preview.classList.contains('hidden') && preview.getAttribute('src'));

    if (hasImg && preview.src) {
      preview.classList.remove('hidden');
      if (empty) empty.classList.add('hidden');
      if (clearBtn) clearBtn.classList.remove('hidden');
    } else {
      if (preview) {
        preview.classList.add('hidden');
        preview.removeAttribute('src');
      }
      if (empty) empty.classList.remove('hidden');
      if (clearBtn) clearBtn.classList.add('hidden');
    }
  }

  function setImagePreview(url) {
    const preview = $('f_preview');
    if (!preview) return;
    if (st.previewObjectUrl) {
      URL.revokeObjectURL(st.previewObjectUrl);
      st.previewObjectUrl = null;
    }
    if (url) {
      preview.src = url;
      preview.classList.remove('hidden');
    } else {
      preview.removeAttribute('src');
      preview.classList.add('hidden');
    }
    syncImageUi();
  }

  function clearItemImage({ markClear = false } = {}) {
    st.file = null;
    if (markClear) st.clearImage = true;
    const input = $('f_image');
    if (input) input.value = '';
    setImagePreview('');
  }

  function setSelectedFromSelect() {
    const sel = $('f_item_id');
    if (!sel || !sel.value) {
      st.selectedItem = null;
      return;
    }
    const opt = sel.selectedOptions[0];
    st.selectedItem = {
      id: +sel.value,
      name: opt.dataset.name || opt.textContent,
      sale_price: +(opt.dataset.price || 0),
      stock: +(opt.dataset.stock || 0),
    };
  }

  async function loadInventoryOptions({ keepId = null } = {}) {
    const sel = $('f_item_id');
    if (!sel) return;

    const category = $('f_category').value;
    const except = st.editing ? `&except_catalog_id=${st.editing.id}` : '';
    const url = `${CATALOG.routes.inventory}?category=${encodeURIComponent(category)}${except}`;

    sel.disabled = true;
    sel.innerHTML = `<option value="">Cargando…</option>`;

    try {
      const data = await fetchJSON(url, { headers: headers() });
      const items = data.items || [];
      let html = `<option value="">Selecciona un producto…</option>`;

      if (keepId && st.editing) {
        html += `<option value="${keepId}" data-name="${esc(st.editing.title)}" data-price="${st.editing.price || 0}" data-stock="${st.editing.stock_quantity || 0}" selected>${esc(st.editing.title)} · $ ${fmtCOP(st.editing.price)} · stock ${st.editing.stock_quantity || 0}</option>`;
      }

      items.forEach((it) => {
        if (keepId && +it.id === +keepId) return;
        html += `<option value="${it.id}" data-name="${esc(it.name)}" data-price="${it.sale_price}" data-stock="${it.stock}">${esc(it.name)} · $ ${fmtCOP(it.sale_price)} · stock ${it.stock}</option>`;
      });

      sel.innerHTML = html;
      if (keepId) sel.value = String(keepId);
      setSelectedFromSelect();
    } catch (e) {
      console.error(e);
      sel.innerHTML = `<option value="">No se pudo cargar el inventario</option>`;
      st.selectedItem = null;
    } finally {
      sel.disabled = !!st.editing;
    }
  }

  function openNew() {
    st.editing = null;
    st.clearImage = false;
    $('modalTitle').textContent = 'Agregar al catálogo';
    $('f_category').value = st.activeCat;
    $('f_category').disabled = false;
    $('f_item_id').disabled = false;
    if ($('f_item_hint')) $('f_item_hint').classList.remove('hidden');
    $('f_desc').value = '';
    $('f_showPrice').checked = true;
    $('f_visible').checked = true;
    $('f_featured').checked = false;
    clearItemImage();
    showModal(true);
    loadInventoryOptions();
  }

  function openEdit(id) {
    const it = st.list.find((x) => x.id === id);
    if (!it) return;
    st.editing = it;
    st.clearImage = false;
    $('modalTitle').textContent = 'Editar ítem del catálogo';
    $('f_category').value = it.category;
    $('f_category').disabled = true;
    if ($('f_item_hint')) $('f_item_hint').classList.add('hidden');
    $('f_desc').value = it.description || '';
    $('f_showPrice').checked = !!it.show_price;
    $('f_visible').checked = !!it.visible;
    $('f_featured').checked = !!it.featured;
    clearItemImage();
    if (it.image_path) setImagePreview(it.image_path);
    showModal(true);
    loadInventoryOptions({ keepId: it.item_id });
  }

  function destroyCropper() {
    if (st.cropper) {
      st.cropper.destroy();
      st.cropper = null;
    }
    if (st.cropObjectUrl) {
      URL.revokeObjectURL(st.cropObjectUrl);
      st.cropObjectUrl = null;
    }
  }

  function showCropModal(v) {
    $('cropModal').classList.toggle('hidden', !v);
    if (!v) destroyCropper();
  }

  function openCropperWithFile(file) {
    if (!file || !file.type || !file.type.startsWith('image/')) {
      alert('Selecciona una imagen válida (JPG, PNG o WebP).');
      return;
    }
    if (typeof Cropper === 'undefined') {
      alert('No se pudo cargar el editor de imagen. Recarga la página.');
      return;
    }

    destroyCropper();
    st.cropObjectUrl = URL.createObjectURL(file);
    const img = $('cropImage');
    showCropModal(true);

    const startCropper = () => {
      if (st.cropper) {
        st.cropper.destroy();
        st.cropper = null;
      }
      st.cropper = new Cropper(img, {
        aspectRatio: CROP_ASPECT,
        viewMode: 1,
        autoCropArea: 1,
        responsive: true,
        background: false,
        movable: true,
        zoomable: true,
        rotatable: false,
        scalable: false,
      });
    };

    img.onload = startCropper;
    img.src = st.cropObjectUrl;
    if (img.complete) startCropper();
  }

  function applyCrop() {
    if (!st.cropper) return;
    const canvas = st.cropper.getCroppedCanvas({
      maxWidth: 1600,
      maxHeight: 900,
      imageSmoothingEnabled: true,
      imageSmoothingQuality: 'high',
    });
    if (!canvas) {
      alert('No se pudo generar el recorte.');
      return;
    }
    canvas.toBlob((blob) => {
      if (!blob) {
        alert('No se pudo generar el recorte.');
        return;
      }
      st.file = new File([blob], 'catalog-item.jpg', { type: 'image/jpeg' });
      st.clearImage = false;
      if (st.previewObjectUrl) {
        URL.revokeObjectURL(st.previewObjectUrl);
        st.previewObjectUrl = null;
      }
      const previewUrl = URL.createObjectURL(blob);
      st.previewObjectUrl = previewUrl;
      const preview = $('f_preview');
      if (preview) {
        preview.src = previewUrl;
        preview.classList.remove('hidden');
      }
      syncImageUi();
      showCropModal(false);
      const input = $('f_image');
      if (input) input.value = '';
    }, 'image/jpeg', 0.9);
  }

  async function saveItem() {
    setSelectedFromSelect();
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

  function initImageDropzone() {
    const zone = $('imageDropzone');
    const input = $('f_image');
    if (!zone || !input) return;

    const openPicker = () => {
      if (st.editing && $('f_preview')?.src && !st.file) {
        // allow replacing existing image
      }
      input.click();
    };

    zone.addEventListener('click', (e) => {
      if (e.target.closest('#btnClearImg')) return;
      openPicker();
    });

    zone.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openPicker();
      }
    });

    zone.addEventListener('dragover', (e) => {
      e.preventDefault();
      zone.classList.add('border-dw-primary', 'bg-dw-lilac-soft');
    });
    zone.addEventListener('dragleave', () => {
      zone.classList.remove('border-dw-primary', 'bg-dw-lilac-soft');
    });
    zone.addEventListener('drop', (e) => {
      e.preventDefault();
      zone.classList.remove('border-dw-primary', 'bg-dw-lilac-soft');
      const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
      if (file) openCropperWithFile(file);
    });

    input.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (file) openCropperWithFile(file);
      e.target.value = '';
    });

    document.addEventListener('paste', (e) => {
      if ($('itemModal').classList.contains('hidden')) return;
      const items = e.clipboardData && e.clipboardData.items;
      if (!items) return;
      for (const item of items) {
        if (item.type && item.type.startsWith('image/')) {
          const file = item.getAsFile();
          if (file) {
            e.preventDefault();
            openCropperWithFile(file);
          }
          break;
        }
      }
    });
  }

  function initEvents() {
    $('btnAdd').addEventListener('click', openNew);
    $('modalClose').addEventListener('click', () => showModal(false));
    $('modalCancel').addEventListener('click', () => showModal(false));
    document.querySelectorAll('[data-close-item-modal]').forEach((el) => {
      el.addEventListener('click', () => showModal(false));
    });
    $('modalSave').addEventListener('click', saveItem);

    $('coverInput')?.addEventListener('change', (e) => {
      const file = e.target.files && e.target.files[0];
      if (file) uploadCover(file);
      e.target.value = '';
    });
    $('btnClearCover')?.addEventListener('click', clearCover);

    $('btnClearImg').addEventListener('click', (e) => {
      e.stopPropagation();
      clearItemImage({ markClear: true });
    });

    $('f_item_id').addEventListener('change', setSelectedFromSelect);

    $('f_category').addEventListener('change', () => {
      if (!st.editing) {
        st.selectedItem = null;
        loadInventoryOptions();
      }
    });

    $('cropClose').addEventListener('click', () => showCropModal(false));
    $('cropCancel').addEventListener('click', () => showCropModal(false));
    $('cropApply').addEventListener('click', applyCrop);

    initImageDropzone();
  }

  document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initEvents();
  });
})();
