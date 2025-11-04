<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CatalogItem extends Model
{
    protected $fillable = [
        'category', 'title', 'description', 'price',
        'show_price', 'visible', 'sort_order', 'image_path', 'item_id',
    ];

    protected $casts = [
        'show_price' => 'boolean',
        'visible'    => 'boolean',
        'price'      => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /* Scopes útiles */
    public function scopeVisible(Builder $q): Builder
    {
        return $q->where('visible', true);
    }

    public function scopeByCategory(Builder $q, string $cat): Builder
    {
        return $q->where('category', $cat);
    }

    public function scopeSorted(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }
}
