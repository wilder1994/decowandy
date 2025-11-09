<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_low_stock_uses_item_min_stock_when_threshold_is_missing(): void
    {
        $this->authenticate();

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
            'min_stock' => 5,
        ]);

        Stock::create([
            'item_id' => $item->id,
            'quantity' => 3,
            'min_threshold' => null,
        ]);

        $response = $this->getJson(route('api.stocks.low'));

        $response->assertOk()->assertJson(function (AssertableJson $json) use ($item) {
            $json->has(1)
                ->where('0.item_id', $item->id)
                ->where('0.name', $item->name)
                ->where('0.sector', $item->sector)
                ->where('0.quantity', fn ($value) => (int) $value === 3)
                ->where('0.min_threshold', fn ($value) => (int) $value === 5);
        });
    }

    public function test_low_stock_prefers_stock_specific_threshold(): void
    {
        $this->authenticate();

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'impresion',
            'min_stock' => 10,
        ]);

        Stock::create([
            'item_id' => $item->id,
            'quantity' => 4,
            'min_threshold' => 6,
        ]);

        // Un item que no debería aparecer porque está sobre el umbral
        $other = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
            'min_stock' => 2,
        ]);

        Stock::create([
            'item_id' => $other->id,
            'quantity' => 5,
            'min_threshold' => 1,
        ]);

        $response = $this->getJson(route('api.stocks.low'));

        $response->assertOk()->assertJson(function (AssertableJson $json) use ($item) {
            $json->has(1)
                ->where('0.item_id', $item->id)
                ->where('0.name', $item->name)
                ->where('0.sector', $item->sector)
                ->where('0.quantity', fn ($value) => (int) $value === 4)
                ->where('0.min_threshold', fn ($value) => (int) $value === 6);
        });
    }
}
