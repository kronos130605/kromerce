<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'average_rating',
        'total_ratings',
        'five_star_count',
        'four_star_count',
        'three_star_count',
        'two_star_count',
        'one_star_count',
        'total_orders',
        'completed_orders',
        'cancelled_orders',
        'total_revenue',
        'average_order_value',
        'order_fulfillment_rate',
        'on_time_delivery_rate',
        'response_rate',
        'average_response_time',
        'total_customers',
        'repeat_customers',
        'customer_retention_rate',
        'total_products',
        'active_products',
        'out_of_stock_products',
        'total_disputes',
        'resolved_disputes',
        'dispute_resolution_rate',
        'last_calculated_at',
    ];

    protected $casts = [
        'average_rating' => 'decimal:2',
        'total_ratings' => 'integer',
        'five_star_count' => 'integer',
        'four_star_count' => 'integer',
        'three_star_count' => 'integer',
        'two_star_count' => 'integer',
        'one_star_count' => 'integer',
        'total_orders' => 'integer',
        'completed_orders' => 'integer',
        'cancelled_orders' => 'integer',
        'total_revenue' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'order_fulfillment_rate' => 'decimal:2',
        'on_time_delivery_rate' => 'decimal:2',
        'response_rate' => 'decimal:2',
        'average_response_time' => 'decimal:2',
        'total_customers' => 'integer',
        'repeat_customers' => 'integer',
        'customer_retention_rate' => 'decimal:2',
        'total_products' => 'integer',
        'active_products' => 'integer',
        'out_of_stock_products' => 'integer',
        'total_disputes' => 'integer',
        'resolved_disputes' => 'integer',
        'dispute_resolution_rate' => 'decimal:2',
        'last_calculated_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
