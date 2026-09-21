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

    {{-- HERO: columnas a la misma altura; CTAs y 2.º carrusel comparten la línea inferior --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[color:var(--dw-lilac)]/35 via-transparent to-transparent"></div>
        <div class="relative mx-auto grid max-w-7xl gap-5 px-4 pb-8 pt-4 md:grid-cols-2 md:items-stretch md:gap-8 md:pb-10 md:pt-5">
            {{-- Columna izquierda --}}
            <div class="flex min-h-0 flex-col">
                <div>
                    <h1 class="text-3xl font-bold leading-tight md:text-4xl lg:text-5xl" style="font-family:'Poppins',Inter,system-ui">
                        <a href="{{ route('catalog.category', 'diseno') }}" class="transition hover:text-[color:var(--dw-accent)]">Diseños</a>,
                        <a href="{{ route('catalog.category', 'papeleria') }}" class="transition hover:text-[color:var(--dw-accent)]">papelería</a> e
                        <span class="block">
                            <a href="{{ route('catalog.category', 'impresion') }}" class="transition hover:text-[color:var(--dw-accent)]">impresiones</a>
                        </span>
                    </h1>

                    <div class="mt-1.5 md:mt-2">
                        <svg viewBox="0 0 800 100" class="h-8 w-full md:h-10" aria-hidden="true">
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

                    <p class="mt-2 text-sm text-gray-600 md:text-[0.95rem]">
                        Logos, tarjetas, papelería y servicios de impresión. Hecho con cariño por DecoWandy.
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2 text-sm text-gray-700">
                        <span class="inline-flex items-center rounded-full border border-gray-100 bg-white px-3 py-1.5 shadow-sm">Fotocopias y escáner</span>
                        <span class="inline-flex items-center rounded-full border border-gray-100 bg-white px-3 py-1.5 shadow-sm">Impresión fotográfica</span>
                        <span class="inline-flex items-center rounded-full border border-gray-100 bg-white px-3 py-1.5 shadow-sm">Detalles y regalos</span>
                    </div>
                </div>

                {{-- Empuja CTAs al fondo = misma línea que el 2.º carrusel --}}
                <div class="mt-auto flex flex-wrap gap-3 pt-5">
                    <a href="{{ $dwHeroWhatsappHref }}"
                       @if($dwHasWhatsapp) target="_blank" rel="noopener" @endif
                       class="inline-flex items-center gap-2 rounded-2xl px-5 py-2.5 text-white brand-gradient shadow transition hover:opacity-90">
                        {{ $dwHasWhatsapp ? 'Solicitar por WhatsApp' : 'Ver contacto' }}
                    </a>
                    <a href="#catalogo"
                       class="inline-flex items-center gap-2 rounded-2xl border border-[color:var(--dw-lilac-2)] px-5 py-2.5 text-[color:var(--dw-accent)] transition hover:bg-[color:var(--dw-lilac)]">
                        Ver catálogo
                    </a>
                </div>
            </div>

            {{-- Columna derecha: vitrina flotante 2 filas, sin contenedor de fondo --}}
            <div class="flex min-h-0 w-full flex-col md:min-h-[17.5rem]">
                <div class="dw-featured-rail">
                    <div class="flex shrink-0 items-baseline justify-between gap-2">
                        <p class="dw-featured-label">Destacados</p>
                        @if($hasFeatured)
                            <p class="text-[11px] text-dw-muted">{{ $featuredMapped->count() }} en vitrina</p>
                        @endif
                    </div>

                    <div class="dw-featured-rows">
                        @foreach([0, 1] as $rowIndex)
                            @php
                                $rowItems = $rowIndex === 0 ? $featuredRow1 : $featuredRow2;
                                $slotA = $rowItems->get(0);
                                $slotB = $rowItems->get(1);
                            @endphp
                            <div class="dw-featured-row" data-featured-row="{{ $rowIndex }}">
                                <div class="dw-featured-grid" data-featured-viewport>
                                    @foreach([$slotA, $slotB] as $slot)
                                        @if($slot)
                                            <a href="{{ $slot['wa'] ?? '#contacto' }}"
                                               @if(!empty($slot['has_whatsapp'])) target="_blank" rel="noopener" @endif
                                               class="dw-featured-tile">
                                                @if(!empty($slot['image']))
                                                    <img src="{{ $slot['image'] }}" alt="{{ $slot['title'] }}">
                                                @else
                                                    <div class="dw-featured-tile-fallback">{{ $slot['title'] }}</div>
                                                @endif
                                                <div class="dw-featured-tile-overlay"></div>
                                                <div class="dw-featured-tile-meta">
                                                    <p class="truncate text-xs font-semibold text-white sm:text-sm">{{ $slot['title'] }}</p>
                                                    <div class="mt-0.5 flex items-center justify-between gap-2 text-[10px] sm:text-xs">
                                                        <span class="font-semibold text-dw-lilac">
                                                            @if(!empty($slot['show_price']) && !empty($slot['price']))
                                                                $ {{ number_format((int) $slot['price'], 0, ',', '.') }}
                                                            @else
                                                                Cotizar
                                                            @endif
                                                        </span>
                                                        @php $st = (int) ($slot['stock'] ?? 0); @endphp
                                                        <span class="{{ $st > 0 ? 'text-emerald-200' : 'text-rose-200' }}">
                                                            {{ $st > 0 ? $st.' disp.' : 'Agotado' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        @else
                                            <div class="dw-featured-slot" aria-hidden="true"></div>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="pointer-events-none absolute inset-y-0 -left-1 -right-1 z-10 flex items-center justify-between">
                                    <button type="button" data-featured-prev class="dw-featured-nav pointer-events-auto invisible" aria-label="Anterior">‹</button>
                                    <button type="button" data-featured-next class="dw-featured-nav pointer-events-auto invisible" aria-label="Siguiente">›</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="catalogo" class="mx-auto grid max-w-7xl gap-6 px-4 py-10 md:py-12">
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
