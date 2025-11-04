{{-- resources/views/welcome.blade.php --}}
@extends('layouts.public')

@section('title', 'DecoWandy — Diseñar es crear, aprender es crecer')

@section('content')
    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 pt-12 pb-16 grid gap-10 md:grid-cols-2 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold leading-tight" style="font-family:'Poppins',Inter,system-ui">
                    Diseños, papelería e
                    <span class="block">impresiones</span>
                </h1>

                <div class="mt-4">
                    <svg viewBox="0 0 800 100" class="w-full h-20">
                        <defs>
                            <linearGradient id="dwBrush" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%"  stop-color="var(--dw-primary)" />
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

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola DecoWandy, quiero cotizar un diseño o impresión 🙂') }}"
                       target="_blank" rel="noopener"
                       class="inline-flex items-center px-5 py-3 rounded-2xl text-white brand-gradient shadow hover:opacity-90 transition">
                        Solicitar por WhatsApp
                    </a>
                    <a href="#catalogo"
                       class="inline-flex items-center px-5 py-3 rounded-2xl border border-[color:var(--dw-lilac-2)] text-[color:var(--dw-accent)] hover:bg-[color:var(--dw-lilac)] transition">
                        Ver catálogo
                    </a>
                </div>
            </div>

            {{-- ilustración --}}
            <div class="justify-self-center">
                {{-- si quieres dejamos el SVG que ya tenías, lo omito aquí para no alargar --}}
            </div>
        </div>
    </section>

    {{-- CATÁLOGO / TARJETAS --}}
    <section id="catalogo" class="max-w-7xl mx-auto px-4 py-12 grid gap-6">
        <h2 class="text-2xl font-bold mb-2">Explora por categoría</h2>
        <div class="grid gap-6 md:grid-cols-3">
            {{-- PAPELERÍA --}}
            <div class="rounded-3xl bg-white border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
                <div class="h-36 rounded-2xl mb-3" style="background:linear-gradient(135deg, var(--dw-lilac), #f6f1ff)"></div>
                <h3 class="text-lg font-semibold">Papelería</h3>
                <p class="text-sm text-gray-600">Cuadernos, sobres, papel bond, cartulinas y más.</p>

                {{-- productos de papelería --}}
                <div class="mt-4 flex flex-wrap gap-2">
                    @php $papShow = $papeleria->take(5); @endphp
                    @forelse($papShow as $p)
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-sm text-slate-700 truncate max-w-[140px]">
                            {{ $p->title }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400">Aún no hay productos en esta categoría.</span>
                    @endforelse
                </div>

                <a href="#papeleria-full" class="mt-4 inline-flex text-[color:var(--dw-accent)] hover:underline">
                    Ver productos
                </a>
            </div>

            {{-- IMPRESIÓN --}}
            <div class="rounded-3xl bg-white border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
                <div class="h-36 rounded-2xl mb-3" style="background:linear-gradient(135deg, #f1ecff, var(--dw-lilac))"></div>
                <h3 class="text-lg font-semibold">Impresión</h3>
                <p class="text-sm text-gray-600">Copias B/N y color, escaneo, fotos, anillados.</p>

                <div class="mt-4 flex flex-wrap gap-2">
                    @php $impShow = $impresion->take(5); @endphp
                    @forelse($impShow as $p)
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-sm text-slate-700 truncate max-w-[140px]">
                            {{ $p->title }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400">Aún no hay servicios de impresión.</span>
                    @endforelse
                </div>

                <a href="#impresion-full" class="mt-4 inline-flex text-[color:var(--dw-accent)] hover:underline">
                    Ver servicios
                </a>
            </div>

            {{-- DISEÑO --}}
            <div class="rounded-3xl bg-white border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
                <div class="h-36 rounded-2xl mb-3" style="background:linear-gradient(135deg, var(--dw-primary), var(--dw-lilac))"></div>
                <h3 class="text-lg font-semibold">Diseño</h3>
                <p class="text-sm text-gray-600">Logos, tarjetas y piezas gráficas a medida.</p>

                <div class="mt-4 flex flex-wrap gap-2">
                    @php $disShow = $diseno->take(5); @endphp
                    @forelse($disShow as $p)
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-sm text-slate-700 truncate max-w-[140px]">
                            {{ $p->title }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400">Pronto añadiremos productos…</span>
                    @endforelse
                </div>

                <a href="#diseno-full" class="mt-4 inline-flex text-[color:var(--dw-accent)] hover:underline">
                    Ver portafolio
                </a>
            </div>
        </div>
    </section>

    {{-- LISTAS COMPLETAS (anclas) --}}
    <section class="max-w-7xl mx-auto px-4 pb-12 space-y-10">
        {{-- PAPELERÍA FULL --}}
        <div id="papeleria-full">
            <h2 class="text-xl font-semibold mb-4">Papelería</h2>
            <div class="grid gap-6 md:grid-cols-3">
                @forelse($papeleria as $p)
                    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                        <div class="h-36 bg-slate-100 overflow-hidden">
                            @if($p->image_path)
                                <img src="{{ $p->image_path }}" class="w-full h-full object-cover" alt="{{ $p->title }}">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold">{{ $p->title }}</h3>
                            @if($p->description)
                                <p class="text-sm text-gray-600">{{ $p->description }}</p>
                            @endif
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[color:var(--dw-accent)] font-semibold">
                                    @if($p->show_price && $p->price)
                                        $ {{ number_format($p->price, 0, ',', '.') }}
                                    @else
                                        $ —
                                    @endif
                                </span>
                                <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola, me interesa: '.$p->title) }}"
                                   target="_blank" rel="noopener"
                                   class="text-sm px-3 py-1 rounded-xl bg-[color:var(--dw-primary)] text-white">Pedir</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No hay productos de papelería aún.</p>
                @endforelse
            </div>
        </div>

        {{-- IMPRESIÓN FULL --}}
        <div id="impresion-full">
            <h2 class="text-xl font-semibold mb-4">Impresión</h2>
            <div class="grid gap-6 md:grid-cols-3">
                @forelse($impresion as $p)
                    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                        <div class="h-36 bg-slate-100 overflow-hidden">
                            @if($p->image_path)
                                <img src="{{ $p->image_path }}" class="w-full h-full object-cover" alt="{{ $p->title }}">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold">{{ $p->title }}</h3>
                            @if($p->description)
                                <p class="text-sm text-gray-600">{{ $p->description }}</p>
                            @endif
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[color:var(--dw-accent)] font-semibold">
                                    @if($p->show_price && $p->price)
                                        $ {{ number_format($p->price, 0, ',', '.') }}
                                    @else
                                        $ —
                                    @endif
                                </span>
                                <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola, me interesa: '.$p->title) }}"
                                   target="_blank" rel="noopener"
                                   class="text-sm px-3 py-1 rounded-xl bg-[color:var(--dw-primary)] text-white">Pedir</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No hay servicios de impresión aún.</p>
                @endforelse
            </div>
        </div>

        {{-- DISEÑO FULL --}}
        <div id="diseno-full">
            <h2 class="text-xl font-semibold mb-4">Diseño</h2>
            <div class="grid gap-6 md:grid-cols-3">
                @forelse($diseno as $p)
                    <div class="rounded-3xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                        <div class="h-36 bg-slate-100 overflow-hidden">
                            @if($p->image_path)
                                <img src="{{ $p->image_path }}" class="w-full h-full object-cover" alt="{{ $p->title }}">
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold">{{ $p->title }}</h3>
                            @if($p->description)
                                <p class="text-sm text-gray-600">{{ $p->description }}</p>
                            @endif
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-[color:var(--dw-accent)] font-semibold">
                                    @if($p->show_price && $p->price)
                                        $ {{ number_format($p->price, 0, ',', '.') }}
                                    @else
                                        $ —
                                    @endif
                                </span>
                                <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola, me interesa: '.$p->title) }}"
                                   target="_blank" rel="noopener"
                                   class="text-sm px-3 py-1 rounded-xl bg-[color:var(--dw-primary)] text-white">Pedir</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No hay diseños cargados.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- DESTACADOS --}}
    <section class="max-w-7xl mx-auto px-4 pb-16">
        <h2 class="text-2xl font-bold mb-4">Destacados</h2>
        <div class="grid gap-6 md:grid-cols-3">
            @forelse($destacados as $d)
                <div class="rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-lg transition overflow-hidden">
                    <div class="h-40 bg-[color:var(--dw-lilac)]/30 overflow-hidden">
                        @if($d->image_path)
                            <img src="{{ $d->image_path }}" class="w-full h-full object-cover" alt="{{ $d->title }}">
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold">{{ $d->title }}</h3>
                        @if($d->description)
                            <p class="text-sm text-gray-600">{{ $d->description }}</p>
                        @endif
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[color:var(--dw-accent)] font-semibold">
                                @if($d->show_price && $d->price)
                                    $ {{ number_format($d->price, 0, ',', '.') }}
                                @else
                                    $ —
                                @endif
                            </span>
                            <a href="https://wa.me/{{ env('DW_WHATSAPP','57XXXXXXXXXX') }}?text={{ urlencode('Hola, me interesa: '.$d->title) }}"
                               target="_blank" rel="noopener"
                               class="text-sm px-3 py-1.5 rounded-xl bg-[color:var(--dw-primary)] text-white hover:opacity-90 transition">Pedir</a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No hay destacados por ahora.</p>
            @endforelse
        </div>
    </section>
@endsection
