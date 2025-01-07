<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * フィールドの一括代入を許可
     */
    protected $fillable = [
        'item_id',
        'buyer_id',
        'purchase_price',
        'payment_method',
        'address_id',
        'purchase_status',
    ];

    /**
     * デフォルト値の設定
     */
    protected $attributes = [
        'purchase_status' => 'pending',
    ];

    /**
     * 購入者とのリレーション
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * 購入された商品とのリレーション
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * 配送先アドレスとのリレーション
     */
    public function address()
    {
        return $this->belongsTo(UserProfile::class, 'address_id');
    }
}
