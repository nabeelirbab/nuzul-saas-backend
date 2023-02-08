<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderProduct extends Pivot
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_original_price',
        'product_sale_price',
        'discount_percentage',
        'discount_amount',
        'product_tax_percentage',
        'product_tax_amount',
        'total_amount_without_tax',
        'total_amount_with_tax',
    ];
}
