@props(['hover' => false, 'padding' => 'p-4'])

<div {{ $attributes->merge(['class' => ($hover ? 'dw-card-hover' : 'dw-card') . ' ' . $padding]) }}>
    {{ $slot }}
</div>
