<x-guest-layout>
    <div class="mx-auto max-w-md">
        <x-dw-page-header title="Confirma tu correo" subtitle="Hemos enviado un enlace de verificación. Haz clic para activar tu cuenta." />

        @if (session('status') == 'verification-link-sent')
            <div class="dw-alert-success mb-4">Hemos reenviado un enlace de verificación a tu correo.</div>
        @endif

        <div class="mt-4 flex flex-col items-center justify-between gap-3 sm:flex-row">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-dw-button type="submit">Reenviar correo de verificación</x-dw-button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-semibold text-dw-muted hover:text-dw-text">Cerrar sesión</button>
            </form>
        </div>
    </div>
</x-guest-layout>
