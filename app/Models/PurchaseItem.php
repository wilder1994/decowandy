<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PurchaseItem extends Model
{
    use HasFactory;


    protected $fillable = [
        'purchase_id',
        'product_name',
        'quantity',
        'total_cost',
        'unit_cost',
        'item_id'
    ];


    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }


    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
