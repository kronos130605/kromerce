<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreCurrencyConfig extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'store_id',
        'default_currency',
        'display_currencies',
        'use_custom_rates',
        'auto_update_rates',
        'rate_update_frequency',
        'last_rate_update',
        'historical_retention_years',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'use_custom_rates' => 'boolean',
        'auto_update_rates' => 'boolean',
        'display_currencies' => 'array',
        'last_rate_update' => 'date',
        'historical_retention_years' => 'integer',
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
     * Get the store that owns the currency configuration.
     */
    public function store(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Scope a query to only include stores with custom rates.
     */
    public function scopeWithCustomRates($query)
    {
        return $query->where('use_custom_rates', true);
    }

    /**
     * Scope a query to only include stores with auto update.
     */
    public function scopeWithAutoUpdate($query)
    {
        return $query->where('auto_update_rates', true);
    }

    /**
     * Get the display currencies as array.
     */
    public function getDisplayCurrenciesAttribute(): array
    {
        return $this->display_currencies ?? [];
    }

    /**
     * Check if the store uses custom rates.
     */
    public function usesCustomRates(): bool
    {
        return $this->use_custom_rates;
    }

    /**
     * Check if the store has auto update enabled.
     */
    public function hasAutoUpdate(): bool
    {
        return $this->auto_update_rates;
    }

    /**
     * Get the default currency.
     */
    public function getDefaultCurrencyAttribute(): string
    {
        return $this->default_currency ?? 'USD';
    }
}
