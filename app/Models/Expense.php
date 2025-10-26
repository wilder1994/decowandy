<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Expense extends Model
{
    use HasFactory;


    protected $fillable = ['date', 'concept', 'category', 'amount', 'note', 'user_id'];


    protected $casts = [
        'date' => 'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
