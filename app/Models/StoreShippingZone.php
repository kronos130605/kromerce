<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreShippingZone extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'countries',
        'states',
        'postal_codes',
        'shipping_cost',
        'free_shipping_threshold',
        'estimated_days',
        'is_active',
        'conditions',
    ];

    protected $casts = [
        'countries' => 'array',
        'states' => 'array',
        'postal_codes' => 'array',
        'shipping_cost' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'estimated_days' => 'integer',
        'is_active' => 'boolean',
        'conditions' => 'json',
    ];

    /**
     * Get the store that owns the shipping zone.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Scope a query to only include active shipping zones.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a postal code is in this shipping zone.
     */
    public function coversPostalCode(string $postalCode): bool
    {
        if (empty($this->postal_codes)) {
            return true; // If no restrictions, covers all
        }

        return in_array($postalCode, $this->postal_codes);
    }

    /**
     * Check if a country is in this shipping zone.
     */
    public function coversCountry(string $country): bool
    {
        if (empty($this->countries)) {
            return true; // If no restrictions, covers all
        }

        return in_array($country, $this->countries);
    }

    /**
     * Get shipping cost for given amount.
     */
    public function getShippingCost(float $amount): float
    {
        if ($this->free_shipping_threshold && $amount >= $this->free_shipping_threshold) {
            return 0;
        }

        return $this->shipping_cost;
    }
}
