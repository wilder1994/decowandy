{{-- resources/views/settings/public.blade.php --}}
@extends('layouts.admin')

@section('title','Ajustes — Editor de página de bienvenida')

@section('content')
<div class="flex items-center justify-between mb-4">
  <div>
    <h1 class="text-2xl font-bold">Editor de página de bienvenida</h1>
    <p class="text-sm text-gray-500">Administra las tarjetas públicas por categoría.</p>
  </div>
  <div class="flex items-center gap-2">
    <a href="{{ url('/#catalogo') }}" target="_blank"
       class="px-4 py-2 rounded-xl border hover:bg-gray-50">Ver vista pública</a>
    <button id="btnAdd"
       class="px-4 py-2 rounded-xl text-white brand-gradient shadow">Nuevo ítem</button>
  </div>
</div>

{{-- Tabs --}}
<div class="flex gap-2 mb-4">
  @php $tabs = ['Papelería','Impresión','Diseño']; @endphp
  @foreach($tabs as $cat)
    <button class="tab-btn rounded-xl px-4 py-2 text-sm font-semibold border"
            data-cat="{{ $cat }}">{{ $cat }}</button>
  @endforeach
</div>

{{-- Grid --}}
<div id="cardsGrid" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>

{{-- Modal CRUD --}}
<div id="itemModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/30"></div>
  <div class="relative mx-auto mt-16 w-[680px] max-w-[95vw] rounded-2xl bg-white p-5 shadow-lg">
    <div class="flex items-center justify-between mb-3">
      <h2 id="modalTitle" class="text-xl font-semibold">Nuevo ítem</h2>
      <button id="modalClose" class="text-gray-500 hover:text-gray-700">✕</button>
    </div>

    <div class="grid gap-4 grid-cols-12">
      <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium mb-1">Categoría</label>
        <select id="f_category" class="w-full rounded-xl border px-3 py-2">
          <option>Papelería</option>
          <option>Impresión</option>
          <option>Diseño</option>
        </select>
      </div>
      <div class="col-span-12 md:col-span-8">
        <label class="block text-sm font-medium mb-1">Título</label>
        <input id="f_title" type="text" class="w-full rounded-xl border px-3 py-2" placeholder="Nombre visible">
      </div>

      <div class="col-span-12">
        <label class="block text-sm font-medium mb-1">Descripción (opcional)</label>
        <textarea id="f_desc" rows="3" class="w-full rounded-xl border px-3 py-2" placeholder="Texto corto"></textarea>
      </div>

      <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium mb-1">Precio (COP)</label>
        <input id="f_price" type="text" class="w-full rounded-xl border px-3 py-2" value="0">
        <div class="mt-3 space-y-2 text-sm">
          <label class="flex items-center gap-2">
            <input id="f_showPrice" type="checkbox" class="rounded">
            <span>Mostrar precio</span>
          </label>
          <label class="flex items-center gap-2">
            <input id="f_visible" type="checkbox" class="rounded" checked>
            <span>Visible</span>
          </label>
          <label class="flex items-center gap-2">
            <input id="f_featured" type="checkbox" class="rounded">
            <span>Destacado (aparece en “Destacados”)</span>
          </label>
        </div>
      </div>

      <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium mb-1">Imagen (opcional)</label>
        <input id="f_image" type="file" accept="image/*" class="w-full rounded-xl border px-3 py-2">
        <button id="btnClearImg" type="button" class="mt-2 text-sm text-rose-600">Quitar imagen</button>
      </div>

      <div class="col-span-12 md:col-span-4">
        <label class="block text-sm font-medium mb-1">Previsualización</label>
        <div class="h-28 rounded-xl border bg-gray-50 flex items-center justify-center overflow-hidden">
          <img id="f_preview" alt="" class="max-h-28 object-contain">
        </div>
      </div>
    </div>

    <div class="mt-5 flex items-center justify-end gap-2">
      <button id="modalCancel" class="px-4 py-2 rounded-xl border hover:bg-gray-50">Cancelar</button>
      <button id="modalSave" class="px-4 py-2 rounded-xl text-white brand-gradient shadow">Guardar</button>
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
    }
  };
</script>
<script src="{{ asset('js/catalog-editor.js') }}"></script>
@endsection
