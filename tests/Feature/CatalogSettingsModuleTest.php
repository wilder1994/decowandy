<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogSettingsModuleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function papeleriaItem(string $name = 'Cuaderno'): Item
    {
        $item = Item::query()->create([
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name).'-'.uniqid(),
            'type' => 'product',
            'sector' => 'papeleria',
            'sale_price' => 2500,
            'cost' => 1000,
            'active' => true,
        ]);

        Stock::query()->create([
            'item_id' => $item->id,
            'quantity' => 4,
            'min_threshold' => 1,
        ]);

        return $item;
    }

    public function test_admin_can_link_inventory_item_to_public_catalog(): void
    {
        $admin = $this->admin();
        $item = $this->papeleriaItem();

        $response = $this->actingAs($admin)->postJson(route('catalog.store'), [
            'category' => 'Papelería',
            'item_id' => $item->id,
            'show_price' => '1',
            'visible' => '1',
            'featured' => '0',
        ]);

        $response->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseHas('catalog_items', [
            'category' => 'Papelería',
            'item_id' => $item->id,
            'title' => $item->name,
            'price' => 2500,
        ]);
    }

    public function test_public_category_shows_stock_quantity(): void
    {
        $item = $this->papeleriaItem('Marcador');
        DB::table('catalog_items')->insert([
            'category' => 'Papelería',
            'title' => $item->name,
            'description' => null,
            'price' => 2500,
            'show_price' => 1,
            'visible' => 1,
            'featured' => 0,
            'sort_order' => 1,
            'image_path' => null,
            'item_id' => $item->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('catalog.category', 'papeleria'));
        $response->assertOk();
        $response->assertSee('4 disponibles', false);
        $response->assertSee('Marcador', false);
    }

    public function test_admin_can_upload_and_clear_category_cover(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $file = UploadedFile::fake()->image('portada.jpg', 800, 400);

        $upload = $this->actingAs($admin)->postJson(route('catalog.cover.update', 'papeleria'), [
            'cover' => $file,
        ]);
        $upload->assertOk()->assertJsonPath('ok', true);
        $this->assertNotEmpty($upload->json('cover_image'));
        $this->assertDatabaseHas('catalog_category_settings', ['slug' => 'papeleria']);

        $clear = $this->actingAs($admin)->deleteJson(route('catalog.cover.destroy', 'papeleria'));
        $clear->assertOk()->assertJsonPath('cover_image', null);
    }

    public function test_inventory_options_exclude_already_linked_items(): void
    {
        $admin = $this->admin();
        $a = $this->papeleriaItem('A');
        $b = $this->papeleriaItem('B');

        DB::table('catalog_items')->insert([
            'category' => 'Papelería',
            'title' => $a->name,
            'price' => 2500,
            'show_price' => 1,
            'visible' => 1,
            'featured' => 0,
            'sort_order' => 1,
            'item_id' => $a->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->getJson(route('catalog.inventory-options', [
            'category' => 'Papelería',
            'q' => '',
        ]));

        $response->assertOk();
        $ids = collect($response->json('items'))->pluck('id');
        $this->assertFalse($ids->contains($a->id));
        $this->assertTrue($ids->contains($b->id));
    }
}
