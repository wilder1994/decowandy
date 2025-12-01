<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class InventoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_uses_item_min_stock_when_threshold_is_missing(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'papeleria',
        ]);

        Stock::create([
            'item_id' => $item->id,
            'quantity' => 3,
            'min_threshold' => 5,
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
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'type' => 'product',
            'sector' => 'impresion',
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
