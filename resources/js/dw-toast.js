let toastTimer = null;

export function showDwToast(message, type = 'info', durationMs = 4500) {
    if (!message) {
        return;
    }

    let toast = document.getElementById('dwGlobalToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'dwGlobalToast';
        toast.className = 'dw-global-toast';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
          <span class="dw-global-toast__icon material-symbols-outlined text-base" aria-hidden="true">info</span>
          <span class="dw-global-toast__text"></span>
        `;
        document.body.appendChild(toast);
    }

    const icon = toast.querySelector('.dw-global-toast__icon');
    const text = toast.querySelector('.dw-global-toast__text');

    toast.classList.remove('dw-global-toast--success', 'dw-global-toast--error', 'dw-global-toast--info', 'is-visible');

    if (type === 'success') {
        toast.classList.add('dw-global-toast--success');
        if (icon) icon.textContent = 'check_circle';
    } else if (type === 'error') {
        toast.classList.add('dw-global-toast--error');
        if (icon) icon.textContent = 'error';
    } else {
        toast.classList.add('dw-global-toast--info');
        if (icon) icon.textContent = 'info';
    }

    if (text) {
        text.textContent = message;
    }

    toast.classList.add('is-visible');

    if (toastTimer) {
        clearTimeout(toastTimer);
    }

    toastTimer = setTimeout(() => {
        toast?.classList.remove('is-visible');
    }, durationMs);
}
