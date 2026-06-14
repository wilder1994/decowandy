import { Html5Qrcode } from 'html5-qrcode';

let activeScanner = null;
let activeModal = null;

function ensureModal() {
    if (activeModal) {
        return activeModal;
    }

    const overlay = document.createElement('div');
    overlay.id = 'dwBarcodeScannerModal';
    overlay.className = 'fixed inset-0 z-[90] hidden';
    overlay.innerHTML = `
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" data-dw-scan-dismiss></div>
      <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-dw-lg bg-dw-card p-4 shadow-dw-neon dw-hairline-neon">
          <div class="mb-3 flex items-center justify-between">
            <h3 class="font-display text-base font-semibold text-dw-text">Escanear código</h3>
            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-full hover:bg-dw-lilac-soft" data-dw-scan-close aria-label="Cerrar">
              <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
          </div>
          <div id="dwBarcodeScannerRegion" class="overflow-hidden rounded-dw border border-dw-border bg-black"></div>
          <p class="mt-3 text-xs text-dw-muted">Apunta la cámara al código de barras o QR. Se detectará una sola vez.</p>
        </div>
      </div>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector('[data-dw-scan-dismiss]')?.addEventListener('click', () => closeBarcodeScanner());
    overlay.querySelector('[data-dw-scan-close]')?.addEventListener('click', () => closeBarcodeScanner());

    activeModal = overlay;
    return overlay;
}

export function closeBarcodeScanner() {
    if (activeScanner) {
        activeScanner.stop().catch(() => {}).finally(() => {
            activeScanner.clear().catch(() => {});
            activeScanner = null;
        });
    }

    if (activeModal) {
        activeModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

export function openBarcodeScanner({ onDetected, onError } = {}) {
    const modal = ensureModal();
    const regionId = 'dwBarcodeScannerRegion';

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    if (activeScanner) {
        activeScanner.stop().catch(() => {});
        activeScanner.clear().catch(() => {});
        activeScanner = null;
    }

    const scanner = new Html5Qrcode(regionId, { verbose: false });
    activeScanner = scanner;

    let handled = false;

    const handleCode = (code) => {
        if (handled || !code) {
            return;
        }
        handled = true;
        closeBarcodeScanner();
        onDetected?.(String(code).trim());
    };

    const config = {
        fps: 10,
        qrbox: { width: 260, height: 160 },
        aspectRatio: 1.5,
    };

    scanner.start(
        { facingMode: 'environment' },
        config,
        (decoded) => handleCode(decoded),
        () => {},
    ).catch((error) => {
        closeBarcodeScanner();
        onError?.(error);
    });

    return { close: closeBarcodeScanner };
}
