import './bootstrap';
import './chart-theme';
import { initTheme } from './theme';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
});
