<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes');
    }

    public function purchases()
    {
        return $this->hasManyThrough(Item::class, Purchase::class, 'buyer_id', 'id', 'id', 'item_id');
    }
}
