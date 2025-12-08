<x-guest-layout>
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-700 font-semibold">Recuperar acceso</p>
            <h1 class="text-2xl font-bold text-slate-900">Restablecer contraseña</h1>
            <p class="text-sm text-slate-600 mt-1">Ingresa tu correo y te enviaremos un enlace para crear una nueva contraseña.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-800">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-xl bg-emerald-700 px-4 py-2.5 text-white font-semibold shadow-md shadow-emerald-700/20 hover:bg-emerald-800 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Enviar enlace de restablecimiento
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
