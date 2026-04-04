<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_sensitive_modules(): void
    {
        $user = User::factory()->operator()->create();

        $this->actingAs($user)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($user)->get(route('finance.index'))->assertForbidden();
        $this->actingAs($user)->get(route('finance.investments'))->assertForbidden();
        $this->actingAs($user)->get(route('expenses.index'))->assertForbidden();
        $this->actingAs($user)->get(route('settings.public'))->assertForbidden();
        $this->actingAs($user)->post(route('api.expenses.store'), [])->assertForbidden();
        $this->actingAs($user)->get(route('api.investments.index'))->assertForbidden();
        $this->actingAs($user)->get(route('catalog.preview'))->assertForbidden();
    }

    public function test_admin_can_access_sensitive_modules(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
        $this->actingAs($admin)->get(route('finance.index'))->assertOk();
        $this->actingAs($admin)->get(route('finance.investments'))->assertOk();
        $this->actingAs($admin)->get(route('expenses.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.public'))->assertOk();
    }
}
