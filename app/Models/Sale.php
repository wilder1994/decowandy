<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'sale_code', 'sold_at', 'date', 'time', 'user_id',
        'customer_name', 'customer_email', 'customer_phone',
        'subtotal', 'discount', 'taxes', 'total',
        'amount_received', 'change_due', 'payment_method',
        'invoice_pdf_path', 'invoice_img_path', 'notes',
    ];

    protected $casts = [
        'sold_at' => 'datetime',
    ];

    /** Una venta tiene muchos ítems */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /** Usuario que realizó la venta */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
