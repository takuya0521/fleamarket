<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseMessage extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'edited_at' => 'datetime',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getImageUrlAttribute(): string
    {
        $path = (string)($this->image_path ?? '');
        if ($path === '') return '';

        if (Str::startsWith($path, ['http://', 'https://'])) return $path;
        if (Str::startsWith($path, 'public/')) return asset(Str::after($path, 'public/'));

        return Storage::url($path);
    }
}