<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function isSold(): bool
    {
        return $this->purchase()->exists();
    }

    public function getImageUrlAttribute(): string
    {
        $path = (string) ($this->image_path ?? '');

        if ($path === '') return '';

        // すでにURLならそのまま（S3など）
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // public/xxxx の場合は asset
        if (Str::startsWith($path, 'public/')) {
            return asset(Str::after($path, 'public/'));
        }

        // storage にある想定
        return Storage::url($path);
    }
}
