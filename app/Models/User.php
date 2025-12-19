<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building',
        'profile_image_path',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function items()
    {
        return $this->hasMany(\App\Models\Item::class, 'seller_id');
    }

    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class);
    }

    public function likedItems()
    {
        return $this->belongsToMany(\App\Models\Item::class, 'likes')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    public function purchases()
    {
        return $this->hasMany(\App\Models\Purchase::class, 'buyer_id');
    }

}
