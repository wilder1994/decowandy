<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_user_management(): void
    {
        $user = User::factory()->operator()->create();

        $response = $this->actingAs($user)->get('/ajustes/usuarios');

        $response->assertForbidden();
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/ajustes/usuarios');

        $response->assertOk();
    }

    public function test_admin_can_create_user_with_explicit_role(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/ajustes/usuarios', [
            'name' => 'Operador Demo',
            'email' => 'operador@example.com',
            'role' => User::ROLE_OPERATOR,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('settings.users'));

        $this->assertDatabaseHas('users', [
            'email' => 'operador@example.com',
            'role' => User::ROLE_OPERATOR,
        ]);
    }
}
