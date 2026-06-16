const MAX_LABELS = 200;

/** @type {Promise<{ getDocument: typeof import('pdfjs-dist').getDocument }> | null} */
let pdfjsReady = null;

function ensurePdfJs() {
    if (!pdfjsReady) {
        pdfjsReady = (async () => {
            const [pdfjs, workerModule] = await Promise.all([
                import('pdfjs-dist'),
                import('pdfjs-dist/build/pdf.worker.min.mjs?worker'),
            ]);

            const WorkerClass = workerModule.default;
            pdfjs.GlobalWorkerOptions.workerPort = new WorkerClass();

            return pdfjs;
        })();
    }

    return pdfjsReady;
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function toast(message, type = 'info') {
    if (window.dwShowToast) {
        window.dwShowToast(message, type);
    }
}

export function initLabelPrintWizard() {
    const pickerModal = document.getElementById('labelPickerModal');
    const previewModal = document.getElementById('labelPreviewModal');
    if (!pickerModal || !previewModal) {
        return;
    }

    const searchInput = document.getElementById('labelPickerSearch');
    const gridBody = document.getElementById('labelPickerGridBody');
    const gridWrap = pickerModal.querySelector('.dw-label-wizard__grid-wrap');
    const emptyState = document.getElementById('labelPickerEmpty');
    const summaryEl = document.getElementById('labelPickerSummary');
    const clearBtn = document.getElementById('labelPickerClear');
    const selectAllInput = document.getElementById('labelPickerSelectAll');
    const previewBtn = document.getElementById('labelPickerPreview');
    const previewFrame = document.getElementById('labelPreviewFrame');
    const previewCanvasHost = document.getElementById('labelPreviewCanvasHost');
    const previewStage = previewModal.querySelector('.dw-label-preview-stage');
    const previewLoading = document.getElementById('labelPreviewLoading');
    const previewMeta = document.getElementById('labelPreviewMeta');
    const previewBack = document.getElementById('labelPreviewBack');
    const previewDownload = document.getElementById('labelPreviewDownload');
    const previewPrint = document.getElementById('labelPreviewPrint');

    /** @type {Map<number, { id: number, name: string, barcode: string, checked: boolean, quantity: number }>} */
    const rowState = new Map();
    /** @type {Array<{ id: number, name: string, barcode: string }>} */
    let visibleItems = [];
    let searchTimer = null;
    let previewBlobUrl = null;
    /** @type {ArrayBuffer | null} */
    let previewPdfBytes = null;
    let previewRenderGen = 0;
    let previewResizeTimer = null;

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value ?? '';
        return div.innerHTML;
    }

    function ensureRow(item) {
        if (!rowState.has(item.id)) {
            rowState.set(item.id, {
                id: item.id,
                name: item.name,
                barcode: item.barcode,
                checked: false,
                quantity: 1,
            });
        } else {
            const row = rowState.get(item.id);
            row.name = item.name;
            row.barcode = item.barcode;
        }

        return rowState.get(item.id);
    }

    function totalLabels() {
        let total = 0;
        rowState.forEach((row) => {
            if (row.checked) {
                total += row.quantity;
            }
        });
        return total;
    }

    function selectedCount() {
        let count = 0;
        rowState.forEach((row) => {
            if (row.checked) {
                count += 1;
            }
        });
        return count;
    }

    function linesPayload() {
        return Array.from(rowState.values())
            .filter((row) => row.checked && row.quantity > 0)
            .map((row) => ({
                item_id: row.id,
                quantity: row.quantity,
            }));
    }

    function revokePreviewUrl() {
        if (previewBlobUrl) {
            URL.revokeObjectURL(previewBlobUrl);
            previewBlobUrl = null;
        }
    }

    function updateSummary() {
        const products = selectedCount();
        const labels = totalLabels();
        summaryEl.textContent = `${products} producto${products === 1 ? '' : 's'} · ${labels} etiqueta${labels === 1 ? '' : 's'}`;
        clearBtn.classList.toggle('hidden', products === 0);
        previewBtn.disabled = labels === 0 || labels > MAX_LABELS;
        updateSelectAllState();
    }

    function updateSelectAllState() {
        if (!selectAllInput || visibleItems.length === 0) {
            if (selectAllInput) {
                selectAllInput.checked = false;
                selectAllInput.indeterminate = false;
            }
            return;
        }

        const checkedVisible = visibleItems.filter((item) => rowState.get(item.id)?.checked).length;
        selectAllInput.checked = checkedVisible === visibleItems.length;
        selectAllInput.indeterminate = checkedVisible > 0 && checkedVisible < visibleItems.length;
    }

    function renderGrid() {
        gridBody.innerHTML = '';
        const hasItems = visibleItems.length > 0;
        gridWrap?.querySelector('.dw-label-wizard__grid')?.classList.toggle('hidden', !hasItems);
        emptyState?.classList.toggle('hidden', hasItems);

        visibleItems.forEach((item) => {
            const row = ensureRow(item);
            const tr = document.createElement('tr');
            tr.className = 'border-t border-dw-border/60';
            tr.innerHTML = `
                <td class="px-2 py-2 text-center align-middle">
                    <input type="checkbox" class="label-picker-check rounded border-dw-border text-dw-primary focus:ring-dw-primary" data-id="${row.id}" ${row.checked ? 'checked' : ''} aria-label="Seleccionar ${escapeHtml(row.name)}">
                </td>
                <td class="px-2 py-2 align-middle">
                    <div class="truncate text-sm font-medium text-dw-text">${escapeHtml(row.name)}</div>
                    <div class="truncate font-mono text-[10px] text-dw-muted">${escapeHtml(row.barcode)}</div>
                </td>
                <td class="px-2 py-2 text-center align-middle">
                    <input type="number" min="1" max="999" class="label-picker-qty dw-input w-16 text-center text-sm tabular-nums" data-id="${row.id}" value="${row.quantity}" ${row.checked ? '' : 'disabled'}>
                </td>
            `;
            gridBody.appendChild(tr);
        });

        updateSummary();
    }

    function setRowChecked(id, checked) {
        const row = rowState.get(id);
        if (!row) {
            return;
        }
        row.checked = checked;
        if (checked && row.quantity < 1) {
            row.quantity = 1;
        }
        renderGrid();
    }

    function adjustQuantity(id, nextQty) {
        const row = rowState.get(id);
        if (!row || !row.checked) {
            return;
        }

        const prev = row.quantity;
        row.quantity = Math.max(1, Math.min(999, nextQty));

        const prevTotal = totalLabels();
        if (totalLabels() > MAX_LABELS) {
            row.quantity = prev;
            toast(`Máximo ${MAX_LABELS} etiquetas por hoja.`, 'error');
        }

        updateSummary();
        const qtyInput = gridBody.querySelector(`.label-picker-qty[data-id="${id}"]`);
        if (qtyInput) {
            qtyInput.value = String(row.quantity);
        }
    }

    async function fetchCandidates(term) {
        const params = new URLSearchParams({ limit: '0' });
        if (term) {
            params.set('search', term);
        }

        const res = await fetch(`/api/items/labels/candidates?${params}`, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });

        if (!res.ok) {
            throw new Error('No se pudo cargar productos.');
        }

        const data = await res.json();
        return Array.isArray(data.items) ? data.items : [];
    }

    async function loadGrid(term = '') {
        try {
            visibleItems = await fetchCandidates(term);
            renderGrid();
        } catch (error) {
            toast(error.message, 'error');
            visibleItems = [];
            renderGrid();
        }
    }

    function openPicker(prefill = []) {
        rowState.clear();
        prefill.forEach((row) => {
            if (row?.id && row?.barcode) {
                rowState.set(row.id, {
                    id: row.id,
                    name: row.name ?? 'Producto',
                    barcode: row.barcode,
                    checked: true,
                    quantity: Math.max(1, Number(row.quantity) || 1),
                });
            }
        });

        searchInput.value = '';
        pickerModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        loadGrid('');
        searchInput.focus();
    }

    function closePicker() {
        pickerModal.classList.add('hidden');
        if (previewModal.classList.contains('hidden')) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function clearPreviewCanvas() {
        previewRenderGen += 1;
        previewPdfBytes = null;
        if (previewCanvasHost) {
            previewCanvasHost.innerHTML = '';
            previewCanvasHost.classList.remove('is-single-page');
        }
    }

    async function drawPage(page, scale, container) {
        const viewport = page.getViewport({ scale });
        const canvas = document.createElement('canvas');
        canvas.width = Math.floor(viewport.width);
        canvas.height = Math.floor(viewport.height);
        canvas.className = 'dw-label-preview-page';

        const context = canvas.getContext('2d');
        if (!context) {
            return;
        }

        container.appendChild(canvas);
        await page.render({ canvasContext: context, viewport }).promise;
    }

    async function renderPdfPreview(blob) {
        if (!previewCanvasHost || !previewStage) {
            return;
        }

        const gen = ++previewRenderGen;
        previewCanvasHost.innerHTML = '';
        previewCanvasHost.classList.remove('is-single-page');

        const data = await blob.arrayBuffer();
        if (gen !== previewRenderGen) {
            return;
        }

        previewPdfBytes = data.slice(0);

        const { getDocument } = await ensurePdfJs();
        const pdf = await getDocument({ data: previewPdfBytes }).promise;
        if (gen !== previewRenderGen) {
            return;
        }

        const padding = 12;
        const availW = Math.max(1, previewStage.clientWidth - padding * 2);
        const availH = Math.max(1, previewStage.clientHeight - padding * 2);
        const stack = document.createElement('div');
        stack.className = 'dw-label-preview-pages';
        previewCanvasHost.appendChild(stack);

        const pages = [];
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum += 1) {
            pages.push(await pdf.getPage(pageNum));
            if (gen !== previewRenderGen) {
                return;
            }
        }

        if (pages.length === 1) {
            const base = pages[0].getViewport({ scale: 1 });
            const scale = Math.min(availW / base.width, availH / base.height);
            previewCanvasHost.classList.add('is-single-page');
            await drawPage(pages[0], scale, stack);
            return;
        }

        const firstBase = pages[0].getViewport({ scale: 1 });
        const gap = 8;
        const totalRawHeight = pages.reduce(
            (sum, page) => sum + page.getViewport({ scale: 1 }).height,
            0,
        );
        const scale = Math.min(
            availW / firstBase.width,
            (availH - gap * (pages.length - 1)) / totalRawHeight,
        );

        for (const page of pages) {
            await drawPage(page, scale, stack);
            if (gen !== previewRenderGen) {
                return;
            }
        }
    }

    function closePreview() {
        previewModal.classList.add('hidden');
        previewFrame.src = 'about:blank';
        previewDownload.disabled = true;
        previewPrint.disabled = true;
        clearPreviewCanvas();
        revokePreviewUrl();
        if (pickerModal.classList.contains('hidden')) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    async function requestPdf(inline) {
        const res = await fetch(inline ? '/api/items/labels/preview' : '/api/items/labels/sheet', {
            method: 'POST',
            headers: {
                Accept: 'application/pdf',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            credentials: 'same-origin',
            body: JSON.stringify({ lines: linesPayload() }),
        });

        if (!res.ok) {
            let message = 'No se pudo generar el PDF.';
            try {
                const data = await res.json();
                if (data?.message) {
                    message = data.message;
                } else if (data?.errors) {
                    message = Object.values(data.errors)[0]?.[0] ?? message;
                }
            } catch {
                // ignore
            }
            throw new Error(message);
        }

        return res.blob();
    }

    async function openPreview() {
        const labels = totalLabels();
        if (labels === 0 || labels > MAX_LABELS) {
            return;
        }

        previewLoading.classList.remove('hidden');
        previewLoading.classList.add('flex');
        previewDownload.disabled = true;
        previewPrint.disabled = true;
        previewMeta.textContent = `${labels} etiqueta${labels === 1 ? '' : 's'} · carta`;
        previewModal.classList.remove('hidden');

        try {
            const blob = await requestPdf(true);
            revokePreviewUrl();
            previewBlobUrl = URL.createObjectURL(blob);
            previewFrame.src = previewBlobUrl;
            await renderPdfPreview(blob);
            previewDownload.disabled = false;
            previewPrint.disabled = false;
        } catch (error) {
            toast(error.message || 'Error al generar PDF.', 'error');
            closePreview();
        } finally {
            previewLoading.classList.add('hidden');
            previewLoading.classList.remove('flex');
        }
    }

    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        const term = searchInput.value.trim();
        searchTimer = setTimeout(() => loadGrid(term), 280);
    });

    gridBody?.addEventListener('change', (e) => {
        const checkbox = e.target.closest('.label-picker-check');
        if (checkbox) {
            setRowChecked(Number(checkbox.dataset.id), checkbox.checked);
            return;
        }

        const qtyInput = e.target.closest('.label-picker-qty');
        if (qtyInput) {
            adjustQuantity(Number(qtyInput.dataset.id), Number(qtyInput.value) || 1);
        }
    });

    selectAllInput?.addEventListener('change', () => {
        const checked = selectAllInput.checked;
        visibleItems.forEach((item) => {
            const row = ensureRow(item);
            row.checked = checked;
            if (checked && row.quantity < 1) {
                row.quantity = 1;
            }
        });
        renderGrid();
    });

    clearBtn?.addEventListener('click', () => {
        rowState.forEach((row) => {
            row.checked = false;
        });
        renderGrid();
    });

    previewBtn?.addEventListener('click', openPreview);
    previewBack?.addEventListener('click', closePreview);

    previewDownload?.addEventListener('click', async () => {
        try {
            const blob = previewBlobUrl
                ? await fetch(previewBlobUrl).then((r) => r.blob())
                : await requestPdf(false);
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'etiquetas-decowandy.pdf';
            a.click();
            URL.revokeObjectURL(url);
        } catch (error) {
            toast(error.message || 'No se pudo descargar.', 'error');
        }
    });

    previewPrint?.addEventListener('click', () => {
        try {
            previewFrame.contentWindow?.focus();
            previewFrame.contentWindow?.print();
        } catch {
            toast('No se pudo abrir el diálogo de impresión.', 'error');
        }
    });

    pickerModal.querySelectorAll('[data-label-picker-dismiss]').forEach((el) => {
        el.addEventListener('click', closePicker);
    });

    previewModal.querySelectorAll('[data-label-preview-dismiss]').forEach((el) => {
        el.addEventListener('click', () => {
            closePreview();
            closePicker();
        });
    });

    window.dwOpenLabelPrintWizard = (prefill = []) => {
        openPicker(Array.isArray(prefill) ? prefill : [prefill].filter(Boolean));
    };

    if (previewStage) {
        const resizeObserver = new ResizeObserver(() => {
            if (previewModal.classList.contains('hidden') || !previewPdfBytes) {
                return;
            }

            clearTimeout(previewResizeTimer);
            previewResizeTimer = setTimeout(() => {
                renderPdfPreview(new Blob([previewPdfBytes], { type: 'application/pdf' }));
            }, 120);
        });
        resizeObserver.observe(previewStage);
    }

    updateSummary();
}
