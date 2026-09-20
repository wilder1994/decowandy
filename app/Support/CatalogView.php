<?php

namespace App\Support;

use App\Models\CatalogCategorySetting;
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

    public static function sectorForCategory(string $categoryName): ?string
    {
        $meta = static::definitions()->get($categoryName);

        return $meta['slug'] ?? null;
    }

    public static function coversBySlug(): Collection
    {
        return CatalogCategorySetting::query()
            ->get()
            ->keyBy('slug')
            ->map(fn (CatalogCategorySetting $row) => $row->coverUrl());
    }

    /**
     * Attach database items and cover images to the category definitions.
     */
    public static function compose(Collection $items): Collection
    {
        $covers = static::coversBySlug();

        return static::definitions()->map(function (array $meta, string $category) use ($items, $covers) {
            $collection = $items->where('category', $category)->values();

            return $meta + [
                'key' => $category,
                'items' => $collection,
                'cover_image' => $covers->get($meta['slug'] ?? '') ?: null,
            ];
        })->values();
    }
}
