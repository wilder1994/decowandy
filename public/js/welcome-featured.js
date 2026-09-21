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
      return `<div class="dw-featured-slot" aria-hidden="true"></div>`;
    }

    const price = item.show_price && item.price
      ? `$ ${fmtCOP(item.price)}`
      : 'Cotizar';
    const stock = Number(item.stock || 0);
    const stockCls = stock > 0 ? 'text-emerald-200' : 'text-rose-200';
    const stockLabel = stock > 0 ? `${stock} disp.` : 'Agotado';
    const media = item.image
      ? `<img src="${esc(item.image)}" alt="${esc(item.title)}">`
      : `<div class="dw-featured-tile-fallback">${esc(item.title)}</div>`;

    return `
      <a href="${esc(item.wa || '#contacto')}"
         ${item.has_whatsapp ? 'target="_blank" rel="noopener"' : ''}
         class="dw-featured-tile">
        ${media}
        <div class="dw-featured-tile-overlay"></div>
        <div class="dw-featured-tile-meta">
          <p class="truncate text-xs font-semibold text-white sm:text-sm">${esc(item.title)}</p>
          <div class="mt-0.5 flex items-center justify-between gap-2 text-[10px] sm:text-xs">
            <span class="font-semibold text-dw-lilac">${price}</span>
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

    let index = 0;
    let timer = null;
    const canRotate = list.length > 2 && !REDUCE;

    function visiblePair() {
      if (list.length === 0) return [null, null];
      if (list.length === 1) return [list[0], null];
      if (list.length === 2) return [list[0], list[1]];
      return [
        list[index % list.length],
        list[(index + 1) % list.length],
      ];
    }

    function render() {
      viewport.className = 'dw-featured-grid';
      const [a, b] = visiblePair();
      viewport.innerHTML = tileHTML(a) + tileHTML(b);

      if (prevBtn) {
        prevBtn.classList.toggle('invisible', !canRotate);
        prevBtn.disabled = !canRotate;
      }
      if (nextBtn) {
        nextBtn.classList.toggle('invisible', !canRotate);
        nextBtn.disabled = !canRotate;
      }
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
