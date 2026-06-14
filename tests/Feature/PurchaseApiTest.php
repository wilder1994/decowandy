<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_to_inventory_creates_stock_entries(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
            'active' => true,
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
            'reason' => 'purchase',
            'ref_id' => $response->json('purchase_id'),
        ]);
    }

    public function test_purchase_rejects_inactive_inventory_item(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
            'active' => false,
        ]);

        $payload = [
            'date' => now()->toDateString(),
            'category' => 'Papeleria',
            'to_inventory' => true,
            'items' => [
                [
                    'product_name' => 'Producto inactivo',
                    'quantity' => 2,
                    'total_cost' => 8000,
                    'item_id' => $item->id,
                ],
            ],
        ];

        $response = $this->postJson(route('api.purchases.store'), $payload);

        $response->assertStatus(422);
        $this->assertDatabaseCount('purchases', 0);
    }

    public function test_papeleria_purchase_creates_new_item_with_barcode(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('api.purchases.store'), [
            'date' => now()->toDateString(),
            'category' => 'Papelería',
            'supplier' => 'Distribuidora Test',
            'to_inventory' => true,
            'items' => [
                [
                    'product_name' => 'Cuaderno 100 hojas',
                    'quantity' => 10,
                    'total_cost' => 50000,
                    'barcode' => 'DWY-0010',
                    'color' => 'Azul',
                    'sale_price' => 7500,
                ],
            ],
        ]);

        $response->assertCreated()->assertJsonPath('total', 50000);

        $this->assertDatabaseHas('items', [
            'name' => 'Cuaderno 100 hojas',
            'sector' => 'papeleria',
            'barcode' => 'DWY-0010',
            'color' => 'Azul',
        ]);

        $item = Item::where('barcode', 'DWY-0010')->first();
        $this->assertNotNull($item);

        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('purchase_items', [
            'product_name' => 'Cuaderno 100 hojas',
            'item_id' => $item->id,
            'quantity' => 10,
        ]);
    }

    public function test_papeleria_purchase_without_barcode_generates_internal_code(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('api.purchases.store'), [
            'date' => now()->toDateString(),
            'category' => 'Papeleria',
            'to_inventory' => true,
            'items' => [
                [
                    'product_name' => 'Bolígrafo negro',
                    'quantity' => 5,
                    'total_cost' => 10000,
                ],
            ],
        ]);

        $response->assertCreated();

        $item = Item::where('name', 'Bolígrafo negro')->first();
        $this->assertNotNull($item);
        $this->assertStringStartsWith('DWY-', $item->barcode);
        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'quantity' => 5,
        ]);
    }
}
