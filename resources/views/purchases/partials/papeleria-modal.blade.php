{{-- Modal papelería compartido: compras, catálogo (editar) e inventario (comprar más) --}}
<div id="purchaseModal" class="dw-app-modal hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close="true"></div>
    <div class="relative mx-auto mt-6 w-[min(920px,95%)] max-h-[92vh] overflow-y-auto rounded-dw-lg bg-dw-card p-4 shadow-dw-neon dw-hairline-neon sm:mt-12 sm:p-5">
        <div class="mb-4 flex items-center justify-between">
            <h3 id="purchaseModalTitle" class="font-display text-xl font-semibold text-dw-text">Registrar compra · Papelería</h3>
            <button id="closePurchaseModal" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="papeleriaProductMeta" class="mb-4 hidden rounded-dw border-hairline border-dw-border bg-dw-lilac-soft px-3 py-2 text-sm text-dw-text">
            <span id="papeleriaProductMetaText"></span>
        </div>

        <p class="mb-4 text-xs text-dw-muted">Escanea o escribe un código para <strong>reutilizar</strong> un producto existente, o genera un <strong>DWY nuevo</strong> solo si es un SKU distinto.</p>
        @include('purchases.partials.form')
    </div>
</div>
