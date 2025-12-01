<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function run(): void
    {
        $seller = User::first();

        $items = Item::whereIn('slug', [
            'cuaderno-a5',
            'resma-carta-75g',
            'impresion-color',
        ])->get()->keyBy('slug');

        $sale = Sale::create([
            'sale_code' => 'DW-000001',
            'sold_at' => Carbon::now()->subDays(2),
            'date' => Carbon::now()->subDays(2)->toDateString(),
            'time' => Carbon::now()->subDays(2)->format('H:i:s'),
            'user_id' => $seller?->id,
            'customer_name' => 'Cliente Demo',
            'payment_method' => 'cash',
            'amount_received' => 100000,
            'total' => 83500,
            'change_due' => 16500,
        ]);

        $lines = [
            [
                'item' => $items['cuaderno-a5'] ?? null,
                'quantity' => 3,
                'unit_price' => 8500,
                'category' => 'papeleria',
            ],
            [
                'item' => $items['resma-carta-75g'] ?? null,
                'quantity' => 1,
                'unit_price' => 22000,
                'category' => 'papeleria',
            ],
            [
                'item' => $items['impresion-color'] ?? null,
                'quantity' => 10,
                'unit_price' => 1500,
                'category' => 'impresion',
            ],
        ];

        foreach ($lines as $line) {
            if (!$line['item']) {
                continue;
            }

            SaleItem::create([
                'sale_id' => $sale->id,
                'item_id' => $line['item']->id,
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'line_total' => $line['unit_price'] * $line['quantity'],
                'category' => $line['category'],
            ]);

            if ($line['item']->type === 'product') {
                $this->inventory->out($line['item']->id, $line['quantity'], 'sale', $sale->id);
            }
        }
    }
}
