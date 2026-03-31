<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'date',
        'page_views',
        'unique_visitors',
        'product_views',
        'orders',
        'revenue',
        'average_order_value',
        'conversion_rate',
        'add_to_cart',
        'abandoned_carts',
        'new_customers',
        'returning_customers',
    ];

    protected $casts = [
        'date' => 'date',
        'page_views' => 'integer',
        'unique_visitors' => 'integer',
        'product_views' => 'integer',
        'orders' => 'integer',
        'revenue' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'add_to_cart' => 'integer',
        'abandoned_carts' => 'integer',
        'new_customers' => 'integer',
        'returning_customers' => 'integer',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
