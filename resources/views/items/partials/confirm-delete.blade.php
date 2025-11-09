@php($item = $item ?? null)
<div class="p-4">
  <h3 class="text-lg font-semibold mb-2">¿Eliminar "{{ optional($item)->name }}"?</h3>
  <p class="text-sm text-gray-600">Esta acción lo quitará del POS. Puedes volver a crearlo o reactivarlo más adelante.</p>
</div>
