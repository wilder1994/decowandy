/* public/js/welcome-featured.js */
(function () {
  const REDUCE = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const INTERVAL_MS = 3000;

  function esc(s) {
    return String(s || '').replace(/[&<>"']/g, (m) => (
      { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]
    ));
  }

  function fmtCOP(n) {
    return String(Math.max(0, Number(n || 0))).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function tileHTML(item) {
    if (!item) {
      return `<div class="aspect-[4/3] rounded-2xl border border-dashed border-white/40 bg-white/10"></div>`;
    }

    const price = item.show_price && item.price
      ? `$ ${fmtCOP(item.price)}`
      : 'Cotizar';
    const stock = Number(item.stock || 0);
    const stockCls = stock > 0 ? 'text-emerald-200' : 'text-rose-200';
    const stockLabel = stock > 0 ? `${stock} disp.` : 'Agotado';
    const img = item.image
      ? `<img src="${esc(item.image)}" alt="${esc(item.title)}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover/tile:scale-105">`
      : `<div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-[color:var(--dw-primary)] to-[color:var(--dw-accent)] text-sm text-white/90 px-3 text-center">${esc(item.title)}</div>`;

    return `
      <a href="${esc(item.wa || '#contacto')}"
         ${item.has_whatsapp ? 'target="_blank" rel="noopener"' : ''}
         class="group/tile relative block aspect-[4/3] overflow-hidden rounded-2xl bg-white/10 shadow-lg ring-1 ring-white/20">
        ${img}
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 p-2.5 sm:p-3">
          <p class="truncate text-sm font-semibold text-white">${esc(item.title)}</p>
          <div class="mt-1 flex items-center justify-between gap-2 text-xs">
            <span class="font-semibold text-[color:var(--dw-lilac)]">${price}</span>
            <span class="${stockCls}">${stockLabel}</span>
          </div>
        </div>
      </a>
    `;
  }

  function mountRow(root, items) {
    if (!root) return;

    const viewport = root.querySelector('[data-featured-viewport]');
    const prevBtn = root.querySelector('[data-featured-prev]');
    const nextBtn = root.querySelector('[data-featured-next]');
    const list = Array.isArray(items) ? items.filter(Boolean) : [];

    if (!viewport) return;

    if (list.length === 0) {
      root.classList.add('hidden');
      return;
    }

    root.classList.remove('hidden');
    let index = 0;
    let timer = null;
    const canRotate = list.length > 2 && !REDUCE;

    function visiblePair() {
      if (list.length === 1) return [list[0], null];
      if (list.length === 2) return [list[0], list[1]];
      const a = list[index % list.length];
      const b = list[(index + 1) % list.length];
      return [a, b];
    }

    function render() {
      if (list.length === 1) {
        viewport.className = 'grid grid-cols-1 gap-3';
        viewport.innerHTML = tileHTML(list[0]);
      } else {
        viewport.className = 'grid grid-cols-2 gap-3';
        const [a, b] = visiblePair();
        viewport.innerHTML = tileHTML(a) + tileHTML(b);
      }
      const showNav = canRotate;
      if (prevBtn) prevBtn.classList.toggle('invisible', !showNav);
      if (nextBtn) nextBtn.classList.toggle('invisible', !showNav);
    }

    function step(delta) {
      if (!canRotate) return;
      index = (index + delta + list.length) % list.length;
      render();
    }

    function stop() {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    }

    function start() {
      stop();
      if (!canRotate) return;
      timer = setInterval(() => step(1), INTERVAL_MS);
    }

    prevBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      step(-1);
      start();
    });
    nextBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      step(1);
      start();
    });

    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);

    render();
    start();
  }

  document.addEventListener('DOMContentLoaded', () => {
    const data = window.DW_FEATURED || {};
    mountRow(document.querySelector('[data-featured-row="0"]'), data.row1 || []);
    mountRow(document.querySelector('[data-featured-row="1"]'), data.row2 || []);
  });
})();
