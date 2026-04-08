<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionRating extends Model
{
    protected $guarded = ['id'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function ratee()
    {
        return $this->belongsTo(User::class, 'ratee_id');
    }
}