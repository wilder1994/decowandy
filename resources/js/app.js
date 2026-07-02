import './bootstrap';
import './chart-theme';
import { initTheme } from './theme';
import { openBarcodeScanner, closeBarcodeScanner } from './barcode-scanner';
import { initColorCombobox } from './dw-color-combobox';
import { showDwToast } from './dw-toast';
import { initLabelPrintWizard } from './label-print-wizard';
import { initPapeleriaPurchaseModal } from './papeleria-purchase-modal';
import { initCatalogListModal } from './catalog-list-modal';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.dwOpenBarcodeScanner = openBarcodeScanner;
window.dwCloseBarcodeScanner = closeBarcodeScanner;
window.dwInitColorCombobox = initColorCombobox;
window.dwShowToast = showDwToast;
window.dwInitCatalogListModal = initCatalogListModal;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initLabelPrintWizard();

    const cfgEl = document.getElementById('papeleria-modal-config');
    if (cfgEl?.textContent) {
        try {
            initPapeleriaPurchaseModal(JSON.parse(cfgEl.textContent));
        } catch (error) {
            console.error('No se pudo inicializar el modal de papelería.', error);
        }
    }
});
