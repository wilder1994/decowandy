<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Cuaderno A5',
                'sector' => 'papeleria',
                'type' => 'product',
                'sale_price' => 8500,
                'cost' => 4200,
                'unit' => 'unidad',
                'featured' => true,
                'stock' => 40,
                'min_threshold' => 8,
            ],
            [
                'name' => 'Resma Carta 75g',
                'sector' => 'papeleria',
                'type' => 'product',
                'sale_price' => 22000,
                'cost' => 14000,
                'unit' => 'paquete',
                'stock' => 25,
                'min_threshold' => 5,
            ],
            [
                'name' => 'Impresión Color',
                'sector' => 'impresion',
                'type' => 'service',
                'sale_price' => 1500,
                'cost' => 500,
                'unit' => 'página',
                'stock' => null,
            ],
            [
                'name' => 'Tarjetas de presentación',
                'sector' => 'diseno',
                'type' => 'service',
                'sale_price' => 95000,
                'cost' => 40000,
                'unit' => 'proyecto',
                'stock' => null,
            ],
            [
                'name' => 'Tinta negra',
                'sector' => 'impresion',
                'type' => 'product',
                'sale_price' => 38000,
                'cost' => 25000,
                'unit' => 'unidad',
                'stock' => 8,
                'min_threshold' => 2,
            ],
        ];

        foreach ($items as $data) {
            $item = Item::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                collect($data)
                    ->except(['stock', 'min_threshold'])
                    ->put('active', true)
                    ->put('featured', $data['featured'] ?? false)
                    ->put('description', $data['description'] ?? null)
                    ->all()
            );

            if ($data['type'] === 'product') {
                Stock::updateOrCreate(
                    ['item_id' => $item->id],
                    [
                        'quantity' => $data['stock'] ?? 0,
                        'min_threshold' => $data['min_threshold'] ?? 0,
                    ]
                );
            }
        }
    }
}
