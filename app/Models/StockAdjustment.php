<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'part_id',
        'adjustment',
        'price_per_unit',
        'reason',
    ];

    protected $table = 'stock_adjustments';
}
