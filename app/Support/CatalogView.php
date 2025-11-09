<?php

namespace App\Support;

use Illuminate\Support\Collection;

class CatalogView
{
    /**
     * Base definitions for catalog categories.
     */
    public static function definitions(): Collection
    {
        return collect([
            'Papelería' => [
                'name'            => 'Papelería',
                'slug'            => 'papeleria',
                'card_summary'    => 'Cuadernos, sobres, papel bond, cartulinas y más.',
                'card_background' => 'linear-gradient(135deg, var(--dw-lilac), #f6f1ff)',
                'cta_label'       => 'Ver productos',
                'tag_empty'       => 'Aún no hay productos en esta categoría.',
                'list_empty'      => 'No hay productos de papelería aún.',
            ],
            'Impresión' => [
                'name'            => 'Impresión',
                'slug'            => 'impresion',
                'card_summary'    => 'Copias B/N y color, escaneo, fotos, anillados.',
                'card_background' => 'linear-gradient(135deg, #f1ecff, var(--dw-lilac))',
                'cta_label'       => 'Ver servicios',
                'tag_empty'       => 'Aún no hay servicios de impresión.',
                'list_empty'      => 'No hay servicios de impresión aún.',
            ],
            'Diseño' => [
                'name'            => 'Diseño',
                'slug'            => 'diseno',
                'card_summary'    => 'Logos, tarjetas y piezas gráficas a medida.',
                'card_background' => 'linear-gradient(135deg, var(--dw-primary), var(--dw-lilac))',
                'cta_label'       => 'Ver portafolio',
                'tag_empty'       => 'Pronto añadiremos productos…',
                'list_empty'      => 'No hay diseños cargados.',
            ],
        ]);
    }

    /**
     * Attach database items to the category definitions.
     */
    public static function compose(Collection $items): Collection
    {
        return static::definitions()->map(function (array $meta, string $category) use ($items) {
            $collection = $items->where('category', $category)->values();

            return $meta + [
                'key'   => $category,
                'items' => $collection,
            ];
        })->values();
    }
}
