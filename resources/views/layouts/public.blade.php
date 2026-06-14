{{-- resources/views/layouts/public.blade.php --}}
{{-- Layout público para DecoWandy
     - Código/IDs/clases en inglés
     - Textos visibles y comentarios en español
--}}
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'DecoWandy')</title>
    <meta name="description" content="@yield('meta_description', 'Diseñar es crear, aprender es crecer')">
    @include('partials.dw-head-assets')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full">
    @php
        $dwWhatsappNumber = preg_replace('/\D+/', '', env('DW_WHATSAPP', ''));
        $dwHasWhatsapp = strlen($dwWhatsappNumber) >= 10;
        $dwWhatsappText = rawurlencode('Hola DecoWandy, quiero realizar un pedido');
        $dwWhatsappHref = $dwHasWhatsapp
            ? "https://wa.me/{$dwWhatsappNumber}?text={$dwWhatsappText}"
            : '#contacto';
    @endphp
    <header class="sticky top-0 z-40 border-b dw-hairline bg-dw-card/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-2.5">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="h-9 w-auto">
                <span class="sr-only">DecoWandy</span>
            </a>
            <nav class="hidden items-center gap-5 text-sm font-semibold md:flex">
                <a href="{{ route('catalog.category', 'papeleria') }}" class="text-dw-text transition hover:text-dw-primary">Papelería</a>
                <a href="{{ route('catalog.category', 'impresion') }}" class="text-dw-text transition hover:text-dw-primary">Impresión</a>
                <a href="{{ route('catalog.category', 'diseno') }}" class="text-dw-text transition hover:text-dw-primary">Diseño</a>
                <a href="#contacto" class="text-dw-text transition hover:text-dw-primary">Contacto</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="dw-btn-secondary hidden sm:inline-flex">
                    Ingresar
                </a>
                <a href="{{ $dwWhatsappHref }}" class="dw-btn-primary"
                   @if($dwHasWhatsapp) target="_blank" rel="noopener" @endif>
                    {{ $dwHasWhatsapp ? 'Pedir por WhatsApp' : 'Ver contacto' }}
                </a>
            </div>
        </div>
    </header>

    {{-- CONTENIDO PRINCIPAL --}}
    <main>
        @yield('content')
    </main>

    <footer id="contacto" class="mt-12 border-t dw-hairline bg-dw-card">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 py-8 md:grid-cols-3">
            <div>
                <img src="{{ asset('images/logo-decowandy.png') }}" alt="DecoWandy" class="mb-2 h-9">
                <p class="text-sm text-dw-muted">Diseñar es crear, aprender es crecer.</p>
            </div>
            <div>
                <h3 class="mb-2 font-display text-sm font-semibold">Contacto</h3>
                <p class="text-sm text-dw-muted">Jamundí, Colombia</p>
                @if($dwHasWhatsapp)
                    <p class="text-sm text-dw-muted">Tel/WhatsApp: <span class="font-semibold text-dw-text">{{ $dwWhatsappNumber }}</span></p>
                @endif
                <p class="text-sm text-dw-muted">Email: contacto@decowandy.com</p>
            </div>
            <div>
                <h3 class="mb-2 font-display text-sm font-semibold">Enlaces</h3>
                <ul class="space-y-1 text-sm text-dw-muted">
                    <li><a class="hover:text-dw-primary" href="{{ route('catalog.category', 'papeleria') }}">Papelería</a></li>
                    <li><a class="hover:text-dw-primary" href="{{ route('catalog.category', 'impresion') }}">Impresión</a></li>
                    <li><a class="hover:text-dw-primary" href="{{ route('catalog.category', 'diseno') }}">Diseño</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t py-3 text-center text-xs text-dw-muted dw-hairline">© {{ date('Y') }} DecoWandy. Todos los derechos reservados.</div>
    </footer>
</body>
</html>
