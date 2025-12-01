<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'sector',
        'sale_price',
        'cost',
        'unit',
        'featured',
        'active',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'cost'       => 'decimal:2',
        'featured'   => 'boolean',
        'active'     => 'boolean',
    ];

    // 👈 RELACIÓN CORRECTA (añadir esto)
    public function stock()
    {
        return $this->hasOne(Stock::class, 'item_id');
    }

    // 👈 Para acceder de forma bonita: $item->quantity
    public function getQuantityAttribute()
    {
        if (array_key_exists('stock', $this->attributes)) {
            return (int) $this->attributes['stock'];
        }

        return $this->stock?->quantity ?? 0;
    }

    public function getMinThresholdAttribute()
    {
        return $this->stock?->min_threshold ?? 0;
    }
}
