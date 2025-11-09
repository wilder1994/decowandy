<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_index_filters_by_category_and_calculates_totals(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $designItem = Item::factory()->create(['sector' => 'diseno', 'sale_price' => 15000]);
        $stationeryItem = Item::factory()->create(['sector' => 'papeleria', 'sale_price' => 1000]);

        $sale = Sale::create([
            'sale_code' => 'DW-000001',
            'sold_at' => '2025-01-10 10:00:00',
            'date' => '2025-01-10',
            'time' => '10:00:00',
            'user_id' => $user->id,
            'payment_method' => 'cash',
            'amount_received' => 16000,
            'total' => 16000,
            'change_due' => 0,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'item_id' => $designItem->id,
            'category' => 'diseno',
            'quantity' => 1,
            'unit_price' => 15000,
            'line_total' => 15000,
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'item_id' => $stationeryItem->id,
            'category' => 'papeleria',
            'quantity' => 1,
            'unit_price' => 1000,
            'line_total' => 1000,
        ]);

        $response = $this->get(route('sales.index', [
            'category' => 'papeleria',
            'date_type' => 'month',
            'month' => '2025-01',
        ]));

        $response->assertStatus(200);

        $sections = $response->viewData('sections');
        $this->assertArrayHasKey('papeleria', $sections);
        $this->assertSame(1000, $sections['papeleria']['total']);
        $this->assertCount(1, $sections['papeleria']['rows']);
        $this->assertArrayNotHasKey('diseno', $sections);

        $this->assertSame(1000, $response->viewData('overallTotal'));
    }

    public function test_sales_index_filters_by_month(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $designItem = Item::factory()->create(['sector' => 'diseno', 'sale_price' => 20000]);

        $januarySale = Sale::create([
            'sale_code' => 'DW-000001',
            'sold_at' => '2025-01-05 09:00:00',
            'date' => '2025-01-05',
            'time' => '09:00:00',
            'user_id' => $user->id,
            'payment_method' => 'transfer',
            'amount_received' => 20000,
            'total' => 20000,
            'change_due' => 0,
        ]);

        SaleItem::create([
            'sale_id' => $januarySale->id,
            'item_id' => $designItem->id,
            'category' => 'diseno',
            'quantity' => 2,
            'unit_price' => 10000,
            'line_total' => 20000,
        ]);

        $februarySale = Sale::create([
            'sale_code' => 'DW-000002',
            'sold_at' => '2025-02-10 11:30:00',
            'date' => '2025-02-10',
            'time' => '11:30:00',
            'user_id' => $user->id,
            'payment_method' => 'card',
            'amount_received' => 15000,
            'total' => 15000,
            'change_due' => 0,
        ]);

        SaleItem::create([
            'sale_id' => $februarySale->id,
            'item_id' => $designItem->id,
            'category' => 'diseno',
            'quantity' => 1,
            'unit_price' => 15000,
            'line_total' => 15000,
        ]);

        $response = $this->get(route('sales.index', [
            'category' => 'all',
            'date_type' => 'month',
            'month' => '2025-01',
        ]));

        $response->assertStatus(200);

        $sections = $response->viewData('sections');
        $this->assertArrayHasKey('diseno', $sections);
        $this->assertSame(20000, $sections['diseno']['total']);
        $this->assertCount(1, $sections['diseno']['rows']);
        $this->assertSame(20000, $response->viewData('overallTotal'));
    }
}
