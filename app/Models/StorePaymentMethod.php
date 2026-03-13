<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorePaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'method',
        'provider',
        'config',
        'is_enabled',
        'min_amount',
        'max_amount',
        'fee_percentage',
        'fixed_fee',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_enabled' => 'boolean',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'fixed_fee' => 'decimal:2',
        'sort_order' => 'integer',
        'config' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the store that owns the payment method.
     */
    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Scope a query to only include enabled payment methods.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope a query to filter by payment method.
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get the payment method's type as a human readable string.
     */
    public function getMethodAttribute(): string
    {
        return match($this->method) {
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'zelle' => 'Zelle',
            'crypto' => 'Cryptocurrency',
            'bank_transfer' => 'Bank Transfer',
            'cash_on_delivery' => 'Cash on Delivery',
            'tropipay' => 'TropiPay',
            default => ucfirst($this->method),
        };
    }

    /**
     * Check if this payment method is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    /**
     * Get the configuration as array.
     */
    public function getConfigAttribute(): array
    {
        return $this->config ?? [];
    }

    /**
     * Calculate total fee for a given amount.
     */
    public function calculateTotalFee(float $amount): float
    {
        $fixedFee = $this->fixed_fee ?? 0;
        $percentageFee = $amount * (($this->fee_percentage ?? 0) / 100);
        
        return $fixedFee + $percentageFee;
    }
}
