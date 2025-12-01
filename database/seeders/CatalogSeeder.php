<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('catalog_items')->truncate();

        $items = [
            [
                'category' => 'Papelería',
                'title' => 'Cuaderno A5 con tapa dura',
                'description' => '48 hojas, varios colores.',
                'price' => 8500,
                'show_price' => 1,
                'visible' => 1,
                'featured' => 1,
                'sort_order' => 1,
            ],
            [
                'category' => 'Papelería',
                'title' => 'Resma carta 75g',
                'description' => 'Papel multipropósito.',
                'price' => 22000,
                'show_price' => 1,
                'visible' => 1,
                'featured' => 0,
                'sort_order' => 2,
            ],
            [
                'category' => 'Impresión',
                'title' => 'Impresión color',
                'description' => 'Calidad láser a todo color.',
                'price' => 1500,
                'show_price' => 1,
                'visible' => 1,
                'featured' => 1,
                'sort_order' => 1,
            ],
            [
                'category' => 'Diseño',
                'title' => 'Tarjetas de presentación',
                'description' => 'Diseño y arte final.',
                'price' => 95000,
                'show_price' => 0,
                'visible' => 1,
                'featured' => 1,
                'sort_order' => 1,
            ],
        ];

        DB::table('catalog_items')->insert($items);
    }
}
