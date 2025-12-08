<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'notes',
        'last_purchase_at',
        'archived_at',
    ];

    protected $casts = [
        'last_purchase_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
}
