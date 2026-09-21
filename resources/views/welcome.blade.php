{{-- resources/views/welcome.blade.php --}}
@extends('layouts.public')

@section('title', 'DecoWandy — Diseñar es crear, aprender es crecer')

@section('content')
    @php
        $dwHasWhatsapp = \App\Support\PublicContact::hasWhatsapp();
        $dwHeroWhatsappHref = \App\Support\PublicContact::whatsappHref('Hola DecoWandy, quiero cotizar un diseño o impresión');

        $featuredMapped = collect($destacados ?? [])->map(function ($d) use ($dwHasWhatsapp) {
            $stock = (int) ($d->stock_quantity ?? 0);
            $waMsg = $stock > 0
                ? "Hola DecoWandy, vi el destacado «{$d->title}». Hay {$stock} disponibles. ¿Me los puedes vender?"
                : "Hola DecoWandy, vi el destacado «{$d->title}». ¿Cuándo vuelven a tener stock?";

            return [
                'title' => $d->title,
                'image' => $d->image_path,
                'price' => (int) ($d->price ?? 0),
                'show_price' => (bool) ($d->show_price ?? false),
                'stock' => $stock,
                'wa' => \App\Support\PublicContact::whatsappHref($waMsg),
                'has_whatsapp' => $dwHasWhatsapp,
            ];
        })->values();

        $featuredRow1 = $featuredMapped->filter(fn ($_, $i) => $i % 2 === 0)->values();
        $featuredRow2 = $featuredMapped->filter(fn ($_, $i) => $i % 2 === 1)->values();
        $hasFeatured = $featuredMapped->isNotEmpty();
    @endphp

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[color:var(--dw-lilac)]/50 via-transparent to-transparent"></div>
        <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-4 pb-16 pt-12 md:grid-cols-2">
            <div>
                <h1 class="text-4xl font-bold leading-tight md:text-5xl" style="font-family:'Poppins',Inter,system-ui">
                    <a href="{{ route('catalog.category', 'diseno') }}" class="transition hover:text-[color:var(--dw-accent)]">Diseños</a>,
                    <a href="{{ route('catalog.category', 'papeleria') }}" class="transition hover:text-[color:var(--dw-accent)]">papelería</a> e
                    <span class="block">
                        <a href="{{ route('catalog.category', 'impresion') }}" class="transition hover:text-[color:var(--dw-accent)]">impresiones</a>
                    </span>
                </h1>

                <div class="mt-4">
                    <svg viewBox="0 0 800 100" class="h-20 w-full" aria-hidden="true">
                        <defs>
                            <linearGradient id="dwBrush" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="var(--dw-primary)" />
                                <stop offset="100%" stop-color="var(--dw-accent)" />
                            </linearGradient>
                        </defs>
                        <path d="M20,70 C120,20 220,90 320,60 C420,30 520,100 620,55 C700,30 760,65 780,60"
                              stroke="url(#dwBrush)" stroke-width="28" stroke-linecap="round" fill="none" opacity=".85"/>
                    </svg>
                </div>

                <p class="mt-4 text-gray-600">
                    Logos, tarjetas, papelería y servicios de impresión. Hecho con cariño por DecoWandy.
                </p>

                <div class="mt-5 flex flex-wrap gap-3 text-sm text-gray-700">
                    <span class="inline-flex items-center gap-2 rounded-full border border-gray-100 bg-white px-3 py-1.5 shadow-sm">
                        Fotocopias y escáner
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-gray-100 bg-white px-3 py-1.5 shadow-sm">
                        Impresión fotográfica
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-gray-100 bg-white px-3 py-1.5 shadow-sm">
                        Detalles y regalos
                    </span>
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ $dwHeroWhatsappHref }}"
                       @if($dwHasWhatsapp) target="_blank" rel="noopener" @endif
                       class="inline-flex items-center gap-2 rounded-2xl px-5 py-3 text-white brand-gradient shadow transition hover:opacity-90">
                        {{ $dwHasWhatsapp ? 'Solicitar por WhatsApp' : 'Ver contacto' }}
                    </a>
                    <a href="#catalogo"
                       class="inline-flex items-center gap-2 rounded-2xl border border-[color:var(--dw-lilac-2)] px-5 py-3 text-[color:var(--dw-accent)] transition hover:bg-[color:var(--dw-lilac)]">
                        Ver catálogo
                    </a>
                </div>
            </div>

            {{-- Destacados: 2 filas × 2 columnas (carrusel por fila si hay overflow) --}}
            <div class="w-full justify-self-stretch">
                <div class="relative overflow-hidden rounded-[1.75rem] bg-gradient-to-br from-[color:var(--dw-primary)] via-[#6b4f9a] to-[color:var(--dw-accent)] p-3 shadow-xl sm:p-4">
                    <div class="mb-3 flex items-center justify-between px-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-white/80">Destacados</p>
                        @if($hasFeatured)
                            <p class="text-[11px] text-white/60">{{ $featuredMapped->count() }} en vitrina</p>
                        @endif
                    </div>

                    @if($hasFeatured)
                        <div class="space-y-3">
                            @foreach([0, 1] as $rowIndex)
                                <div class="relative" data-featured-row="{{ $rowIndex }}">
                                    <div class="grid grid-cols-2 gap-3" data-featured-viewport>
                                        {{-- JS hidrata; fallback SSR abajo --}}
                                    </div>
                                    <div class="pointer-events-none absolute inset-y-0 left-0 right-0 flex items-center justify-between px-1">
                                        <button type="button"
                                                data-featured-prev
                                                class="pointer-events-auto invisible flex h-8 w-8 items-center justify-center rounded-full bg-black/35 text-white backdrop-blur-sm transition hover:bg-black/50"
                                                aria-label="Anterior">‹</button>
                                        <button type="button"
                                                data-featured-next
                                                class="pointer-events-auto invisible flex h-8 w-8 items-center justify-center rounded-full bg-black/35 text-white backdrop-blur-sm transition hover:bg-black/50"
                                                aria-label="Siguiente">›</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex aspect-[4/3] flex-col items-center justify-center rounded-2xl border border-dashed border-white/30 bg-white/10 px-6 text-center">
                            <p class="text-sm font-medium text-white">Pronto verás aquí los destacados</p>
                            <p class="mt-1 text-xs text-white/70">Márcalos como destacados en Ajustes del catálogo.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- CATÁLOGO / TARJETAS --}}
    <section id="catalogo" class="mx-auto grid max-w-7xl gap-6 px-4 py-12">
        <div>
            <h2 class="text-2xl font-bold">Explora por categoría</h2>
            <p class="mt-1 text-sm text-gray-500">Elige una categoría para ver productos y stock disponible.</p>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($categories as $category)
                @include('welcome.partials.category-card', ['category' => $category])
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
<script>
  window.DW_FEATURED = {
    row1: @json($featuredRow1),
    row2: @json($featuredRow2),
  };
</script>
<script src="{{ asset('js/welcome-featured.js') }}?v={{ filemtime(public_path('js/welcome-featured.js')) }}"></script>
@endpush
