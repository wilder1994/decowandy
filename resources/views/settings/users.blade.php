{{-- resources/views/settings/users.blade.php --}}
@extends('layouts.admin')

@section('title', 'Usuarios | DecoWandy')

@section('content')
  <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <x-dw-page-header title="Panel de usuarios" subtitle="Consulta y gestiona usuarios del sistema." />
    <div class="flex items-center gap-2">
      @can('manage-public-page')
        <x-dw-button variant="secondary" :href="route('settings.public')">Volver a ajustes</x-dw-button>
      @endcan
      <x-dw-button id="btnOpenUserModal" type="button">Nuevo usuario</x-dw-button>
    </div>
  </div>

  <x-dw-card padding="p-0" class="overflow-hidden">
    <div class="flex items-center justify-between border-b px-4 py-3 dw-hairline">
      <h2 class="font-display text-sm font-semibold text-dw-text">Usuarios registrados</h2>
      <span class="text-xs text-dw-muted">{{ $users->count() }} usuarios</span>
    </div>

    <div class="px-4 py-3">
    @if(session('status'))
      <div class="dw-alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="dw-alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="overflow-x-auto">
      <table class="dw-table min-w-full text-sm">
        <thead>
          <tr class="text-left">
            <th class="py-2 pr-4">Nombre</th>
            <th class="py-2 pr-4">Email</th>
            <th class="py-2 pr-4">Acceso</th>
            <th class="py-2 pr-4">Creado</th>
            <th class="py-2 pr-4 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td class="py-2 pr-4 font-semibold text-dw-text">{{ $user->name }}</td>
              <td class="py-2 pr-4 text-dw-text">{{ $user->email }}</td>
              <td class="py-2 pr-4">
                <span class="dw-badge-primary">{{ $user->accessLabel() }}</span>
              </td>
              <td class="py-2 pr-4 text-dw-muted">{{ optional($user->created_at)->format('d/m/Y') }}</td>
              <td class="py-2 pr-4">
                <div class="flex justify-end gap-2">
                  <button
                    class="btnEditUser dw-link"
                    data-id="{{ $user->id }}"
                    data-name="{{ $user->name }}"
                    data-email="{{ $user->email }}"
                    data-role="{{ $user->role }}"
                    data-can-operate="{{ $user->can_operate ? '1' : '0' }}"
                    data-can-inventory="{{ $user->can_inventory ? '1' : '0' }}">
                    Editar
                  </button>
                  <form method="POST" action="{{ route('settings.users.destroy', $user) }}" onsubmit="return confirm('Eliminar usuario?');">
                    @csrf
                    @method('DELETE')
                    <button class="text-xs font-semibold text-dw-rose hover:underline" type="submit">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-4 text-center text-sm text-dw-muted">No hay usuarios registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    </div>
  </x-dw-card>

  <div id="userModal" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close="1"></div>
    <div class="relative mx-auto mt-16 w-[min(620px,94vw)] rounded-dw-lg bg-dw-card p-5 shadow-dw-neon dw-hairline-neon">
      <div class="mb-4 flex items-center justify-between">
        <h3 id="userModalTitle" class="font-display text-lg font-semibold text-dw-text">Nuevo usuario</h3>
        <button id="userModalClose" type="button" class="flex h-8 w-8 items-center justify-center rounded-dw border-hairline border-dw-border text-dw-muted hover:bg-dw-lilac-soft">x</button>
      </div>

      <form id="userForm" method="POST" action="{{ route('settings.users.store') }}" class="grid gap-3 md:grid-cols-2">
        @csrf
        <input type="hidden" name="_method" value="POST" id="userFormMethod">
        <input type="text" name="name" id="u_name" class="dw-input md:col-span-2" placeholder="Nombre" required>
        <input type="email" name="email" id="u_email" class="dw-input md:col-span-2" placeholder="Email" required>

        <select name="role" id="u_role" class="dw-select md:col-span-2" required>
          <option value="" disabled selected>Selecciona el tipo de cuenta</option>
          <option value="admin">Administrador (acceso total)</option>
          <option value="staff">Personal (módulos personalizados)</option>
        </select>

        <div id="staffModules" class="hidden rounded-dw border-hairline border-dw-border bg-dw-lilac-soft p-4 md:col-span-2">
          <p class="mb-3 text-sm font-semibold text-dw-text">Módulos habilitados</p>
          <div class="grid gap-2 sm:grid-cols-2">
            <label class="flex items-start gap-2 rounded-dw border-hairline border-dw-border bg-dw-card px-3 py-2">
              <input type="checkbox" name="can_operate" id="u_can_operate" value="1" class="mt-1 rounded border-dw-border text-dw-primary focus:ring-dw-primary">
              <span>
                <span class="block text-sm font-semibold text-dw-text">Operación</span>
                <span class="block text-xs text-dw-muted">Ventas, clientes y POS.</span>
              </span>
            </label>
            <label class="flex items-start gap-2 rounded-dw border-hairline border-dw-border bg-dw-card px-3 py-2">
              <input type="checkbox" name="can_inventory" id="u_can_inventory" value="1" class="mt-1 rounded border-dw-border text-dw-primary focus:ring-dw-primary">
              <span>
                <span class="block text-sm font-semibold text-dw-text">Inventario</span>
                <span class="block text-xs text-dw-muted">Productos, stock y compras.</span>
              </span>
            </label>
          </div>
        </div>

        <input type="password" name="password" id="u_password" class="dw-input md:col-span-2" placeholder="Contrasena">
        <input type="password" name="password_confirmation" id="u_password_confirmation" class="dw-input md:col-span-2" placeholder="Confirmar contrasena">

        <p id="u_password_help" class="text-xs text-dw-muted md:col-span-2">Requerida al crear. Dejala vacia para mantener al editar.</p>

        <div class="mt-2 flex justify-end gap-2 md:col-span-2">
          <x-dw-button type="button" id="userModalCancel" variant="secondary">Cancelar</x-dw-button>
          <x-dw-button type="submit">Guardar</x-dw-button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const methodInput = document.getElementById('userFormMethod');
    const title = document.getElementById('userModalTitle');
    const nameInput = document.getElementById('u_name');
    const emailInput = document.getElementById('u_email');
    const roleInput = document.getElementById('u_role');
    const modulesBox = document.getElementById('staffModules');
    const operateInput = document.getElementById('u_can_operate');
    const inventoryInput = document.getElementById('u_can_inventory');
    const passInput = document.getElementById('u_password');
    const passConfirmInput = document.getElementById('u_password_confirmation');
    const passHelp = document.getElementById('u_password_help');
    const openBtn = document.getElementById('btnOpenUserModal');
    const closeBtn = document.getElementById('userModalClose');
    const cancelBtn = document.getElementById('userModalCancel');

    function toggleModules() {
      const isStaff = roleInput.value === 'staff';
      modulesBox.classList.toggle('hidden', !isStaff);

      if (!isStaff) {
        operateInput.checked = false;
        inventoryInput.checked = false;
      }
    }

    function openModal(mode, user) {
      modal.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');

      if (mode === 'create') {
        title.textContent = 'Nuevo usuario';
        form.action = "{{ route('settings.users.store') }}";
        methodInput.value = 'POST';
        nameInput.value = '';
        emailInput.value = '';
        roleInput.value = '';
        operateInput.checked = false;
        inventoryInput.checked = false;
        passInput.value = '';
        passConfirmInput.value = '';
        passInput.required = true;
        passHelp.textContent = 'Requerida al crear. Dejala vacia para mantener al editar.';
      } else {
        title.textContent = 'Editar usuario';
        form.action = "{{ url('/ajustes/usuarios') }}/" + user.id;
        methodInput.value = 'PUT';
        nameInput.value = user.name || '';
        emailInput.value = user.email || '';
        roleInput.value = user.role || 'staff';
        operateInput.checked = user.canOperate === '1';
        inventoryInput.checked = user.canInventory === '1';
        passInput.value = '';
        passConfirmInput.value = '';
        passInput.required = false;
        passHelp.textContent = 'Dejala vacia para mantener la contrasena.';
      }

      toggleModules();
    }

    function closeModal() {
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }

    roleInput?.addEventListener('change', toggleModules);
    openBtn?.addEventListener('click', () => openModal('create'));
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
      if (e.target.dataset.close) {
        closeModal();
      }
    });

    document.querySelectorAll('.btnEditUser').forEach((btn) => {
      btn.addEventListener('click', () => {
        openModal('edit', {
          id: btn.dataset.id,
          name: btn.dataset.name,
          email: btn.dataset.email,
          role: btn.dataset.role,
          canOperate: btn.dataset.canOperate,
          canInventory: btn.dataset.canInventory,
        });
      });
    });
  });
</script>
@endpush
