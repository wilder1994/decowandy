@props(['category', 'limit' => 5])

@php
    $items = collect($category['items'] ?? []);
    $tags = $items->take($limit);
    $slug = $category['slug'] ?? \Illuminate\Support\Str::slug($category['name'] ?? '');
    $ctaLabel = $category['cta_label'] ?? 'Ver más';
    $emptyMessage = $category['tag_empty'] ?? 'Sin elementos.';
    $background = $category['card_background'] ?? 'linear-gradient(135deg, var(--dw-lilac), var(--dw-accent))';
@endphp

<div class="rounded-3xl bg-white border border-gray-100 p-5 shadow-sm hover:shadow-md transition">
    <div class="h-36 rounded-2xl mb-3" style="background:{{ $background }}"></div>
    <h3 class="text-lg font-semibold">{{ $category['name'] ?? '' }}</h3>
    @if(!empty($category['card_summary']))
        <p class="text-sm text-gray-600">{{ $category['card_summary'] }}</p>
    @endif

    <div class="mt-4 flex flex-wrap gap-2">
        @forelse($tags as $item)
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm text-slate-700 truncate max-w-[140px]">
                {{ $item->title }}
            </span>
        @empty
            <span class="text-xs text-gray-400">{{ $emptyMessage }}</span>
        @endforelse
    </div>

    <a href="#{{ $slug }}-full" class="mt-4 inline-flex text-[color:var(--dw-accent)] hover:underline">
        {{ $ctaLabel }}
    </a>
</div>
