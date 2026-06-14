<button {{ $attributes->merge(['type' => 'submit', 'class' => 'dw-btn-danger']) }}>
    {{ $slot }}
</button>
