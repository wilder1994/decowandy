<button {{ $attributes->merge(['type' => 'submit', 'class' => 'dw-btn-primary']) }}>
    {{ $slot }}
</button>
