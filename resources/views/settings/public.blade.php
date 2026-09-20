{{-- resources/views/settings/public.blade.php --}}
@extends('layouts.admin')

@section('title','Ajustes — Editor de página de bienvenida')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css">
@endpush

@section('content')
@php
  $categoryCollection = ($categories ?? collect()) instanceof \Illuminate\Support\Collection
      ? $categories
      : collect($categories ?? []);
  $initialCategory = $categoryCollection->first();
  $covers = $covers ?? collect();
  $categoryMeta = $categoryCollection->mapWithKeys(fn ($c) => [
      $c['slug'] => [
          'name' => $c['name'],
          'slug' => $c['slug'],
          'cta_label' => $c['cta_label'] ?? 'Ver más',
          'tag_empty' => $c['tag_empty'] ?? 'Sin productos',
          'card_background' => $c['card_background'] ?? null,
      ],
  ]);
@endphp

<div class="mb-4">
  <x-dw-page-header title="Editor de página de bienvenida" subtitle="La portada se ve igual que en la tienda. Sube o quita la imagen aquí." />
</div>

<div class="space-y-4">
  <div class="flex flex-wrap items-center justify-between gap-2">
    <div class="flex flex-wrap gap-2">
      @foreach($categoryCollection as $category)
        <button type="button" class="tab-btn dw-tab" data-cat="{{ $category['name'] }}" data-slug="{{ $category['slug'] }}" data-active="false">{{ $category['name'] }}</button>
      @endforeach
    </div>
    <x-dw-button id="btnAdd" type="button">Agregar al catálogo</x-dw-button>
  </div>

  <div class="dw-card p-4">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
      <div>
        <h3 class="font-display text-sm font-semibold text-dw-text">Portada de la tarjeta</h3>
        <p class="text-xs text-dw-muted">Así se verá en “Explora por categoría”.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <label class="dw-btn-secondary cursor-pointer px-3 py-1.5 text-xs">
          Subir imagen
          <input id="coverInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
        </label>
        <button id="btnClearCover" type="button" class="dw-btn-secondary px-3 py-1.5 text-xs text-dw-rose">Quitar</button>
      </div>
    </div>

    <div id="coverCard" class="mx-auto w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-md">
      <div id="coverPreview" class="relative h-48 w-full overflow-hidden bg-gradient-to-br from-purple-100 to-purple-200">
        <div id="coverCta" class="absolute top-3 left-3 z-10 inline-flex items-center gap-2 rounded-full bg-white/80 px-3 py-1 text-xs text-[color:var(--dw-primary)] shadow-sm">
          <span class="material-symbols-outlined text-sm">inventory_2</span>
          <span id="coverCtaLabel">Ver más</span>
        </div>
        <span id="coverEmptyHint" class="flex h-full items-center justify-center text-sm text-dw-muted">Sin portada (degradado por defecto)</span>
      </div>
      <div class="space-y-3 p-6">
        <div class="flex items-center justify-between">
          <h3 id="coverName" class="text-xl font-semibold text-dw-text">Categoría</h3>
          <span id="coverSlug" class="rounded-full bg-purple-50 px-2 py-1 text-xs text-purple-700"></span>
        </div>
        <p id="coverEmpty" class="text-sm text-gray-500"></p>
        <p class="mt-1 font-semibold text-[color:var(--dw-accent)]">Ver productos</p>
      </div>
    </div>
  </div>

  <div id="cardsGrid" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
</div>

