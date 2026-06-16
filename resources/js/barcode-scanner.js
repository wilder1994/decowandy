import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';
import { showDwToast } from './dw-toast';

const BARCODE_FORMATS = [
    Html5QrcodeSupportedFormats.QR_CODE,
    Html5QrcodeSupportedFormats.EAN_13,
    Html5QrcodeSupportedFormats.EAN_8,
    Html5QrcodeSupportedFormats.UPC_A,
    Html5QrcodeSupportedFormats.UPC_E,
    Html5QrcodeSupportedFormats.CODE_128,
    Html5QrcodeSupportedFormats.CODE_39,
    Html5QrcodeSupportedFormats.CODE_93,
    Html5QrcodeSupportedFormats.ITF,
    Html5QrcodeSupportedFormats.CODABAR,
];

let activeScanner = null;
let activeModal = null;
let pausedParents = [];
let scanHintTimer = null;
let lastOptions = null;

function ensureModal() {
    if (activeModal) {
        return activeModal;
    }

    const overlay = document.createElement('div');
    overlay.id = 'dwBarcodeScannerModal';
    overlay.className = 'dw-scanner-overlay hidden';
    overlay.innerHTML = `
      <div class="dw-scanner-overlay__backdrop" data-dw-scan-dismiss></div>
      <div class="dw-scanner-overlay__shell">
        <div class="dw-scanner-overlay__panel">
          <div class="dw-scanner-overlay__header">
            <div>
              <h3 class="dw-scanner-overlay__title">Escanear código</h3>
              <p class="dw-scanner-overlay__hint" id="dwScannerStatus">Apunta la barra o QR dentro del marco</p>
            </div>
            <button type="button" class="dw-scanner-overlay__close" data-dw-scan-close aria-label="Cerrar">
              <span class="material-symbols-outlined text-xl">close</span>
            </button>
          </div>
          <div class="dw-scanner-overlay__viewport">
            <div id="dwBarcodeScannerRegion"></div>
            <div class="dw-scanner-overlay__frame" aria-hidden="true"></div>
          </div>
          <div id="dwScannerError" class="dw-scanner-overlay__error hidden" role="alert"></div>
          <div class="dw-scanner-overlay__actions">
            <button type="button" id="dwScannerTorch" class="dw-scanner-overlay__action hidden" aria-label="Linterna">
              <span class="material-symbols-outlined text-lg">flashlight_on</span>
              <span>Linterna</span>
            </button>
            <button type="button" id="dwScannerRetry" class="dw-scanner-overlay__action hidden">
              <span class="material-symbols-outlined text-lg">refresh</span>
              <span>Reintentar</span>
            </button>
            <button type="button" class="dw-scanner-overlay__action dw-scanner-overlay__action--ghost" data-dw-scan-close>
              <span class="material-symbols-outlined text-lg">close</span>
              <span>Cancelar</span>
            </button>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector('[data-dw-scan-dismiss]')?.addEventListener('click', () => closeBarcodeScanner());
    overlay.querySelectorAll('[data-dw-scan-close]').forEach((btn) => {
        btn.addEventListener('click', () => closeBarcodeScanner());
    });
    overlay.querySelector('#dwScannerRetry')?.addEventListener('click', () => {
        if (lastOptions) {
            startScanning(lastOptions);
        }
    });
    overlay.querySelector('#dwScannerTorch')?.addEventListener('click', toggleTorch);

    activeModal = overlay;
    return overlay;
}

function setScannerStatus(message) {
    const status = document.getElementById('dwScannerStatus');
    if (status) {
        status.textContent = message;
    }
}

function setScannerError(message) {
    const errorBox = document.getElementById('dwScannerError');
    if (!errorBox) {
        return;
    }

    if (!message) {
        errorBox.textContent = '';
        errorBox.classList.add('hidden');
        return;
    }

    errorBox.textContent = message;
    errorBox.classList.remove('hidden');
}

function pauseParentModals(explicitParent) {
    resumeParentModals();

    const candidates = explicitParent
        ? [explicitParent]
        : Array.from(document.querySelectorAll('.fixed.inset-0')).filter((el) => {
            return el.id !== 'dwBarcodeScannerModal' && !el.classList.contains('hidden');
        });

    candidates.forEach((el) => {
        el.classList.add('dw-modal-paused');
        pausedParents.push(el);
    });
}

function resumeParentModals() {
    pausedParents.forEach((el) => el.classList.remove('dw-modal-paused'));
    pausedParents = [];
}

async function toggleTorch() {
    if (!activeScanner) {
        return;
    }

    try {
        const capabilities = activeScanner.getRunningTrackCameraCapabilities();
        const torch = capabilities.torchFeature();

        if (!torch.isSupported()) {
            setScannerError('Este dispositivo no soporta linterna.');
            return;
        }

        const next = !torch.value();
        await torch.apply(next);

        const btn = document.getElementById('dwScannerTorch');
        if (btn) {
            btn.classList.toggle('is-active', next);
        }

        setScannerError('');
    } catch (error) {
        setScannerError('No se pudo activar la linterna.');
    }
}

function setupTorchButton() {
    const btn = document.getElementById('dwScannerTorch');
    if (!btn || !activeScanner) {
        return;
    }

    try {
        const torch = activeScanner.getRunningTrackCameraCapabilities().torchFeature();
        if (torch.isSupported()) {
            btn.classList.remove('hidden');
            btn.classList.toggle('is-active', Boolean(torch.value()));
        } else {
            btn.classList.add('hidden');
        }
    } catch {
        btn.classList.add('hidden');
    }
}

function buildScanConfig() {
    return {
        fps: 15,
        disableFlip: false,
        qrbox: (viewfinderWidth, viewfinderHeight) => {
            const width = Math.min(Math.floor(viewfinderWidth * 0.92), 360);
            const height = Math.max(72, Math.min(Math.floor(viewfinderHeight * 0.28), 120));
            return { width, height };
        },
        aspectRatio: 1.777778,
        videoConstraints: {
            facingMode: { ideal: 'environment' },
            width: { ideal: 1280 },
            height: { ideal: 720 },
        },
    };
}

async function startScanning(options = {}) {
    const modal = ensureModal();
    const regionId = 'dwBarcodeScannerRegion';
    const retryBtn = document.getElementById('dwScannerRetry');

    lastOptions = options;
    setScannerError('');
    setScannerStatus('Apunta la barra o QR dentro del marco');
    retryBtn?.classList.add('hidden');

    if (activeScanner) {
        try {
            await activeScanner.stop();
        } catch {
            // ignore
        }
        try {
            activeScanner.clear();
        } catch {
            // ignore
        }
        activeScanner = null;
    }

    const scanner = new Html5Qrcode(regionId, {
        verbose: false,
        formatsToSupport: BARCODE_FORMATS,
        useBarCodeDetectorIfSupported: true,
    });

    activeScanner = scanner;

    let handled = false;
    let decodeAttempts = 0;

    const handleCode = (code) => {
        if (handled || !code) {
            return;
        }
        handled = true;
        const value = String(code).trim();
        closeBarcodeScanner();
        options.onDetected?.(value);
    };

    const handleFailure = (message, { toast = true, keepOpen = true } = {}) => {
        setScannerStatus('No se pudo completar el escaneo');
        setScannerError(message);
        retryBtn?.classList.remove('hidden');

        if (toast) {
            showDwToast(message, 'error');
        }

        options.onScanError?.(message);

        if (!keepOpen) {
            closeBarcodeScanner();
        }
    };

    try {
        await scanner.start(
            { facingMode: 'environment' },
            buildScanConfig(),
            (decoded) => handleCode(decoded),
            () => {
                decodeAttempts += 1;
                if (decodeAttempts === 40) {
                    setScannerStatus('Acerca más el código y enciende la linterna si hace falta');
                }
            },
        );

        setupTorchButton();

        if (scanHintTimer) {
            clearTimeout(scanHintTimer);
        }

        scanHintTimer = setTimeout(() => {
            if (!handled) {
                setScannerStatus('¿Problemas? Usa la linterna o pulsa Reintentar');
                retryBtn?.classList.remove('hidden');
            }
        }, 12000);
    } catch (error) {
        const message = error?.message?.includes('NotAllowed')
            ? 'Permiso de cámara denegado. Actívalo en el navegador.'
            : 'No se pudo acceder a la cámara. Verifica HTTPS y permisos.';

        handleFailure(message, { toast: true, keepOpen: true });
        options.onError?.(error);
    }
}

export function closeBarcodeScanner() {
    if (scanHintTimer) {
        clearTimeout(scanHintTimer);
        scanHintTimer = null;
    }

    if (activeScanner) {
        activeScanner.stop().catch(() => {}).finally(() => {
            activeScanner.clear().catch(() => {});
            activeScanner = null;
        });
    }

    if (activeModal) {
        activeModal.classList.add('hidden');
    }

    document.body.classList.remove('overflow-hidden');
    resumeParentModals();
    setScannerError('');
}

export function openBarcodeScanner(options = {}) {
    const modal = ensureModal();

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    pauseParentModals(options.parentModal ?? null);

    startScanning(options);

    return { close: closeBarcodeScanner };
}
