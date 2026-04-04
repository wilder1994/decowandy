<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_sale_without_triggering_runtime_error(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
            'sale_price' => 5000,
            'active' => true,
        ]);

        Stock::create([
            'item_id' => $item->id,
            'quantity' => 10,
            'min_threshold' => 1,
        ]);

        $response = $this->actingAs($admin)->postJson(route('sales.store'), [
            'payment_method' => 'cash',
            'amount_received' => 10000,
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 1,
                    'unit_price' => 5000,
                ],
            ],
        ]);

        $response->assertCreated();
        $response->assertJson([
            'ok' => true,
            'total' => 5000,
            'change_due' => 5000,
        ]);

        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseCount('sale_items', 1);
        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'quantity' => 9,
        ]);

        $sale = Sale::firstOrFail();
        $line = SaleItem::firstOrFail();

        $this->assertSame($admin->id, $sale->user_id);
        $this->assertSame(5000, $sale->total);
        $this->assertSame('cash', $sale->payment_method);
        $this->assertSame($sale->id, $line->sale_id);
        $this->assertSame(5000, $line->unit_price);
    }

    public function test_sale_persists_selected_payment_method(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create([
            'type' => 'service',
            'sector' => 'diseno',
            'sale_price' => 18000,
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->postJson(route('sales.store'), [
            'payment_method' => 'transfer',
            'amount_received' => 18000,
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('total', 18000);

        $this->assertDatabaseHas('sales', [
            'payment_method' => 'transfer',
            'total' => 18000,
        ]);
    }

    public function test_sale_rejects_inactive_items(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
            'sale_price' => 5000,
            'active' => false,
        ]);

        $response = $this->actingAs($admin)->postJson(route('sales.store'), [
            'payment_method' => 'cash',
            'amount_received' => 5000,
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('sales', 0);
    }
}
