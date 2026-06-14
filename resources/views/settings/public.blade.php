{{-- resources/views/settings/public.blade.php --}}
@extends('layouts.admin')

@section('title','Ajustes — Editor de página de bienvenida')

@section('content')
@php
  $categoryCollection = ($categories ?? collect()) instanceof \Illuminate\Support\Collection
      ? $categories
      : collect($categories ?? []);
  $initialCategory = $categoryCollection->first();
@endphp

<div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
  <x-dw-page-header title="Editor de página de bienvenida" subtitle="Administra las tarjetas públicas por categoría." />
  <div class="flex flex-wrap items-center gap-2">
    <x-dw-button variant="secondary" :href="url('/#catalogo')" target="_blank">Ver vista pública</x-dw-button>
    @can('manage-users')
      <x-dw-button variant="secondary" :href="route('settings.users')">Panel de usuarios</x-dw-button>
    @endcan
    <x-dw-button id="btnAdd" type="button">Nuevo ítem</x-dw-button>
  </div>
</div>

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px] xl:grid-cols-[minmax(0,1fr)_420px]">
  <div>
    <div class="mb-4 flex flex-wrap gap-2">
      @foreach($categoryCollection as $category)
        <button type="button" class="tab-btn dw-tab" data-cat="{{ $category['name'] }}" data-active="false">{{ $category['name'] }}</button>
      @endforeach
    </div>

    <div id="cardsGrid" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3"></div>
  </div>
</div>

<div id="itemModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
  <div class="relative mx-auto mt-16 w-[min(680px,95vw)] rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
    <div class="mb-3 flex items-center justify-between">
      <h2 id="modalTitle" class="font-display text-xl font-semibold text-dw-text">Nuevo ítem</h2>
      <button id="modalClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">✕</button>
    </div>

    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-12 md:col-span-4">
        <label class="dw-label mb-1" for="f_category">Categoría</label>
        <select id="f_category" class="dw-select">
          <option>Papelería</option>
          <option>Impresión</option>
          <option>Diseño</option>
        </select>
      </div>
      <div class="col-span-12 md:col-span-8">
        <label class="dw-label mb-1" for="f_title">Título</label>
        <input id="f_title" type="text" class="dw-input" placeholder="Nombre visible">
      </div>

      <div class="col-span-12">
        <label class="dw-label mb-1" for="f_desc">Descripción (opcional)</label>
        <textarea id="f_desc" rows="3" class="dw-input" placeholder="Texto corto"></textarea>
      </div>

      <div class="col-span-12 md:col-span-4">
        <label class="dw-label mb-1" for="f_price">Precio (COP)</label>
        <input id="f_price" type="text" class="dw-input" value="0">
        <div class="mt-3 space-y-2 text-sm text-dw-text">
          <label class="flex items-center gap-2">
            <input id="f_showPrice" type="checkbox" class="rounded border-dw-border text-dw-primary">
            <span>Mostrar precio</span>
          </label>
          <label class="flex items-center gap-2">
            <input id="f_visible" type="checkbox" class="rounded border-dw-border text-dw-primary" checked>
            <span>Visible</span>
          </label>
          <label class="flex items-center gap-2">
            <input id="f_featured" type="checkbox" class="rounded border-dw-border text-dw-primary">
            <span>Destacado (aparece en “Destacados”)</span>
          </label>
        </div>
      </div>

      <div class="col-span-12 md:col-span-4">
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

{{-- Variables para el JS externo --}}
<script>
  window.CATALOG = {
    csrf: "{{ csrf_token() }}",
    routes: {
      index:  "{{ route('catalog.index') }}",
      store:  "{{ route('catalog.store') }}",
      update: "{{ url('ajustes/welcome/api/items') }}",           // + '/{id}'
      destroy:"{{ url('ajustes/welcome/api/items') }}",           // + '/{id}/delete'
      sort:   "{{ route('catalog.sort') }}",
      preview:"{{ route('catalog.preview') }}",
    },
    defaultCategory: "{{ $initialCategory['name'] ?? 'Papelería' }}"
  };
</script>
<script src="{{ asset('js/catalog-editor.js') }}"></script>
@endsection
