<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeder principal con datos de ejemplo listos para pruebas end-to-end.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ItemSeeder::class,
            CatalogSeeder::class,
            PurchaseSeeder::class,
            SaleSeeder::class,
            ExpenseSeeder::class,
            InvestmentSeeder::class,
        ]);
    }
}
