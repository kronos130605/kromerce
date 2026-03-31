<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreRating extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'user_id',
        'order_id',
        'overall_rating',
        'product_quality_rating',
        'shipping_speed_rating',
        'customer_service_rating',
        'communication_rating',
        'title',
        'comment',
        'verified_purchase',
        'status',
        'moderated_by',
        'moderated_at',
        'helpful_count',
        'not_helpful_count',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'product_quality_rating' => 'integer',
        'shipping_speed_rating' => 'integer',
        'customer_service_rating' => 'integer',
        'communication_rating' => 'integer',
        'verified_purchase' => 'boolean',
        'moderated_at' => 'datetime',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
