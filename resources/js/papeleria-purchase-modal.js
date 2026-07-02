import { initColorCombobox } from './dw-color-combobox';

let modalState = {
    mode: 'create',
    item: null,
};

export function initPapeleriaPurchaseModal(config) {
    const form = document.getElementById('purchase-form');
    const purchaseModal = document.getElementById('purchaseModal');
    if (!form || !purchaseModal || !config) {
        return;
    }

    const {
        storeUrl,
        barcodeLookupUrl,
        nextBarcodeUrl,
        csrfToken,
        inventoryConfig,
        today,
    } = config;

    const modalTitle = document.getElementById('purchaseModalTitle');
    const productMeta = document.getElementById('papeleriaProductMeta');
    const productMetaText = document.getElementById('papeleriaProductMetaText');
    const submitBtn = document.getElementById('purchaseSubmitBtn');
    const submitHint = document.getElementById('purchaseSubmitHint');
    const categoryInput = document.getElementById('purchase-category');
    const papeleriaHint = document.getElementById('papeleriaPurchaseHint');
    const linesBody = document.getElementById('purchase-lines');
    const templatePapeleria = document.getElementById('purchase-line-papeleria');
    const templateStandard = document.getElementById('purchase-line-standard');
    const headPapeleria = document.querySelector('.purchase-head-papeleria');
    const headStandard = document.querySelector('.purchase-head-standard');
    const addBtn = document.getElementById('add-purchase-line');
    const summaryLines = document.getElementById('summary-lines');
    const summaryUnits = document.getElementById('summary-units');
    const summaryAmount = document.getElementById('summary-amount');
    const feedbackBox = document.getElementById('purchase-feedback');
    const purchaseMetaFields = document.getElementById('purchaseMetaFields');

    let lineIndex = 0;
    let lookupTimer = null;

    const currencyFormatter = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 });
    const numberFormatter = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });

    function isPapeleriaCategory(value) {
        const v = (value || '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        return v === 'papeleria';
    }

    function suggestedSalePrice(cost) {
        const pct = Number(inventoryConfig.markup_percent || 40);
        const c = Number(cost || 0);
        if (!Number.isFinite(c) || c <= 0) {
            return 0;
        }
        return Math.round(c * (1 + pct / 100));
    }

    function showFeedback(message, type = 'success') {
        if (!feedbackBox) {
            return;
        }
        feedbackBox.textContent = message;
        feedbackBox.classList.remove('hidden', 'dw-alert-success', 'dw-alert-error');
        feedbackBox.classList.add(type === 'error' ? 'dw-alert-error' : 'dw-alert-success');
        if (window.dwShowToast) {
            window.dwShowToast(message, type === 'error' ? 'error' : 'success');
        }
    }

    function clearFeedback() {
        if (!feedbackBox) {
            return;
        }
        feedbackBox.classList.add('hidden');
        feedbackBox.textContent = '';
    }

    function updateCategoryMode() {
        const papeleria = isPapeleriaCategory(categoryInput?.value);
        headPapeleria?.classList.toggle('hidden', !papeleria);
        headStandard?.classList.toggle('hidden', papeleria);
        papeleriaHint?.classList.toggle('hidden', !papeleria);
    }

    function updateModeUi() {
        const isRestock = modalState.mode === 'restock';

        if (modalTitle) {
            modalTitle.textContent = isRestock
                ? 'Comprar más · Papelería'
                : 'Registrar compra · Papelería';
        }

        if (submitBtn) {
            submitBtn.textContent = 'Guardar compra';
        }

        if (submitHint) {
            submitHint.textContent = 'Papelería crea o reutiliza productos por código al guardar.';
        }

        addBtn?.classList.toggle('hidden', isRestock);

        linesBody?.querySelectorAll('.remove-line').forEach((btn) => {
            btn.classList.toggle('hidden', isRestock);
        });
    }

    function updateProductMeta(item) {
        if (!productMeta || !productMetaText) {
            return;
        }
        if (!item || modalState.mode !== 'restock') {
            productMeta.classList.add('hidden');
            return;
        }
        const stock = Number(item.stock ?? 0);
        const min = Number(item.min_stock ?? 0);
        const code = item.barcode || 'Sin código';
        productMetaText.textContent = `Stock actual: ${stock} · Mínimo: ${min} · Código: ${code}`;
        productMeta.classList.remove('hidden');
    }

    function recalc() {
        let totalLines = 0;
        let totalUnits = 0;
        let totalAmount = 0;

        linesBody.querySelectorAll('tr').forEach((row) => {
            const productInput = row.querySelector('.product');
            const qtyInput = row.querySelector('.qty');
            const costInput = row.querySelector('.cost');
            const unitCell = row.querySelector('.unit');

            const quantity = parseInt(qtyInput?.value ?? '0', 10) || 0;
            const totalCost = parseInt(costInput?.value ?? '0', 10) || 0;

            if (productInput?.value.trim()) {
                totalLines += 1;
            }
            totalUnits += quantity;
            totalAmount += totalCost;

            if (unitCell) {
                unitCell.textContent = numberFormatter.format(quantity > 0 ? Math.floor(totalCost / quantity) : 0);
            }
        });

        if (summaryLines) {
            summaryLines.textContent = String(totalLines);
        }
        if (summaryUnits) {
            summaryUnits.textContent = numberFormatter.format(totalUnits);
        }
        if (summaryAmount) {
            summaryAmount.textContent = currencyFormatter.format(totalAmount);
        }
    }

    async function lookupBarcode(row, code) {
        const status = row.querySelector('.line-status');
        const productInput = row.querySelector('.product');
        const itemIdInput = row.querySelector('.item-id');
        const saleInput = row.querySelector('.sale-price');
        const colorInput = row.querySelector('.color');
        const stockHint = row.querySelector('.stock-hint');

        if (!code) {
            if (status) {
                status.textContent = '';
            }
            if (itemIdInput) {
                itemIdInput.value = '';
            }
            if (stockHint) {
                stockHint.textContent = '';
            }
            return;
        }

        try {
            const res = await fetch(`${barcodeLookupUrl}/${encodeURIComponent(code)}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const data = await res.json();

            if (res.ok && data?.item) {
                if (productInput) {
                    productInput.value = data.item.name || '';
                }
                if (itemIdInput) {
                    itemIdInput.value = data.item.id || '';
                }
                if (saleInput && !saleInput.dataset.touched) {
                    saleInput.value = Math.round(Number(data.item.sale_price || 0));
                }
                if (colorInput) {
                    colorInput.value = data.item.color || 'N/A';
                }
                if (stockHint) {
                    stockHint.textContent = `Stock actual: ${data.item.stock ?? 0}`;
                }
                if (status) {
                    status.textContent = 'Reutilizando código · producto existente';
                    status.className = 'line-status text-[10px] text-green-700 font-medium';
                }
                return;
            }
        } catch (e) {
            console.warn(e);
        }

        if (itemIdInput) {
            itemIdInput.value = '';
        }
        if (stockHint) {
            stockHint.textContent = '';
        }
        if (status) {
            status.textContent = 'Código nuevo — se creará al guardar';
            status.className = 'line-status text-[10px] text-dw-primary';
        }
    }

    function wirePapeleriaRow(row) {
        const barcodeInput = row.querySelector('.barcode');
        const productInput = row.querySelector('.product');
        const qtyInput = row.querySelector('.qty');
        const costInput = row.querySelector('.cost');
        const saleInput = row.querySelector('.sale-price');
        const colorInput = row.querySelector('.color');
        const removeBtn = row.querySelector('.remove-line');
        const scanBtn = row.querySelector('.scan-barcode');
        const genBtn = row.querySelector('.gen-barcode');
        const reuseBtn = row.querySelector('.reuse-barcode');

        if (colorInput && window.dwInitColorCombobox) {
            delete colorInput.dataset.dwColorInit;
            initColorCombobox(colorInput, { colors: inventoryConfig.colors });
        }

        [qtyInput, costInput, productInput].forEach((input) => input?.addEventListener('input', recalc));

        costInput?.addEventListener('input', () => {
            if (!saleInput?.dataset.touched) {
                const qty = parseInt(qtyInput?.value || '1', 10) || 1;
                const total = parseInt(costInput.value || '0', 10) || 0;
                const unit = qty > 0 ? Math.floor(total / qty) : 0;
                saleInput.value = suggestedSalePrice(unit) || '';
            }
        });

        saleInput?.addEventListener('input', () => {
            saleInput.dataset.touched = '1';
        });

        barcodeInput?.addEventListener('input', () => {
            clearTimeout(lookupTimer);
            lookupTimer = setTimeout(() => lookupBarcode(row, barcodeInput.value.trim()), 350);
        });

        barcodeInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                lookupBarcode(row, barcodeInput.value.trim());
                productInput?.focus();
            }
        });

        reuseBtn?.addEventListener('click', () => {
            lookupBarcode(row, barcodeInput?.value.trim() || '');
            barcodeInput?.focus();
        });

        scanBtn?.addEventListener('click', () => {
            if (!window.dwOpenBarcodeScanner) {
                showFeedback('Escáner no disponible.', 'error');
                return;
            }
            window.dwOpenBarcodeScanner({
                parentModal: purchaseModal,
                onDetected: (code) => {
                    if (barcodeInput) {
                        barcodeInput.value = code;
                    }
                    lookupBarcode(row, code);
                    qtyInput?.focus();
                    qtyInput?.select?.();
                },
                onError: () => showFeedback('No se pudo acceder a la cámara.', 'error'),
                onScanError: (message) => showFeedback(message, 'error'),
            });
        });

        genBtn?.addEventListener('click', async () => {
            try {
                const res = await fetch(nextBarcodeUrl, {
                    headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'same-origin',
                });
                const data = await res.json();
                if (data?.barcode && barcodeInput) {
                    barcodeInput.value = data.barcode;
                    if (row.querySelector('.item-id')) {
                        row.querySelector('.item-id').value = '';
                    }
                    const status = row.querySelector('.line-status');
                    if (status) {
                        status.textContent = 'Código DWY nuevo';
                        status.className = 'line-status text-[10px] text-amber-700';
                    }
                    lookupBarcode(row, data.barcode);
                }
            } catch (e) {
                showFeedback('No se pudo generar código DWY.', 'error');
            }
        });

        removeBtn?.addEventListener('click', () => {
            if (modalState.mode === 'restock') {
                return;
            }
            row.remove();
            if (!linesBody.querySelector('tr')) {
                addLine();
            } else {
                recalc();
            }
        });
    }

    function wireStandardRow(row) {
        const qtyInput = row.querySelector('.qty');
        const costInput = row.querySelector('.cost');
        const productInput = row.querySelector('.product');
        const removeBtn = row.querySelector('.remove-line');

        [qtyInput, costInput, productInput].forEach((input) => input?.addEventListener('input', recalc));

        removeBtn?.addEventListener('click', () => {
            row.remove();
            if (!linesBody.querySelector('tr')) {
                addLine();
            } else {
                recalc();
            }
        });
    }

    function fillPapeleriaRow(row, item) {
        const barcode = row.querySelector('.barcode');
        const product = row.querySelector('.product');
        const itemId = row.querySelector('.item-id');
        const qty = row.querySelector('.qty');
        const cost = row.querySelector('.cost');
        const salePrice = row.querySelector('.sale-price');
        const color = row.querySelector('.color');
        const stockHint = row.querySelector('.stock-hint');
        const status = row.querySelector('.line-status');

        if (barcode) {
            barcode.value = item.barcode || '';
        }
        if (product) {
            product.value = item.name || '';
        }
        if (itemId) {
            itemId.value = item.id || '';
        }
        if (qty) {
            qty.value = modalState.mode === 'restock' ? '1' : '1';
        }
        if (cost) {
            cost.value = '0';
        }
        if (salePrice) {
            salePrice.value = Math.round(Number(item.sale_price || 0));
            salePrice.dataset.touched = '1';
        }
        if (color) {
            color.value = item.color || 'N/A';
        }
        if (stockHint) {
            stockHint.textContent = `Stock actual: ${item.stock ?? 0}`;
        }
        if (status && item.id) {
            status.textContent = 'Producto existente vinculado';
            status.className = 'line-status text-[10px] text-green-700 font-medium';
        }
    }

    function addLine(prefillItem = null) {
        const papeleria = isPapeleriaCategory(categoryInput?.value);
        const template = papeleria ? templatePapeleria : templateStandard;
        if (!template || !linesBody) {
            return null;
        }

        const clone = template.content.firstElementChild.cloneNode(true);
        const idx = lineIndex++;

        const productInput = clone.querySelector('.product');
        const qtyInput = clone.querySelector('.qty');
        const costInput = clone.querySelector('.cost');

        if (productInput) {
            productInput.name = `items[${idx}][product_name]`;
        }
        if (qtyInput) {
            qtyInput.name = `items[${idx}][quantity]`;
            if (!qtyInput.value) {
                qtyInput.value = 1;
            }
        }
        if (costInput) {
            costInput.name = `items[${idx}][total_cost]`;
            if (!costInput.value) {
                costInput.value = 0;
            }
        }

        if (papeleria) {
            wirePapeleriaRow(clone);
            if (prefillItem) {
                fillPapeleriaRow(clone, prefillItem);
            }
        } else {
            const itemSelect = clone.querySelector('.item-select');
            if (itemSelect) {
                itemSelect.name = `items[${idx}][item_id]`;
            }
            wireStandardRow(clone);
        }

        linesBody.appendChild(clone);
        recalc();
        return clone;
    }

    function rebuildLines(prefillItem = null) {
        linesBody.innerHTML = '';
        lineIndex = 0;
        addLine(prefillItem);
    }

    function resetForm(prefillItem = null) {
        form.reset();
        const dateInput = form.querySelector('[name="date"]');
        if (dateInput && today) {
            dateInput.value = today;
        }
        if (categoryInput) {
            categoryInput.value = 'Papelería';
        }
        const toInv = form.querySelector('[name="to_inventory"]');
        if (toInv) {
            toInv.checked = true;
        }
        clearFeedback();
        updateCategoryMode();
        rebuildLines(prefillItem);
    }

    function openModal(options = {}) {
        modalState.mode = options.mode || 'create';
        modalState.item = options.item || null;

        resetForm(modalState.item);
        updateModeUi();
        updateProductMeta(modalState.item);

        if (modalState.item && modalState.mode === 'restock') {
            const row = linesBody.querySelector('tr');
            if (row) {
                fillPapeleriaRow(row, modalState.item);
            }
        }

        purchaseModal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        purchaseModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        modalState = { mode: 'create', item: null };
        productMeta?.classList.add('hidden');
    }

    categoryInput?.addEventListener('change', () => {
        updateCategoryMode();
        rebuildLines();
    });
    categoryInput?.addEventListener('input', updateCategoryMode);

    addBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        addLine();
    });

    document.getElementById('closePurchaseModal')?.addEventListener('click', closeModal);
    purchaseModal.querySelector('[data-close]')?.addEventListener('click', closeModal);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearFeedback();

        const papeleria = isPapeleriaCategory(categoryInput?.value);
        const lines = [];

        linesBody.querySelectorAll('tr').forEach((row) => {
            const product = row.querySelector('.product')?.value?.trim() ?? '';
            const qty = parseInt(row.querySelector('.qty')?.value ?? '0', 10) || 0;
            const totalCost = parseInt(row.querySelector('.cost')?.value ?? '0', 10) || 0;
            if (!product) {
                return;
            }

            const line = { product_name: product, quantity: qty, total_cost: totalCost };

            if (papeleria) {
                const barcode = row.querySelector('.barcode')?.value?.trim() ?? '';
                const salePrice = parseInt(row.querySelector('.sale-price')?.value ?? '0', 10) || 0;
                const color = row.querySelector('.color')?.value?.trim() || 'N/A';
                const itemId = row.querySelector('.item-id')?.value ?? '';

                if (barcode) {
                    line.barcode = barcode;
                }
                if (salePrice > 0) {
                    line.sale_price = salePrice;
                }
                if (color) {
                    line.color = color;
                }
                if (itemId) {
                    line.item_id = Number(itemId);
                }
            }

            lines.push(line);
        });

        if (!lines.length) {
            showFeedback('Agrega al menos una línea válida.', 'error');
            return;
        }

        try {
            const payload = {
                date: form.querySelector('[name="date"]').value,
                category: categoryInput?.value.trim(),
                supplier: form.querySelector('[name="supplier"]').value.trim() || null,
                note: form.querySelector('[name="note"]').value.trim() || (modalState.mode === 'restock' ? 'Reposición desde Inventario' : null),
                to_inventory: form.querySelector('[name="to_inventory"]').checked,
                items: lines,
            };

            if (!payload.date || !payload.category) {
                showFeedback('Completa fecha y categoría.', 'error');
                return;
            }

            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (response.ok) {
                showFeedback('Compra registrada. Actualizando…', 'success');
                setTimeout(() => window.location.reload(), 900);
                closeModal();
                return;
            }

            const data = await response.json().catch(() => null);
            if (data?.errors) {
                showFeedback(Object.values(data.errors).flat().join(' '), 'error');
            } else if (data?.message) {
                showFeedback(data.message, 'error');
            } else {
                showFeedback('No se pudo registrar la compra.', 'error');
            }
        } catch (error) {
            console.error(error);
            showFeedback('Error inesperado al guardar.', 'error');
        }
    });

    window.dwOpenPapeleriaModal = openModal;
    window.dwClosePapeleriaModal = closeModal;

    updateCategoryMode();
    rebuildLines();

    return { openModal, closeModal };
}
