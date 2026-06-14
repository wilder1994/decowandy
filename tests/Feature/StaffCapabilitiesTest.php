<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffCapabilitiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_operate_only_user_can_access_sales_but_not_inventory(): void
    {
        $user = User::factory()->staffOperate()->create();

        $this->actingAs($user)->get(route('sales.index'))->assertOk();
        $this->actingAs($user)->get(route('customers.index'))->assertOk();
        $this->actingAs($user)->get(route('items.index'))->assertForbidden();
        $this->actingAs($user)->get(route('purchases.index'))->assertForbidden();
        $this->actingAs($user)->postJson('/api/items', [])->assertForbidden();
    }

    public function test_inventory_only_user_can_access_inventory_but_not_sales(): void
    {
        $user = User::factory()->staffInventory()->create();

        $this->actingAs($user)->get(route('items.index'))->assertOk();
        $this->actingAs($user)->get(route('purchases.index'))->assertOk();
        $this->actingAs($user)->get(route('sales.index'))->assertForbidden();
        $this->actingAs($user)->get(route('customers.index'))->assertForbidden();
        $this->actingAs($user)->postJson(route('sales.store'), [])->assertForbidden();
    }

    public function test_staff_with_both_modules_can_access_operational_and_inventory_areas(): void
    {
        $user = User::factory()->staffFull()->create();

        $this->actingAs($user)->get(route('sales.index'))->assertOk();
        $this->actingAs($user)->get(route('items.index'))->assertOk();
        $this->actingAs($user)->get(route('finance.index'))->assertForbidden();
        $this->actingAs($user)->get(route('reports.index'))->assertForbidden();
    }
}
