{{-- resources/views/welcome/partials/category-card.blade.php --}}
@php
    use Illuminate\Support\Str;

    $slug = Str::slug($category['slug'] ?? $category['key'] ?? $category['name'] ?? 'categoria');
    $items = collect($category['items'] ?? []);
    $count = $items->count();
    $summary = $category['card_summary'] ?? '';
    $cta = $category['cta_label'] ?? 'Ver productos';
    $thumbs = $items->filter(function ($item) {
            $path = is_array($item)
                ? ($item['image_path'] ?? null)
                : ($item->image_path ?? null);

            return ! empty($path);
        })
        ->take(3)
        ->values();
@endphp

<a href="{{ route('catalog.category', $slug) }}"
   class="group relative block overflow-hidden rounded-3xl bg-white shadow-md transition hover:-translate-y-1 hover:shadow-xl">
    <div class="relative h-48 w-full overflow-hidden bg-gradient-to-br from-purple-100 to-purple-200"
         @if(!empty($category['card_background'] ?? null) && empty($category['cover_image'] ?? null))
             style="background: {{ $category['card_background'] }}"
         @endif>
        @if(!empty($category['cover_image'] ?? null))
            <img src="{{ $category['cover_image'] }}"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                 alt="{{ $category['name'] }}">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"></div>
        <div class="absolute bottom-3 left-3 right-3 flex items-end justify-between gap-2">
            <h3 class="text-xl font-semibold text-white drop-shadow-sm">{{ $category['name'] }}</h3>
            <span class="shrink-0 rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-[color:var(--dw-primary)] shadow-sm">
                {{ $count === 0 ? 'Vacío' : $count.' '.($count === 1 ? 'ítem' : 'ítems') }}
            </span>
        </div>
    </div>

    <div class="relative space-y-3 bg-gradient-to-br from-white via-white to-[color:var(--dw-lilac)]/40 p-5">
        @if($summary !== '')
            <p class="text-sm leading-relaxed text-gray-600">{{ $summary }}</p>
        @elseif($count === 0)
            <p class="text-sm text-gray-500">{{ $category['tag_empty'] ?? 'Sin productos' }}</p>
        @endif

        @if($thumbs->isNotEmpty())
            <div class="flex -space-x-2">
                @foreach($thumbs as $thumb)
                    @php
                        $src = is_array($thumb)
                            ? ($thumb['image_path'] ?? '')
                            : ($thumb->image_path ?? '');
                    @endphp
                    <img src="{{ $src }}"
                         alt=""
                         class="h-9 w-9 rounded-full object-cover ring-2 ring-white shadow-sm">
                @endforeach
                @if($count > $thumbs->count())
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[color:var(--dw-lilac)] text-[10px] font-semibold text-[color:var(--dw-primary)] ring-2 ring-white">
                        +{{ $count - $thumbs->count() }}
                    </span>
                @endif
            </div>
        @endif

        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-[color:var(--dw-accent)] transition group-hover:gap-2.5">
            {{ $cta }}
            <span aria-hidden="true">→</span>
        </span>
    </div>
</a>
