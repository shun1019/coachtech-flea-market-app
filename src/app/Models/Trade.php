<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Rating;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'seller_id',
        'status',
        'completed_at',
    ];

    /**
     * 関連する商品とのリレーション
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 購入者とのリレーション
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * 出品者とのリレーション
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * 関連するチャットメッセージとのリレーション
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    /**
     * 最新のチャットメッセージ
     */
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /**
     * 評価とのリレーション
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * 両者から評価されたか判定
     */
    public function isFullyRated()
    {
        return $this->ratings()->count() === 2;
    }
}
