{{-- Wizard imprimir etiquetas papelería --}}
<div id="labelPickerModal" class="fixed inset-0 z-[75] hidden" aria-hidden="true">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-label-picker-dismiss></div>
  <div class="absolute inset-0 flex items-end justify-center p-0 sm:items-center sm:p-4" role="dialog" aria-labelledby="labelPickerTitle" aria-modal="true">
    <div class="dw-label-wizard flex max-h-[92dvh] w-full flex-col overflow-hidden rounded-t-dw-lg bg-dw-card shadow-dw-neon sm:max-h-[85vh] sm:w-[min(640px,100%)] sm:rounded-dw-lg" style="border: 0.5px solid var(--dw-border-neon);">
      <header class="flex shrink-0 items-center justify-between border-b px-4 py-3" style="border-color: var(--dw-border);">
        <div>
          <h3 id="labelPickerTitle" class="font-display text-base font-bold text-dw-text">Imprimir etiquetas</h3>
          <p class="text-[11px] text-dw-muted">Marca productos y cantidad · máx. 200 etiquetas</p>
        </div>
        <button type="button" class="dw-pos-btn-square" data-label-picker-dismiss aria-label="Cerrar">
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </header>

      <div class="flex min-h-0 flex-1 flex-col px-4 py-3">
        <div class="relative mb-3 shrink-0">
          <span class="material-symbols-outlined pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-[18px] text-dw-muted">search</span>
          <input id="labelPickerSearch" type="search" class="dw-input w-full pl-9" placeholder="Filtrar por nombre o código…" autocomplete="off" spellcheck="false">
        </div>

        <div class="mb-2 flex shrink-0 items-center justify-between gap-2">
          <p id="labelPickerSummary" class="text-xs font-semibold text-dw-text">0 productos · 0 etiquetas</p>
          <button type="button" id="labelPickerClear" class="dw-btn-ghost text-[11px] hidden">Desmarcar todo</button>
        </div>

        <div class="dw-label-wizard__grid-wrap min-h-0 flex-1 overflow-hidden rounded-dw border-hairline border-dw-border">
          <table class="dw-label-wizard__grid dw-table w-full text-sm">
            <thead class="sticky top-0 z-[1] bg-dw-card text-[10px] uppercase tracking-wide text-dw-muted">
              <tr>
                <th class="w-10 px-2 py-2 text-center">
                  <input id="labelPickerSelectAll" type="checkbox" class="rounded border-dw-border text-dw-primary focus:ring-dw-primary" aria-label="Seleccionar todos visibles">
                </th>
                <th class="px-2 py-2 text-left">Producto / código</th>
                <th class="w-20 px-2 py-2 text-center">Cant.</th>
              </tr>
            </thead>
            <tbody id="labelPickerGridBody"></tbody>
          </table>
          <p id="labelPickerEmpty" class="hidden px-4 py-10 text-center text-xs text-dw-muted">
            No hay productos con código de barras.
          </p>
        </div>
      </div>

      <footer class="flex shrink-0 flex-wrap justify-end gap-2 border-t px-4 py-3" style="border-color: var(--dw-border);">
        <button type="button" class="dw-btn-secondary" data-label-picker-dismiss>Cancelar</button>
        <button type="button" id="labelPickerPreview" class="dw-btn-primary" disabled>Vista previa</button>
      </footer>
    </div>
  </div>
</div>

<div id="labelPreviewModal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" data-label-preview-dismiss></div>
  <div class="absolute inset-0 flex items-center justify-center p-3 sm:p-4" role="dialog" aria-labelledby="labelPreviewTitle" aria-modal="true">
    <div class="dw-label-preview-panel flex h-[88vh] max-h-[88vh] w-[min(720px,94vw)] min-h-0 flex-col overflow-hidden rounded-dw-lg bg-dw-card shadow-dw-neon" style="border: 0.5px solid var(--dw-border-neon);">
      <header class="flex shrink-0 flex-wrap items-center justify-between gap-2 border-b px-4 py-2.5" style="border-color: var(--dw-border);">
        <div>
          <h3 id="labelPreviewTitle" class="font-display text-base font-bold text-dw-text">Vista previa</h3>
          <p id="labelPreviewMeta" class="text-[11px] text-dw-muted">0 etiquetas · carta</p>
        </div>
        <button type="button" class="dw-pos-btn-square" data-label-preview-dismiss aria-label="Cerrar">
          <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
      </header>

      <div class="dw-label-preview-stage relative min-h-0 flex-1 overflow-hidden bg-dw-lilac-soft/40">
        <div id="labelPreviewLoading" class="absolute inset-0 z-10 hidden flex-col items-center justify-center gap-2 bg-dw-card/80 text-sm text-dw-muted">
          <span class="material-symbols-outlined animate-spin text-2xl text-dw-primary">progress_activity</span>
          Generando PDF…
        </div>
        <div id="labelPreviewCanvasHost" class="absolute inset-0 z-[1] min-h-0 overflow-auto"></div>
        <iframe id="labelPreviewFrame" class="pointer-events-none absolute inset-0 h-full w-full border-0 opacity-0" title="Imprimir etiquetas" aria-hidden="true" tabindex="-1"></iframe>
      </div>

      <footer class="flex shrink-0 flex-wrap justify-between gap-2 border-t px-4 py-3" style="border-color: var(--dw-border);">
        <button type="button" id="labelPreviewBack" class="dw-btn-secondary">
          <span class="material-symbols-outlined text-base align-middle">arrow_back</span>
          Volver
        </button>
        <div class="flex flex-wrap gap-2">
          <button type="button" class="dw-btn-secondary" data-label-preview-dismiss>Cancelar</button>
          <button type="button" id="labelPreviewDownload" class="dw-btn-secondary" disabled>
            <span class="material-symbols-outlined text-base align-middle">download</span>
            Descargar
          </button>
          <button type="button" id="labelPreviewPrint" class="dw-btn-primary" disabled>
            <span class="material-symbols-outlined text-base align-middle">print</span>
            Imprimir
          </button>
        </div>
      </footer>
    </div>
  </div>
</div>
