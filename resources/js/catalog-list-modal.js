/**
 * Modal de lista de productos del catálogo (selección + PDF).
 *
 * @param {object} options
 * @param {import('axios').AxiosInstance} options.axios
 * @param {() => string} options.getSector
 * @param {Record<string, string>} options.sectorLabels
 * @param {HTMLElement | null} [options.btnOpen]
 */
export function initCatalogListModal({ axios, getSector, sectorLabels, btnOpen }) {
    const modal = document.getElementById('catalogListModal');
    const btnClose = document.getElementById('catalogListClose');
    const searchInput = document.getElementById('catalogListSearch');
    const selectAll = document.getElementById('catalogListSelectAll');
    const tableBody = document.getElementById('catalogListBody');
    const countsEl = document.getElementById('catalogListCounts');
    const btnDownload = document.getElementById('catalogListDownloadPdf');

    if (!modal || !tableBody || !axios || typeof getSector !== 'function') {
        return;
    }

    const currency = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    });

    const state = {
        items: [],
        filtered: [],
        selected: new Set(),
        loading: false,
        sector: '',
    };

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.innerText = value ?? '';
        return div.innerHTML;
    }

    function formatColor(color) {
        const value = (color ?? '').trim();
        if (!value || value.toUpperCase() === 'N/A') {
            return '—';
        }
        return value;
    }

    function openModal() {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        searchInput.value = '';
        state.selected.clear();
        state.sector = getSector();
        loadItems();
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function applyFilter() {
        const term = searchInput.value.trim().toLowerCase();
        if (!term) {
            state.filtered = [...state.items];
            return;
        }

        state.filtered = state.items.filter((item) => {
            const haystack = [
                item.name,
                item.barcode,
                item.color,
                String(item.sale_price ?? ''),
            ]
                .map((part) => String(part ?? '').toLowerCase())
                .join(' ');

            return haystack.includes(term);
        });
    }

    function updateCounts() {
        const total = state.filtered.length;
        const selectedVisible = state.filtered.filter((item) => state.selected.has(item.id)).length;
        const sectorLabel = sectorLabels[state.sector] || state.sector;
        countsEl.textContent = `${sectorLabel} · ${selectedVisible} de ${total} seleccionados (${state.items.length} total)`;
        btnDownload.disabled = selectedVisible === 0 || state.loading;
        if (selectAll) {
            const allVisibleSelected = total > 0 && selectedVisible === total;
            selectAll.checked = allVisibleSelected;
            selectAll.indeterminate = selectedVisible > 0 && !allVisibleSelected;
        }
    }

    function renderRows() {
        tableBody.innerHTML = '';

        if (state.loading) {
            tableBody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-sm text-dw-muted">Cargando productos…</td></tr>';
            updateCounts();
            return;
        }

        if (!state.filtered.length) {
            tableBody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-sm text-dw-muted">Sin productos para mostrar.</td></tr>';
            updateCounts();
            return;
        }

        state.filtered.forEach((item) => {
            const tr = document.createElement('tr');
            const checked = state.selected.has(item.id) ? 'checked' : '';
            tr.innerHTML = `
                <td class="py-2 pr-2 w-10">
                    <input type="checkbox" class="catalog-list-check" data-id="${item.id}" ${checked}>
                </td>
                <td class="py-2 pr-4">${escapeHtml(item.name)}</td>
                <td class="py-2 pr-4 font-mono text-xs">${escapeHtml(item.barcode || '—')}</td>
                <td class="py-2 pr-4 text-dw-muted">${escapeHtml(formatColor(item.color))}</td>
                <td class="py-2 pr-2 text-right whitespace-nowrap">${escapeHtml(currency.format(Number(item.sale_price ?? 0)))}</td>
            `;
            tableBody.appendChild(tr);
        });

        updateCounts();
    }

    async function loadItems() {
        state.loading = true;
        renderRows();
        try {
            const { data } = await axios.get('/api/items/catalog-export', {
                params: { sector: state.sector },
            });
            state.items = Array.isArray(data.data) ? data.data : [];
            applyFilter();
            state.loading = false;
            renderRows();
        } catch (error) {
            state.items = [];
            state.filtered = [];
            state.loading = false;
            renderRows();
            if (window.dwShowToast) {
                window.dwShowToast('No se pudo cargar la lista de productos.', 'error');
            }
        }
    }

    async function downloadPdf() {
        const itemIds = state.filtered
            .filter((item) => state.selected.has(item.id))
            .map((item) => item.id);

        if (!itemIds.length) {
            return;
        }

        btnDownload.disabled = true;
        try {
            const response = await axios.post(
                '/api/items/catalog-list/pdf',
                {
                    sector: state.sector,
                    item_ids: itemIds,
                },
                { responseType: 'blob' },
            );

            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = URL.createObjectURL(blob);
            const anchor = document.createElement('a');
            anchor.href = url;
            anchor.download = 'lista-productos-decowandy.pdf';
            anchor.click();
            URL.revokeObjectURL(url);

            if (window.dwShowToast) {
                window.dwShowToast('PDF descargado.', 'success');
            }
        } catch (error) {
            if (window.dwShowToast) {
                window.dwShowToast('No se pudo generar el PDF.', 'error');
            }
        } finally {
            updateCounts();
        }
    }

    btnOpen?.addEventListener('click', openModal);
    btnClose?.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target.dataset.clClose !== undefined) {
            closeModal();
        }
    });

    searchInput?.addEventListener('input', () => {
        applyFilter();
        renderRows();
    });

    selectAll?.addEventListener('change', () => {
        if (selectAll.checked) {
            state.filtered.forEach((item) => state.selected.add(item.id));
        } else {
            state.filtered.forEach((item) => state.selected.delete(item.id));
        }
        renderRows();
    });

    tableBody.addEventListener('change', (event) => {
        const checkbox = event.target.closest('.catalog-list-check');
        if (!checkbox) {
            return;
        }
        const id = Number(checkbox.dataset.id);
        if (!Number.isFinite(id)) {
            return;
        }
        if (checkbox.checked) {
            state.selected.add(id);
        } else {
            state.selected.delete(id);
        }
        updateCounts();
    });

    btnDownload?.addEventListener('click', downloadPdf);
}
