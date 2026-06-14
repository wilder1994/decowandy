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

    public function test_admin_can_create_staff_user_with_operate_module(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/ajustes/usuarios', [
            'name' => 'Operador Demo',
            'email' => 'operador@example.com',
            'role' => User::ROLE_STAFF,
            'can_operate' => '1',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('settings.users'));

        $this->assertDatabaseHas('users', [
            'email' => 'operador@example.com',
            'role' => User::ROLE_STAFF,
            'can_operate' => true,
            'can_inventory' => false,
        ]);
    }

    public function test_admin_can_create_staff_user_with_both_modules(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/ajustes/usuarios', [
            'name' => 'Personal Completo',
            'email' => 'full@example.com',
            'role' => User::ROLE_STAFF,
            'can_operate' => '1',
            'can_inventory' => '1',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('settings.users'));

        $this->assertDatabaseHas('users', [
            'email' => 'full@example.com',
            'role' => User::ROLE_STAFF,
            'can_operate' => true,
            'can_inventory' => true,
        ]);
    }

    public function test_staff_user_requires_at_least_one_module(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/ajustes/usuarios', [
            'name' => 'Sin modulos',
            'email' => 'sin-modulos@example.com',
            'role' => User::ROLE_STAFF,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('can_operate');
    }
}
