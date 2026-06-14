import './bootstrap';
import './chart-theme';
import { initTheme } from './theme';
import { openBarcodeScanner, closeBarcodeScanner } from './barcode-scanner';
import { initColorCombobox } from './dw-color-combobox';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.dwOpenBarcodeScanner = openBarcodeScanner;
window.dwCloseBarcodeScanner = closeBarcodeScanner;
window.dwInitColorCombobox = initColorCombobox;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
});
