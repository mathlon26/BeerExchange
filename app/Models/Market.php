<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_session_id',
        'name',
        'start_time',
        'end_time',
        'profit',
        'unit',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function drinks()
    {
        return $this->hasMany(Drink::class);
    }
}
