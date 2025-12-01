<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    // Si tu tabla se llama "stock_movements", no hace falta $table.
    // Ponemos fillable para permitir create([...]) desde InventoryService.
    protected $fillable = [
        'item_id',
        'quantity',
        'reason',
        'sale_id',
        'purchase_id',
        'unit_cost',
        'type',
        'ref_id',
    ];

    protected $casts = [
        'item_id'     => 'integer',
        'quantity'    => 'integer', // o 'decimal:2' si usas decimales
        'sale_id'     => 'integer',
        'purchase_id' => 'integer',
        'unit_cost'   => 'integer',
        'ref_id'      => 'integer',
    ];

    // Relaciones opcionales (solo si tienes estas tablas)
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
