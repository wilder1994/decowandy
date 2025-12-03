<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_out_throws_when_not_enough_stock(): void
    {
        $item = Item::factory()->create();
        Stock::create(['item_id' => $item->id, 'quantity' => 1]);

        $service = app(InventoryService::class);

        $this->expectException(RuntimeException::class);
        $service->out($item->id, 3, 'sale', null, true);
    }

    public function test_out_decrements_stock_and_logs_movement(): void
    {
        $item = Item::factory()->create();
        $stock = Stock::create(['item_id' => $item->id, 'quantity' => 5]);
        $service = app(InventoryService::class);

        $service->out($item->id, 2, 'sale', 10, true);

        $stock->refresh();
        $this->assertSame(3, $stock->quantity);

        $movement = StockMovement::where('item_id', $item->id)->first();
        $this->assertNotNull($movement);
        $this->assertSame(2, $movement->quantity);
        $this->assertSame('sale', $movement->reason);
        $this->assertSame(10, $movement->ref_id);
    }
}
