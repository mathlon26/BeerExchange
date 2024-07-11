<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'market_price',
        'bottom_price',
        'upper_price',
        'cost_price',
        'in_discount',
        'pumping',
        'dumping',
        'allow_discount',
        'allow_autocrash',
        'allow_manualcrash',
        'amount_sold',
        'transactions',
        'price_history',
        'market_id',
    ];

    public function market()
    {
        return $this->belongsTo(Market::class);
    }
}
