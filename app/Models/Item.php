<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','description','type','sector',
        'sale_price','cost','stock','min_stock','unit',
        'featured','active',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'featured' => 'boolean',
        'active' => 'boolean',
    ];
}
