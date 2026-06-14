@props(['disabled' => false, 'label' => null])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'dw-input']) }}>