{{-- Modal: agregar / editar ítem del catálogo --}}
<div id="itemModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close-item-modal></div>
  <div class="absolute inset-0 flex items-center justify-center p-4" data-close-item-modal>
    <div class="flex max-h-[min(92vh,40rem)] w-full max-w-md flex-col overflow-hidden rounded-dw-lg bg-dw-card shadow-dw-neon dw-hairline-neon" data-item-modal-panel>
      <div class="flex shrink-0 items-center justify-between border-b border-dw-border px-4 py-3">
        <h2 id="modalTitle" class="font-display text-lg font-semibold text-dw-text">Agregar al catálogo</h2>
        <button id="modalClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">✕</button>
      </div>

      <div class="min-h-0 flex-1 space-y-3 overflow-y-auto px-4 py-3">
        <div>
          <label class="dw-label mb-1" for="f_category">Categoría</label>
          <select id="f_category" class="dw-select">
            @foreach($categoryCollection as $category)
              <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="dw-label mb-1" for="f_item_id">Producto del inventario</label>
          <select id="f_item_id" class="dw-select">
            <option value="">Selecciona un producto…</option>
          </select>
          <p id="f_item_hint" class="mt-1 text-xs text-dw-muted">Ítems activos del sector, aún no publicados aquí.</p>
        </div>

        <div>
          <label class="dw-label mb-1" for="f_desc">Nota pública (opcional)</label>
          <textarea id="f_desc" rows="2" class="dw-input" maxlength="160" placeholder="Texto corto bajo el nombre en la tienda"></textarea>
        </div>

        <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-sm text-dw-text">
          <label class="flex items-center gap-2">
            <input id="f_showPrice" type="checkbox" class="rounded border-dw-border text-dw-primary" checked>
            <span>Mostrar precio</span>
          </label>
          <label class="flex items-center gap-2">
            <input id="f_visible" type="checkbox" class="rounded border-dw-border text-dw-primary" checked>
            <span>Visible</span>
          </label>
          <label class="flex items-center gap-2">
            <input id="f_featured" type="checkbox" class="rounded border-dw-border text-dw-primary">
            <span>Destacado</span>
          </label>
        </div>

        <div>
          <label class="dw-label mb-1">Imagen (opcional)</label>
          <div id="imageDropzone"
               class="relative mx-auto flex aspect-video w-full max-w-xs cursor-pointer flex-col items-center justify-center overflow-hidden rounded-dw border border-dashed border-dw-border bg-dw-lilac-soft/60 px-3 py-4 text-center transition hover:border-dw-primary hover:bg-dw-lilac-soft"
               tabindex="0"
               role="button"
               aria-label="Subir o pegar imagen">
            <input id="f_image" type="file" accept="image/jpeg,image/png,image/webp" class="hidden">
            <img id="f_preview" alt="" class="absolute inset-0 hidden h-full w-full object-cover">
            <div id="imageDropEmpty" class="pointer-events-none space-y-0.5">
              <span class="material-symbols-outlined text-2xl text-dw-primary">add_photo_alternate</span>
              <p class="text-sm font-medium text-dw-text">Arrastra, pega o haz clic</p>
              <p class="text-xs text-dw-muted">Se abrirá el recorte</p>
            </div>
          </div>
          <button id="btnClearImg" type="button" class="mx-auto mt-1.5 hidden block text-sm font-semibold text-dw-rose hover:underline">Quitar imagen</button>
        </div>
      </div>

      <div class="flex shrink-0 items-center justify-end gap-2 border-t border-dw-border px-4 py-3">
        <button id="modalCancel" type="button" class="dw-btn-secondary">Cancelar</button>
        <button id="modalSave" type="button" class="dw-btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal: recorte de imagen --}}
<div id="cropModal" class="fixed inset-0 z-[60] hidden">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="flex max-h-[92vh] w-full max-w-lg flex-col overflow-hidden rounded-dw-lg bg-dw-card shadow-dw-neon dw-hairline-neon">
      <div class="flex shrink-0 items-center justify-between border-b border-dw-border px-4 py-3">
        <h3 class="font-display text-lg font-semibold text-dw-text">Ajustar imagen</h3>
        <button id="cropClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">✕</button>
      </div>
      <div class="min-h-0 flex-1 bg-neutral-900 p-3" style="max-height: 55vh;">
        <img id="cropImage" alt="Recorte" class="block max-h-full max-w-full">
      </div>
      <p class="shrink-0 px-4 pt-2 text-xs text-dw-muted">Mueve y amplía para encuadrar la tarjeta pública.</p>
      <div class="flex shrink-0 items-center justify-end gap-2 px-4 py-3">
        <button id="cropCancel" type="button" class="dw-btn-secondary">Cancelar</button>
        <button id="cropApply" type="button" class="dw-btn-primary">Usar recorte</button>
      </div>
    </div>
  </div>
</div>

<script>
  window.CATALOG = {
    csrf: "{{ csrf_token() }}",
    routes: {
      index:  "{{ route('catalog.index') }}",
      store:  "{{ route('catalog.store') }}",
      update: "{{ url('ajustes/welcome/api/items') }}",
      destroy:"{{ url('ajustes/welcome/api/items') }}",
      sort:   "{{ route('catalog.sort') }}",
      inventory: "{{ route('catalog.inventory-options') }}",
      coverUpdate: "{{ url('ajustes/welcome/api/categories') }}",
      coverDestroy: "{{ url('ajustes/welcome/api/categories') }}",
    },
    defaultCategory: "{{ $initialCategory['name'] ?? 'Papelería' }}",
    defaultSlug: "{{ $initialCategory['slug'] ?? 'papeleria' }}",
    covers: @json($covers),
    categories: @json($categoryMeta),
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
<script src="{{ asset('js/catalog-editor.js') }}?v={{ filemtime(public_path('js/catalog-editor.js')) }}"></script>
@endsection
