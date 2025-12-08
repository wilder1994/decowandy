<x-guest-layout>
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-700 font-semibold">Seguridad</p>
            <h1 class="text-2xl font-bold text-slate-900">Confirma tu contraseña</h1>
            <p class="text-sm text-slate-600 mt-1">Antes de continuar, valida tu contraseña para mantener tu cuenta segura.</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div>
                <label for="password" class="block text-sm font-semibold text-slate-800">Contraseña</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-emerald-600 focus:ring-emerald-500/30">
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full rounded-xl bg-emerald-700 px-4 py-2.5 text-white font-semibold shadow-md shadow-emerald-700/20 hover:bg-emerald-800 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
