<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCatalogListTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_user_can_export_catalog_items_by_sector(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        Item::factory()->create([
            'sector' => 'papeleria',
            'name' => 'Bolígrafo azul',
            'barcode' => 'DWY-0012',
            'color' => 'Azul',
            'sale_price' => 2500,
            'active' => true,
        ]);

        Item::factory()->create([
            'sector' => 'diseno',
            'name' => 'Logo vector',
            'active' => true,
        ]);

        $response = $this->getJson('/api/items/catalog-export?sector=papeleria');

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('sector', 'papeleria')
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Bolígrafo azul', 'barcode' => 'DWY-0012']);
    }

    public function test_catalog_list_pdf_requires_selected_items(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $this->postJson('/api/items/catalog-list/pdf', [
            'sector' => 'papeleria',
        ])->assertStatus(422);
    }

    public function test_catalog_list_pdf_returns_pdf_for_selected_items(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'sector' => 'papeleria',
            'name' => 'Resaltador',
            'barcode' => 'DWY-0044',
            'color' => 'Amarillo',
            'sale_price' => 3200,
            'active' => true,
        ]);

        $response = $this->postJson('/api/items/catalog-list/pdf', [
            'sector' => 'papeleria',
            'item_ids' => [$item->id],
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_catalog_list_pdf_rejects_items_from_other_sector(): void
    {
        $user = User::factory()->staffInventory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create([
            'sector' => 'diseno',
            'name' => 'Tarjeta visita',
            'active' => true,
        ]);

        $this->postJson('/api/items/catalog-list/pdf', [
            'sector' => 'papeleria',
            'item_ids' => [$item->id],
        ])->assertStatus(422);
    }
}
