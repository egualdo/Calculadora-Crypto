<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'currency_pair',
        'buy_price',
        'sell_price',
        'average_price',
        'metadata',
        'last_updated'
    ];

    protected $casts = [
        'buy_price' => 'decimal:4',
        'sell_price' => 'decimal:4',
        'average_price' => 'decimal:4',
        'metadata' => 'array',
        'last_updated' => 'datetime'
    ];

    public function scopeLatestRates($query)
    {
        return $query->whereIn('id', function ($subquery) {
            $subquery->selectRaw('MAX(id)')
                ->from('exchange_rates')
                ->groupBy('type', 'currency_pair');
        });
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
