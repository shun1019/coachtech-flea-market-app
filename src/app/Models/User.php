<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'email_verified_at',
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
        return $this->hasMany(Purchase::class, 'buyer_id');
    }

    public function receivedRatings()
    {
        return $this->hasManyThrough(
            Rating::class,
            Trade::class,
            'buyer_id',
            'trade_id',
            'id',
            'id'
        )->where('rater_id', '!=', $this->id)
            ->orWhereHas('trade', function ($query) {
                $query->where('seller_id', $this->id);
            });
    }

    public function getAverageRatingAttribute()
    {
        $ratings = Rating::whereHas('trade', function ($query) {
            $query->where('buyer_id', $this->id)
                ->orWhere('seller_id', $this->id);
        })->where('rater_id', '!=', $this->id)->pluck('rate');

        if ($ratings->isEmpty()) {
            return null;
        }

        return round($ratings->average());
    }
}
