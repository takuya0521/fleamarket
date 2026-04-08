<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRead extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}