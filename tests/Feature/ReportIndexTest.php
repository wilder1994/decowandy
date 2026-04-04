<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_filter_keeps_report_metrics_consistent(): void
    {
        $user = User::factory()->create();

        $papeleria = Item::factory()->create([
            'sector' => 'papeleria',
            'type' => 'product',
            'cost' => 1000,
        ]);

        $diseno = Item::factory()->create([
            'sector' => 'diseno',
            'type' => 'service',
            'cost' => 2000,
        ]);

        $saleMixed = Sale::create([
            'sale_code' => 'DW-REPORT-001',
            'sold_at' => now()->startOfDay()->addHours(10),
            'date' => now()->toDateString(),
            'time' => '10:00:00',
            'user_id' => $user->id,
            'customer_name' => 'Cliente mixto',
            'payment_method' => 'cash',
            'amount_received' => 10000,
            'total' => 10000,
            'change_due' => 0,
        ]);

        SaleItem::create([
            'sale_id' => $saleMixed->id,
            'item_id' => $papeleria->id,
            'description' => $papeleria->name,
            'quantity' => 1,
            'unit_price' => 4000,
            'line_total' => 4000,
            'category' => 'papeleria',
        ]);

        SaleItem::create([
            'sale_id' => $saleMixed->id,
            'item_id' => $diseno->id,
            'description' => $diseno->name,
            'quantity' => 1,
            'unit_price' => 6000,
            'line_total' => 6000,
            'category' => 'diseno',
        ]);

        $saleOnlyDiseno = Sale::create([
            'sale_code' => 'DW-REPORT-002',
            'sold_at' => now()->startOfDay()->addHours(12),
            'date' => now()->toDateString(),
            'time' => '12:00:00',
            'user_id' => $user->id,
            'customer_name' => 'Cliente diseno',
            'payment_method' => 'transfer',
            'amount_received' => 7000,
            'total' => 7000,
            'change_due' => 0,
        ]);

        SaleItem::create([
            'sale_id' => $saleOnlyDiseno->id,
            'item_id' => $diseno->id,
            'description' => $diseno->name,
            'quantity' => 1,
            'unit_price' => 7000,
            'line_total' => 7000,
            'category' => 'diseno',
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', [
            'from' => now()->toDateString(),
            'to' => now()->toDateString(),
            'category' => 'papeleria',
        ]));

        $response->assertOk();
        $response->assertViewHas('totales', fn (array $totales) => $totales['ingresos'] === 4000 && $totales['cogs'] === 1000);
        $response->assertViewHas('resumen', fn (array $resumen) => $resumen['ingresos'] === 4000);
        $response->assertViewHas('cashflowDataset', fn (array $dataset) => $dataset['entradas'] === [4000]);
        $response->assertViewHas('ventasListado', function ($ventasListado) use ($saleMixed, $saleOnlyDiseno) {
            return $ventasListado->count() === 1
                && $ventasListado->first()->sale_code === $saleMixed->sale_code
                && $ventasListado->first()->total === 4000
                && $ventasListado->first()->items_count === 1
                && $ventasListado->contains(fn ($sale) => $sale->sale_code === $saleOnlyDiseno->sale_code) === false;
        });
    }
}
