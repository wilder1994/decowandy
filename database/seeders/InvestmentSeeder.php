<?php

namespace Database\Seeders;

use App\Models\Investment;
use Illuminate\Database\Seeder;

class InvestmentSeeder extends Seeder
{
    public function run(): void
    {
        $investments = [
            [
                'date' => now()->subDays(10)->toDateString(),
                'concept' => 'Capital inicial',
                'amount' => 1500000,
                'note' => 'Aporte de socios',
            ],
            [
                'date' => now()->subDays(4)->toDateString(),
                'concept' => 'Compra impresora',
                'amount' => 950000,
                'note' => 'Equipo de impresión color',
            ],
        ];

        foreach ($investments as $inv) {
            Investment::create($inv);
        }
    }
}
