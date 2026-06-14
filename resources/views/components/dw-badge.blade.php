@props(['variant' => 'primary'])

@php
    $classes = match ($variant) {
        'warning' => 'dw-badge-warning',
        'danger' => 'dw-badge-danger',
        default => 'dw-badge-primary',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
