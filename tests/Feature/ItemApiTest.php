<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_item(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Nuevo producto',
            'description' => 'Descripción corta',
            'type' => 'product',
            'sector' => 'diseno',
            'sale_price' => 25000,
            'cost' => 12000,
            'unit' => 'unidad',
            'featured' => true,
            'active' => true,
        ];

        $response = $this->postJson('/api/items', $payload);

        $response->assertCreated()
            ->assertJsonPath('item.name', 'Nuevo producto')
            ->assertJsonPath('item.sector', 'diseno');

        $this->assertDatabaseHas('items', [
            'name' => 'Nuevo producto',
            'sector' => 'diseno',
            'type' => 'product',
        ]);
    }

    public function test_creating_stockable_item_records_initial_adjustment_movement(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Producto con stock',
            'description' => 'Controlado',
            'type' => 'product',
            'sector' => 'impresion',
            'sale_price' => 18000,
            'cost' => 9000,
            'stock' => 7,
            'min_stock' => 2,
            'unit' => 'unidad',
            'featured' => false,
            'active' => true,
        ];

        $response = $this->postJson('/api/items', $payload);

        $itemId = $response->json('item.id');

        $response->assertCreated()
            ->assertJsonPath('item.stock', 7)
            ->assertJsonPath('item.min_stock', 2);

        $this->assertDatabaseHas('stocks', [
            'item_id' => $itemId,
            'quantity' => 7,
            'min_threshold' => 2,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $itemId,
            'type' => 'in',
            'quantity' => 7,
            'reason' => 'adjustment',
            'notes' => 'Stock inicial registrado desde el editor de items.',
        ]);

        $this->assertSame(1, StockMovement::where('item_id', $itemId)->count());
    }

    public function test_authenticated_user_can_update_item(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'name' => 'Original',
            'sector' => 'diseno',
            'type' => 'product',
            'sale_price' => 12000,
        ]);

        $payload = [
            'name' => 'Actualizado',
            'sale_price' => 18000,
            'type' => 'service',
            'sector' => 'papeleria',
            'active' => false,
        ];

        $response = $this->putJson("/api/items/{$item->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('item.name', 'Actualizado')
            ->assertJsonPath('item.sector', 'papeleria')
            ->assertJsonPath('item.active', false);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Actualizado',
            'sector' => 'papeleria',
            'type' => 'service',
            'active' => false,
        ]);
    }

    public function test_updating_stockable_item_records_adjustment_delta_only(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'diseno',
            'active' => true,
        ]);

        Stock::create([
            'item_id' => $item->id,
            'quantity' => 5,
            'min_threshold' => 1,
        ]);

        $response = $this->putJson("/api/items/{$item->id}", [
            'stock' => 8,
            'min_stock' => 3,
        ]);

        $response->assertOk()
            ->assertJsonPath('item.stock', 8)
            ->assertJsonPath('item.min_stock', 3);

        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'quantity' => 8,
            'min_threshold' => 3,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 3,
            'reason' => 'adjustment',
            'notes' => 'Ajuste manual registrado desde el editor de items.',
        ]);

        $this->assertSame(1, StockMovement::where('item_id', $item->id)->count());
    }

    public function test_updating_item_without_stock_does_not_create_extra_adjustment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'diseno',
            'name' => 'Antes',
        ]);

        Stock::create([
            'item_id' => $item->id,
            'quantity' => 4,
            'min_threshold' => 1,
        ]);

        StockMovement::create([
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => 4,
            'reason' => 'adjustment',
            'notes' => 'Stock inicial registrado desde el editor de items.',
        ]);

        $response = $this->putJson("/api/items/{$item->id}", [
            'name' => 'Despues',
            'min_stock' => 3,
        ]);

        $response->assertOk()
            ->assertJsonPath('item.name', 'Despues')
            ->assertJsonPath('item.stock', 4)
            ->assertJsonPath('item.min_stock', 3);

        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'quantity' => 4,
            'min_threshold' => 3,
        ]);

        $this->assertSame(1, StockMovement::where('item_id', $item->id)->count());
    }

    public function test_updating_product_to_service_resets_inventory_traceably(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
        ]);

        Stock::create([
            'item_id' => $item->id,
            'quantity' => 6,
            'min_threshold' => 2,
        ]);

        $response = $this->putJson("/api/items/{$item->id}", [
            'type' => 'service',
        ]);

        $response->assertOk()
            ->assertJsonPath('item.type', 'service')
            ->assertJsonPath('item.stock', 0)
            ->assertJsonPath('item.min_stock', 0);

        $this->assertDatabaseHas('stocks', [
            'item_id' => $item->id,
            'quantity' => 0,
            'min_threshold' => 0,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'item_id' => $item->id,
            'type' => 'out',
            'quantity' => 6,
            'reason' => 'adjustment',
            'notes' => 'Conversion del item a servicio. Inventario ajustado a cero.',
        ]);
    }

    public function test_authenticated_user_can_deactivate_item(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create(['active' => true]);

        $response = $this->deleteJson("/api/items/{$item->id}");

        $response->assertOk()
            ->assertJson([
                'ok' => true,
                'active' => false,
                'deleted' => false,
            ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'active' => false,
        ]);
    }

    public function test_authenticated_user_can_force_delete_item_without_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();

        $response = $this->deleteJson("/api/items/{$item->id}", ['force' => 1]);

        $response->assertOk()->assertJson(['ok' => true, 'deleted' => true]);

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_authenticated_user_cannot_force_delete_item_with_sales_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create();
        $sale = Sale::create([
            'sale_code' => 'SALE-TEST-001',
            'sold_at' => now(),
            'date' => now()->toDateString(),
            'time' => now()->format('H:i:s'),
            'user_id' => $user->id,
            'subtotal' => 7000,
            'total' => 7000,
            'amount_received' => 7000,
            'change_due' => 0,
            'payment_method' => 'cash',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'item_id' => $item->id,
            'description' => $item->name,
            'quantity' => 1,
            'unit_price' => 7000,
            'line_total' => 7000,
        ]);

        $response = $this->deleteJson("/api/items/{$item->id}", ['force' => 1]);

        $response->assertStatus(409)
            ->assertJsonPath('ok', false);

        $this->assertDatabaseHas('items', ['id' => $item->id]);
        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'item_id' => $item->id,
        ]);
    }
}
