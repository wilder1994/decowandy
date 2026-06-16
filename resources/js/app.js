import './bootstrap';
import './chart-theme';
import { initTheme } from './theme';
import { openBarcodeScanner, closeBarcodeScanner } from './barcode-scanner';
import { initColorCombobox } from './dw-color-combobox';
import { showDwToast } from './dw-toast';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.dwOpenBarcodeScanner = openBarcodeScanner;
window.dwCloseBarcodeScanner = closeBarcodeScanner;
window.dwInitColorCombobox = initColorCombobox;
window.dwShowToast = showDwToast;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
});
