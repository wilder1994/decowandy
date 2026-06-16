@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'dw-page-header mb-4 sm:mb-5']) }}>
    <h1 class="dw-page-title">{{ $title }}</h1>
    @if ($subtitle)
        <p class="dw-page-subtitle mt-1">{{ $subtitle }}</p>
    @endif
</div>
