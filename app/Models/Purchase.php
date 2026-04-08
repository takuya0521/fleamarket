<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'purchased_at' => 'datetime',
        'buyer_completed_at' => 'datetime',
];

public function messages()
{
    return $this->hasMany(\App\Models\PurchaseMessage::class);
}

public function reads()
{
    return $this->hasMany(\App\Models\PurchaseRead::class);
}

public function ratings()
{
    return $this->hasMany(\App\Models\TransactionRating::class);
}

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
