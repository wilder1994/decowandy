<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name','slug','description','type','sector',
        'sale_price','cost','stock','min_stock','unit',
        'featured','active',
    ];
}
