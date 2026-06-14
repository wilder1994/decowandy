@props(['label' => null, 'for' => null])

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1']) }}>
    @if ($label)
        <label @if($for) for="{{ $for }}" @endif class="dw-label">{{ $label }}</label>
    @endif
    <input {{ $attributes->except('class', 'label')->merge(['class' => 'dw-input']) }} />
</div>
