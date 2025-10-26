<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CatalogItem extends Model
{
    use HasFactory;


    protected $fillable = [
        'category',
        'title',
        'description',
        'price',
        'show_price',
        'visible',
        'featured',
        'sort_order',
        'image_path',
        'item_id'
    ];


    protected $casts = [
        'show_price' => 'boolean',
        'visible' => 'boolean',
        'featured' => 'boolean',
    ];


    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
