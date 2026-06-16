<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Services\ItemLabelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemLabelSheetTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_user_can_search_label_candidates(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        Item::factory()->create([
            'sector' => 'papeleria',
            'name' => 'Cuaderno espiral',
            'barcode' => 'DWY-0099',
            'active' => true,
        ]);

        $response = $this->getJson('/api/items/labels/candidates?search=cuaderno');

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonFragment(['barcode' => 'DWY-0099']);
    }

    public function test_label_sheet_preview_requires_lines(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $this->postJson('/api/items/labels/preview', [])
            ->assertStatus(422);
    }

    public function test_label_sheet_preview_returns_pdf_with_quantities(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'sector' => 'papeleria',
            'barcode' => 'DWY-0007',
            'active' => true,
        ]);

        $response = $this->postJson('/api/items/labels/preview', [
            'lines' => [
                ['item_id' => $item->id, 'quantity' => 2],
            ],
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_label_sheet_rejects_more_than_max_labels(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'sector' => 'papeleria',
            'barcode' => 'DWY-0200',
            'active' => true,
        ]);

        $this->postJson('/api/items/labels/sheet', [
            'lines' => [
                ['item_id' => $item->id, 'quantity' => ItemLabelService::MAX_SHEET_LABELS + 1],
            ],
        ])->assertStatus(422);
    }

    public function test_compact_label_png_has_no_empty_barcode_error(): void
    {
        $item = Item::factory()->make([
            'sector' => 'papeleria',
            'name' => 'Resaltador',
            'barcode' => '7702153573116',
        ]);

        $png = app(ItemLabelService::class)->buildCompactLabelPng($item);

        $this->assertNotEmpty($png);
        $this->assertStringStartsWith("\x89PNG", $png);
    }
}
