@php
    $papeleriaModalConfig = $papeleriaModalConfig ?? [
        'storeUrl' => route('api.purchases.store'),
        'updateItemUrl' => url('/api/items/__ID__'),
        'barcodeLookupUrl' => url('/api/items/by-barcode'),
        'nextBarcodeUrl' => route('api.items.next-barcode'),
        'csrfToken' => csrf_token(),
        'inventoryConfig' => $inventoryConfig ?? [
            'colors' => config('decowandy.inventory.colors', ['N/A']),
            'markup_percent' => (int) config('decowandy.inventory.markup_percent', 40),
        ],
        'today' => now()->toDateString(),
    ];
@endphp
<script type="application/json" id="papeleria-modal-config">@json($papeleriaModalConfig)</script>
