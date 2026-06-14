@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-5']) }}>
    <h1 class="dw-page-title">{{ $title }}</h1>
    @if ($subtitle)
        <p class="dw-page-subtitle mt-0.5">{{ $subtitle }}</p>
    @endif
</div>
