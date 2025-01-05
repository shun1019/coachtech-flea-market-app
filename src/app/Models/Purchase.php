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
        'item_id',         // 購入された商品のID
        'buyer_id',        // 購入者のID
        'purchase_price',  // 購入金額
        'payment_method',  // 支払い方法（例: カード、コンビニ払い）
        'address_id',      // 配送先のアドレスID
        'purchase_status', // 購入の状態（例: pending, completed）
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
