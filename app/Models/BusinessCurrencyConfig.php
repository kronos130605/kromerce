<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessCurrencyConfig extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'store_id',
        'default_currency',
        'display_currencies',
        'use_custom_rates',
        'auto_update_rates',
        'rate_update_frequency',
        'last_rate_update',
        'historical_retention_years',
        'preferred_cuba_source_id',
    ];

    protected $casts = [
        'display_currencies' => 'array',
        'use_custom_rates' => 'boolean',
        'auto_update_rates' => 'boolean',
        'last_rate_update' => 'date',
        'historical_retention_years' => 'integer',
    ];

    /**
     * Get the store that owns the currency config.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the Cuba source preference for this store.
     */
    public function preferredCubaSource(): BelongsTo
    {
        return $this->belongsTo(CurrencySource::class, 'preferred_cuba_source_id');
    }

    /**
     * Get currency symbol.
     */
    private function getCurrencySymbol(string $currency): string
    {
        return config("currencies.supported.{$currency}.symbol", $currency);
    }

    /**
     * Get currency name.
     */
    private function getCurrencyName(string $currency): string
    {
        return config("currencies.supported.{$currency}.name", $currency);
    }

    /**
     * Get currency flag emoji.
     */
    private function getCurrencyFlag(string $currency): string
    {
        return config("currencies.supported.{$currency}.flag", '🌍');
    }
}
