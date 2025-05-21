<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'name',
        'short_description',
        'buying_price',
        'selling_price',
        'supplier',
        'goods_received_voucher',
        'stock_level',
        'min_level',
    ];
}