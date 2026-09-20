{{-- resources/views/catalog/category.blade.php --}}
@extends('layouts.public')

@section('title', $categoryName . ' — DecoWandy')

@section('content')
@php
    $dwHasWhatsapp = \App\Support\PublicContact::hasWhatsapp();
@endphp
<section class="max-w-7xl mx-auto px-4 py-12">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">{{ $categoryName }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $items->count() }} producto(s) publicados</p>
        </div>
        <a href="{{ route('welcome') }}"
        class="inline-flex items-center px-5 py-2.5 rounded-xl border border-[color:var(--dw-lilac-2)] text-[color:var(--dw-accent)] hover:bg-[color:var(--dw-lilac)] transition text-sm font-semibold">
        ← Volver al inicio
        </a>
    </div>

    @if(!empty($coverImage))
        <div class="mb-8 h-44 overflow-hidden rounded-3xl">
            <img src="{{ $coverImage }}" alt="{{ $categoryName }}" class="h-full w-full object-cover">
        </div>
    @endif

    @if ($items->isEmpty())
        <p class="text-gray-500">Aún no hay productos en esta categoría.</p>
    @else
        <div class="grid gap-8 md:grid-cols-3">
            @foreach ($items as $d)
                @php
                    $stock = (int) ($d->stock_quantity ?? 0);
                    $waMsg = $stock > 0
                        ? "Hola DecoWandy, vi «{$d->title}» en {$categoryName}. Hay {$stock} disponibles. ¿Me los puedes vender? Ya puedo transferir."
                        : "Hola DecoWandy, vi «{$d->title}» en {$categoryName}. ¿Cuándo vuelven a tener stock?";
                @endphp
                <div class="rounded-3xl bg-white border border-gray-100 shadow-sm hover:shadow-lg transition overflow-hidden">
                    <div class="h-48 flex items-center justify-center bg-[color:var(--dw-lilac)]/20 overflow-hidden">
                        @if ($d->image_path)
                            <img src="{{ $d->image_path }}"
                                class="max-h-44 w-auto object-contain transition-transform duration-300 hover:scale-105"
                                alt="{{ $d->title }}">
                        @else
                            <div class="text-gray-400 text-sm">Sin imagen</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-semibold">{{ $d->title }}</h3>
                        @if($d->description)
                            <p class="text-sm text-gray-600">{{ $d->description }}</p>
                        @endif
                        <p class="mt-2 text-xs font-medium {{ $stock > 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $stock > 0 ? $stock.' disponibles' : 'Agotado' }}
                        </p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-[color:var(--dw-accent)] font-semibold">
                                @if($d->show_price && $d->price)
                                    $ {{ number_format($d->price, 0, ',', '.') }}
                                @else
                                    $ —
                                @endif
                            </span>
                            <a href="{{ \App\Support\PublicContact::whatsappHref($waMsg) }}"
                               @if($dwHasWhatsapp) target="_blank" rel="noopener" @endif
                               class="text-sm px-3 py-1.5 rounded-xl bg-[color:var(--dw-primary)] text-white hover:opacity-90 transition">
                               {{ $dwHasWhatsapp ? 'Pedir' : 'Contacto' }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection
