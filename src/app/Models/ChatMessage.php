<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trade_id',
        'body',
        'image_path',
        'read_at',
    ];

    /**
     * 送信ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 関連する取引とのリレーション
     */
    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }
}
