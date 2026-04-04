{{-- resources/views/settings/users.blade.php --}}
@extends('layouts.admin')

@section('title', 'Usuarios | DecoWandy')

@section('content')
  @php($roles = \App\Models\User::allowedRoles())

  <div class="mb-6 flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">Panel de usuarios</h1>
      <p class="text-sm text-gray-500">Consulta y gestiona usuarios del sistema.</p>
    </div>
    <div class="flex items-center gap-2">
      @can('manage-public-page')
        <a href="{{ route('settings.public') }}"
           class="rounded-xl border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-slate-50">
          Volver a ajustes
        </a>
      @endcan
      <button id="btnOpenUserModal"
              class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">
        Nuevo usuario
      </button>
    </div>
  </div>

  <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
    <div class="mb-3 flex items-center justify-between">
      <h2 class="text-lg font-semibold text-slate-800">Usuarios registrados</h2>
      <span class="text-xs text-gray-500">{{ $users->count() }} usuarios</span>
    </div>

    @if(session('status'))
      <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">
        {{ $errors->first() }}
      </div>
    @endif

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="text-gray-500">
          <tr class="text-left">
            <th class="py-2 pr-4">Nombre</th>
            <th class="py-2 pr-4">Email</th>
            <th class="py-2 pr-4">Rol</th>
            <th class="py-2 pr-4">Creado</th>
            <th class="py-2 pr-4 text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($users as $user)
            <tr>
              <td class="py-2 pr-4 font-semibold text-gray-800">{{ $user->name }}</td>
              <td class="py-2 pr-4 text-gray-700">{{ $user->email }}</td>
              <td class="py-2 pr-4">
                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-700">{{ $user->role ?? 'Sin rol' }}</span>
              </td>
              <td class="py-2 pr-4 text-gray-600">{{ optional($user->created_at)->format('d/m/Y') }}</td>
              <td class="py-2 pr-4">
                <div class="flex justify-end gap-2">
                  <button
                    class="btnEditUser text-xs font-semibold text-indigo-600 hover:underline"
                    data-id="{{ $user->id }}"
                    data-name="{{ $user->name }}"
                    data-email="{{ $user->email }}"
                    data-role="{{ $user->role ?? '' }}">
                    Editar
                  </button>
                  <form method="POST" action="{{ route('settings.users.destroy', $user) }}" onsubmit="return confirm('Eliminar usuario?');">
                    @csrf
                    @method('DELETE')
                    <button class="text-xs font-semibold text-rose-600 hover:underline" type="submit">Eliminar</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-4 text-center text-sm text-gray-500">No hay usuarios registrados.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div id="userModal" class="fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-black/40" data-close="1"></div>
    <div class="relative mx-auto mt-16 w-[min(620px,94vw)] rounded-2xl bg-white p-5 shadow-2xl">
      <div class="mb-4 flex items-center justify-between">
        <h3 id="userModalTitle" class="text-lg font-semibold text-slate-800">Nuevo usuario</h3>
        <button id="userModalClose" class="flex h-9 w-9 items-center justify-center rounded-full text-gray-600 hover:bg-gray-100">x</button>
      </div>

      <form id="userForm" method="POST" action="{{ route('settings.users.store') }}" class="grid gap-3 md:grid-cols-2">
        @csrf
        <input type="hidden" name="_method" value="POST" id="userFormMethod">
        <input type="text" name="name" id="u_name" class="rounded-lg border px-3 py-2 md:col-span-2" placeholder="Nombre" required>
        <input type="email" name="email" id="u_email" class="rounded-lg border px-3 py-2 md:col-span-2" placeholder="Email" required>

        <select name="role" id="u_role" class="rounded-lg border px-3 py-2" required>
          <option value="" disabled>Selecciona un rol</option>
          @foreach($roles as $role)
            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
          @endforeach
        </select>

        <div class="hidden md:block"></div>

        <input type="password" name="password" id="u_password" class="rounded-lg border px-3 py-2 md:col-span-2" placeholder="Contrasena">
        <input type="password" name="password_confirmation" id="u_password_confirmation" class="rounded-lg border px-3 py-2 md:col-span-2" placeholder="Confirmar contrasena">

        <p id="u_password_help" class="text-xs text-gray-500 md:col-span-2">Requerida al crear. Dejala vacia para mantener al editar.</p>

        <div class="mt-2 flex justify-end gap-2 md:col-span-2">
          <button type="button" id="userModalCancel" class="rounded-xl border px-4 py-2 text-sm font-semibold hover:bg-slate-50">Cancelar</button>
          <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-700">Guardar</button>
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
    const passInput = document.getElementById('u_password');
    const passConfirmInput = document.getElementById('u_password_confirmation');
    const passHelp = document.getElementById('u_password_help');
    const openBtn = document.getElementById('btnOpenUserModal');
    const closeBtn = document.getElementById('userModalClose');
    const cancelBtn = document.getElementById('userModalCancel');

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
        roleInput.value = user.role || '';
        passInput.value = '';
        passConfirmInput.value = '';
        passInput.required = false;
        passHelp.textContent = 'Dejala vacia para mantener la contrasena.';
      }
    }

    function closeModal() {
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }

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
        });
      });
    });
  });
</script>
@endpush
