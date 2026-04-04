<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_does_not_render_legacy_whatsapp_placeholders_or_broken_category_anchors(): void
    {
        $response = $this->get(route('welcome'));

        $response->assertOk();
        $response->assertDontSee('TU_NUMERO');
        $response->assertDontSee('57XXXXXXXXXX');
        $response->assertDontSee('href="#papeleria"', false);
        $response->assertDontSee('href="#impresion"', false);
        $response->assertDontSee('href="#diseno"', false);
        $response->assertDontSee(route('catalog.category', 'papeleria') . '#papeleria-full', false);
    }

    public function test_category_page_uses_contact_fallback_when_whatsapp_is_not_configured(): void
    {
        DB::table('catalog_items')->insert([
            'category' => 'Papelería',
            'title' => 'Cartulina Iris',
            'description' => 'Producto demo',
            'price' => 5000,
            'show_price' => 1,
            'visible' => 1,
            'featured' => 0,
            'sort_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('catalog.category', 'papeleria'));

        $response->assertOk();
        $response->assertDontSee('TU_NUMERO');
        $response->assertDontSee('57XXXXXXXXXX');
        $response->assertSee('href="#contacto"', false);
    }
}
