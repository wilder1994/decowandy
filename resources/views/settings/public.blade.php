{{-- resources/views/settings/public.blade.php --}}
@extends('layouts.admin')

@section('title','Ajustes — Editor de página de bienvenida')

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

<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
  <x-dw-page-header title="Editor de página de bienvenida" subtitle="La portada se ve igual que en la tienda. Sube o quita la imagen aquí." />
  <div class="flex flex-wrap items-center gap-2">
    @can('manage-users')
      <x-dw-button variant="secondary" :href="route('settings.users')">Panel de usuarios</x-dw-button>
    @endcan
    <x-dw-button id="btnAdd" type="button">Agregar producto</x-dw-button>
  </div>
</div>

<div class="space-y-4">
  <div class="flex flex-wrap gap-2">
    @foreach($categoryCollection as $category)
      <button type="button" class="tab-btn dw-tab" data-cat="{{ $category['name'] }}" data-slug="{{ $category['slug'] }}" data-active="false">{{ $category['name'] }}</button>
    @endforeach
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

<div id="itemModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
  <div class="relative mx-auto mt-10 w-[min(720px,95vw)] max-h-[90vh] overflow-y-auto rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
    <div class="mb-3 flex items-center justify-between">
      <h2 id="modalTitle" class="font-display text-xl font-semibold text-dw-text">Agregar producto</h2>
      <button id="modalClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">✕</button>
    </div>

    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-12 md:col-span-4">
        <label class="dw-label mb-1" for="f_category">Categoría</label>
        <select id="f_category" class="dw-select">
          @foreach($categoryCollection as $category)
            <option value="{{ $category['name'] }}">{{ $category['name'] }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-span-12 md:col-span-8">
        <label class="dw-label mb-1" for="f_item_search">Producto del inventario</label>
        <input id="f_item_search" type="search" class="dw-input" placeholder="Buscar por nombre o código…" autocomplete="off">
        <input id="f_item_id" type="hidden" value="">
        <div id="f_item_selected" class="mt-2 hidden rounded-dw border-hairline border-dw-border bg-dw-lilac-soft px-3 py-2 text-sm text-dw-text"></div>
        <div id="f_item_results" class="mt-1 hidden max-h-48 overflow-y-auto rounded-dw border border-dw-border bg-dw-card py-1 shadow-dw-neon"></div>
      </div>

      <div class="col-span-12">
        <label class="dw-label mb-1" for="f_desc">Nota pública (opcional)</label>
        <textarea id="f_desc" rows="2" class="dw-input" placeholder="Texto corto para el cliente"></textarea>
      </div>

      <div class="col-span-12 md:col-span-5 space-y-2 text-sm text-dw-text">
        <label class="flex items-center gap-2">
          <input id="f_showPrice" type="checkbox" class="rounded border-dw-border text-dw-primary" checked>
          <span>Mostrar precio</span>
        </label>
        <label class="flex items-center gap-2">
          <input id="f_visible" type="checkbox" class="rounded border-dw-border text-dw-primary" checked>
          <span>Visible en la tienda</span>
        </label>
        <label class="flex items-center gap-2">
          <input id="f_featured" type="checkbox" class="rounded border-dw-border text-dw-primary">
          <span>Destacado</span>
        </label>
      </div>

      <div class="col-span-12 md:col-span-3">
        <label class="dw-label mb-1" for="f_image">Imagen (opcional)</label>
        <input id="f_image" type="file" accept="image/*" class="dw-input">
        <button id="btnClearImg" type="button" class="mt-2 text-sm font-semibold text-dw-rose hover:underline">Quitar imagen</button>
      </div>

      <div class="col-span-12 md:col-span-4">
        <label class="dw-label mb-1">Previsualización</label>
        <div class="flex h-28 items-center justify-center overflow-hidden rounded-dw border-hairline border-dw-border bg-dw-lilac-soft">
          <img id="f_preview" alt="" class="max-h-28 object-contain">
        </div>
      </div>
    </div>

    <div class="mt-5 flex items-center justify-end gap-2">
      <button id="modalCancel" type="button" class="dw-btn-secondary">Cancelar</button>
      <button id="modalSave" type="button" class="dw-btn-primary">Guardar</button>
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
<script src="{{ asset('js/catalog-editor.js') }}?v={{ filemtime(public_path('js/catalog-editor.js')) }}"></script>
@endsection
