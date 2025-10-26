<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Purchase extends Model
{
    use HasFactory;


    protected $fillable = [
        'category',
        'date',
        'supplier',
        'note',
        'to_inventory',
        'total'
    ];


    protected $casts = [
        'to_inventory' => 'boolean',
        'date' => 'date',
    ];


    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
