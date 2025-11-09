<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PurchaseApiTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_purchase_to_inventory_creates_stock_entries(): void
    {
        $this->authenticate();

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
        ]);

        $payload = [
            'date' => now()->toDateString(),
            'category' => 'Papelería',
            'supplier' => 'Proveedor Demo',
            'note' => 'Factura 123',
            'to_inventory' => true,
            'items' => [
                [
                    'product_name' => 'Resma Carta',
                    'quantity' => 3,
                    'total_cost' => 15000,
                    'item_id' => $item->id,
                ],
            ],
        ];

        $response = $this->postJson(route('api.purchases.store'), $payload);

        $response->assertCreated()
            ->assertJsonStructure(['ok', 'purchase_id', 'total'])
            ->assertJsonPath('total', 15000);

        $this->assertDatabaseHas('purchases', [
            'id' => $response->json('purchase_id'),
            'category' => 'Papelería',
            'total' => 15000,
            'to_inventory' => true,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $response->json('purchase_id'),
            'product_name' => 'Resma Carta',
            'quantity' => 3,
            'total_cost' => 15000,
            'unit_cost' => 5000,
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 3,
            'reason' => 'compra',
            'related_id' => $response->json('purchase_id'),
        ]);
    }
}
