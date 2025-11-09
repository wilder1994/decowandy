<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_authenticated_user_can_create_item(): void
    {
        $this->authenticate();

        $payload = [
            'name' => 'Nuevo producto',
            'description' => 'Descripción corta',
            'type' => 'product',
            'sector' => 'diseno',
            'sale_price' => 25000,
            'cost' => 12000,
            'stock' => 10,
            'min_stock' => 2,
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

    public function test_authenticated_user_can_update_item(): void
    {
        $this->authenticate();

        $item = Item::factory()->create([
            'name' => 'Original',
            'sector' => 'diseno',
            'type' => 'product',
            'sale_price' => 12000,
            'stock' => 5,
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

    public function test_authenticated_user_can_delete_item(): void
    {
        $this->authenticate();

        $item = Item::factory()->create();

        $response = $this->deleteJson("/api/items/{$item->id}", ['force' => 1]);

        $response->assertOk()->assertJson(['ok' => true, 'deleted' => true]);

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }
}
