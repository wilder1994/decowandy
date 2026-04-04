<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'cost' => 'decimal:2',
        'featured' => 'boolean',
        'active' => 'boolean',
    ];

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'item_id');
    }

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

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function hasProtectedHistory(): bool
    {
        return $this->saleItems()->exists()
            || $this->stockMovements()->exists()
            || $this->purchaseItems()->exists();
    }
}
