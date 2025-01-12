<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'condition',
        'image',
        'status',
        'like_count',
        'comments_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likesByUsers()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
