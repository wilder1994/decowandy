export function initColorCombobox(input, options = {}) {
    if (!input || input.dataset.dwColorInit) {
        return;
    }

    const colors = Array.isArray(options.colors) ? options.colors : ['N/A'];
    const wrapper = input.closest('[data-dw-color-combobox]') || input.parentElement;
    const listId = `${input.id || 'color'}_list`;

    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-controls', listId);
    input.setAttribute('autocomplete', 'off');

    const list = document.createElement('ul');
    list.id = listId;
    list.className = 'dw-color-combobox hidden';
    list.setAttribute('role', 'listbox');

    if (wrapper) {
        wrapper.classList.add('relative');
        wrapper.appendChild(list);
    }

    let highlight = -1;
    let results = [];

    function filterColors(query) {
        const q = (query || '').trim().toLowerCase();
        if (!q) {
            return colors.slice(0, 12);
        }
        return colors.filter((color) => color.toLowerCase().includes(q)).slice(0, 12);
    }

    function closeList() {
        list.classList.add('hidden');
        list.innerHTML = '';
        highlight = -1;
        results = [];
        input.setAttribute('aria-expanded', 'false');
    }

    function renderList(items) {
        results = items;
        list.innerHTML = '';

        if (!items.length) {
            closeList();
            return;
        }

        items.forEach((color, index) => {
            const li = document.createElement('li');
            li.className = 'dw-color-combobox-option' + (index === 0 ? ' is-highlighted' : '');
            li.setAttribute('role', 'option');
            li.textContent = color;
            li.addEventListener('mousedown', (e) => {
                e.preventDefault();
                input.value = color;
                closeList();
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
            list.appendChild(li);
        });

        highlight = 0;
        list.classList.remove('hidden');
        input.setAttribute('aria-expanded', 'true');
    }

    function highlightItem(index) {
        highlight = index;
        list.querySelectorAll('.dw-color-combobox-option').forEach((el, i) => {
            el.classList.toggle('is-highlighted', i === index);
        });
    }

    input.addEventListener('input', () => renderList(filterColors(input.value)));
    input.addEventListener('focus', () => renderList(filterColors(input.value)));
    input.addEventListener('keydown', (e) => {
        if (list.classList.contains('hidden')) {
            if (e.key === 'ArrowDown') {
                renderList(filterColors(input.value));
            }
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = highlight < results.length - 1 ? highlight + 1 : 0;
            highlightItem(next);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = highlight > 0 ? highlight - 1 : results.length - 1;
            highlightItem(prev);
        } else if (e.key === 'Enter' && highlight >= 0) {
            e.preventDefault();
            input.value = results[highlight];
            closeList();
            input.dispatchEvent(new Event('change', { bubbles: true }));
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closeList();
        }
    });

    document.addEventListener('mousedown', (e) => {
        if (wrapper?.contains(e.target)) {
            return;
        }
        closeList();
    });

    input.dataset.dwColorInit = '1';
}
