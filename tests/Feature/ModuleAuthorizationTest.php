<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_finance_or_public_settings_modules(): void
    {
        $user = User::factory()->operator()->create();

        foreach ([
            '/reportes',
            '/finanzas',
            '/finanzas/inversiones',
            '/gastos',
            '/ajustes/welcome',
            '/api/investments',
            '/ajustes/welcome/api/items',
        ] as $path) {
            $this->actingAs($user)->get($path)->assertForbidden();
        }

        $this->actingAs($user)->post('/api/expenses', [
            'date' => now()->toDateString(),
            'concept' => 'Gasto no autorizado',
            'category' => 'operacion',
            'amount' => 1000,
        ])->assertForbidden();
    }

    public function test_admin_can_access_finance_and_public_settings_modules(): void
    {
        $admin = User::factory()->admin()->create();

        foreach ([
            '/reportes',
            '/finanzas',
            '/finanzas/inversiones',
            '/gastos',
            '/ajustes/welcome',
            '/api/investments',
            '/ajustes/welcome/api/items',
        ] as $path) {
            $this->actingAs($admin)->get($path)->assertOk();
        }

        $this->actingAs($admin)->postJson('/api/expenses', [
            'date' => now()->toDateString(),
            'concept' => 'Gasto autorizado',
            'category' => 'operacion',
            'amount' => 1000,
        ])->assertCreated();
    }
}
