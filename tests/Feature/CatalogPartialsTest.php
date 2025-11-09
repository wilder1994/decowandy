<?php

namespace Tests\Feature;

use Illuminate\Support\Collection;
use Tests\TestCase;

class CatalogPartialsTest extends TestCase
{
    private function makeItem(array $overrides = []): object
    {
        return (object) array_merge([
            'title'       => 'Producto',
            'description' => 'Descripción',
            'price'       => 12000,
            'show_price'  => 1,
            'image_path'  => null,
        ], $overrides);
    }

    public function test_category_card_partial_lists_first_items()
    {
        $category = [
            'name'            => 'Papelería',
            'slug'            => 'papeleria',
            'card_summary'    => 'Resumen',
            'card_background' => 'bg',
            'cta_label'       => 'Ver todo',
            'tag_empty'       => 'Vacío',
            'items'           => new Collection(array_map(fn ($i) => $this->makeItem(['title' => 'Item '.$i]), range(1, 6))),
        ];

        $html = view('welcome.partials.category-card', ['category' => $category])->render();

        $this->assertStringContainsString('Papelería', $html);
        $this->assertStringContainsString('#papeleria-full', $html);
        $this->assertStringContainsString('Item 1', $html);
        $this->assertStringContainsString('Item 5', $html);
        $this->assertStringNotContainsString('Item 6', $html, 'La vista previa solo debe mostrar los primeros 5 ítems.');
    }

    public function test_category_card_partial_shows_empty_message()
    {
        $category = [
            'name'      => 'Impresión',
            'slug'      => 'impresion',
            'tag_empty' => 'Sin servicios.',
            'items'     => new Collection(),
        ];

        $html = view('welcome.partials.category-card', ['category' => $category])->render();

        $this->assertStringContainsString('Sin servicios.', $html);
    }

    public function test_category_list_partial_renders_catalog_information()
    {
        $category = [
            'name'       => 'Diseño',
            'slug'       => 'diseno',
            'list_empty' => 'Nada cargado',
            'items'      => new Collection([
                $this->makeItem(['title' => 'Identidad Visual', 'price' => 55000, 'show_price' => 1]),
                $this->makeItem(['title' => 'Presentación', 'show_price' => 0]),
            ]),
        ];

        $html = view('welcome.partials.category-list', ['category' => $category])->render();

        $this->assertStringContainsString('Diseño', $html);
        $this->assertStringContainsString('Identidad Visual', $html);
        $this->assertStringContainsString('55.000', $html);
        $this->assertStringContainsString('https://wa.me/57XXXXXXXXXX?text=Hola%2C+me+interesa%3A+Identidad+Visual', $html);
        $this->assertStringContainsString('$ —', $html);
        $this->assertStringNotContainsString('Nada cargado', $html);
    }
}
