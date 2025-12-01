<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::where('email', 'admin@demo.test')->value('id')
            ?? User::value('id');

        $expenses = [
            [
                'date' => now()->subDays(3)->toDateString(),
                'concept' => 'Servicios públicos',
                'category' => 'Operación',
                'amount' => 75000,
                'note' => 'Energía y agua',
            ],
            [
                'date' => now()->subDay()->toDateString(),
                'concept' => 'Publicidad',
                'category' => 'Marketing',
                'amount' => 50000,
                'note' => 'Campaña redes',
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create($expense + ['user_id' => $userId]);
        }
    }
}
