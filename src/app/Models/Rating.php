<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'rater_id',
        'rate',
    ];

    public function trade()
    {
        return $this->belongsTo(Trade::class);
    }

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }
}
