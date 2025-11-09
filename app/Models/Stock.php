<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'quantity',
        'min_threshold',
    ];

    protected $casts = [
        'item_id' => 'integer',
        'quantity' => 'integer',
        'min_threshold' => 'integer',
    ];
}
