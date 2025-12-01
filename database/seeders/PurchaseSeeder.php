<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function run(): void
    {
        $items = Item::whereIn('slug', [
            'cuaderno-a5',
            'resma-carta-75g',
            'tinta-negra',
        ])->get()->keyBy('slug');

        $purchase = Purchase::create([
            'category' => 'Papelería',
            'date' => now()->subDays(5)->toDateString(),
            'supplier' => 'Papelería Central',
            'note' => 'Factura 001',
            'to_inventory' => true,
            'total' => 0,
        ]);

        $lines = [
            [
                'product_name' => 'Cuaderno A5',
                'quantity' => 20,
                'total_cost' => 80000,
                'item' => $items['cuaderno-a5'] ?? null,
            ],
            [
                'product_name' => 'Resma carta 75g',
                'quantity' => 10,
                'total_cost' => 180000,
                'item' => $items['resma-carta-75g'] ?? null,
            ],
            [
                'product_name' => 'Tinta negra',
                'quantity' => 5,
                'total_cost' => 100000,
                'item' => $items['tinta-negra'] ?? null,
            ],
        ];

        $total = 0;

        foreach ($lines as $line) {
            $item = $line['item'];
            $qty = $line['quantity'];
            $tcost = $line['total_cost'];
            $unitCost = intdiv($tcost, max(1, $qty));

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_name' => $line['product_name'],
                'quantity' => $qty,
                'total_cost' => $tcost,
                'unit_cost' => $unitCost,
                'item_id' => $item?->id,
            ]);

            $total += $tcost;

            if ($item) {
                $this->inventory->in($item->id, $qty, $unitCost, 'purchase', $purchase->id);
            }
        }

        $purchase->update(['total' => $total]);
    }
}
