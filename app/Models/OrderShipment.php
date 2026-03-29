<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderShipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'order_id',
        'shipment_number',
        'carrier',
        'service',
        'tracking_number',
        'tracking_url',
        'ship_from_name',
        'ship_from_address',
        'ship_from_city',
        'ship_from_state',
        'ship_from_postal_code',
        'ship_from_country',
        'weight',
        'weight_unit',
        'length',
        'width',
        'height',
        'dimension_unit',
        'shipping_cost',
        'insurance_amount',
        'declared_value',
        'status',
        'shipped_at',
        'delivered_at',
        'estimated_delivery_at',
        'notes',
        'carrier_data',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'carrier_data' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(OrderItem::class, 'shipment_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
