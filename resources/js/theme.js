const STORAGE_KEY = 'dw-theme';

export function getStoredTheme() {
    return localStorage.getItem(STORAGE_KEY) || 'system';
}

export function resolveTheme(pref) {
    if (pref === 'dark') {
        return 'dark';
    }
    if (pref === 'light') {
        return 'light';
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

export function applyTheme(pref) {
    const resolved = resolveTheme(pref);
    document.documentElement.setAttribute('data-theme', resolved);
    document.documentElement.dataset.themePref = pref;
    document.querySelectorAll('[data-dw-theme-select]').forEach((el) => {
        if (el.value !== pref) {
            el.value = pref;
        }
    });
    window.dispatchEvent(new CustomEvent('dw-theme-change', { detail: { pref, resolved } }));
}

export function setTheme(pref) {
    localStorage.setItem(STORAGE_KEY, pref);
    applyTheme(pref);
}

export function initTheme() {
    applyTheme(getStoredTheme());

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (getStoredTheme() === 'system') {
            applyTheme('system');
        }
    });

    document.querySelectorAll('[data-dw-theme-select]').forEach((select) => {
        select.addEventListener('change', (event) => {
            setTheme(event.target.value);
        });
    });
}

if (typeof window !== 'undefined') {
    window.dwSetTheme = setTheme;
    window.dwApplyTheme = applyTheme;
}
