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
        return collect(config('decowandy.catalog_categories'));
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
