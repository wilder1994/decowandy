<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'item_id', 'description', 'quantity', 'unit_price', 'line_total', 'category', 'sheets_used',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'integer',
        'line_total' => 'integer',
        'sheets_used' => 'integer',
    ];

    /** Ítem vendido */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** Venta a la que pertenece */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
