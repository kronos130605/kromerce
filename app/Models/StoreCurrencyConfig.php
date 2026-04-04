<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreCurrencyConfig extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'store_id',
        'default_currency',
        'display_currencies',
        'use_custom_rates',
        'auto_update_rates',
        'rate_update_frequency',
        'last_rate_update',
        'historical_retention_years',
        'preferred_cuba_source_id',
        'preferred_foreign_source_id',
        'dashboard_pairs',
    ];

    protected $casts = [
        'use_custom_rates' => 'boolean',
        'auto_update_rates' => 'boolean',
        'display_currencies' => 'array',
        'dashboard_pairs' => 'array',
        'last_rate_update' => 'date',
        'historical_retention_years' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function preferredCubaSource(): BelongsTo
    {
        return $this->belongsTo(CurrencySource::class, 'preferred_cuba_source_id');
    }

    public function preferredForeignSource(): BelongsTo
    {
        return $this->belongsTo(CurrencySource::class, 'preferred_foreign_source_id');
    }

    public function scopeWithCustomRates($query)
    {
        return $query->where('use_custom_rates', true);
    }

    public function scopeWithAutoUpdate($query)
    {
        return $query->where('auto_update_rates', true);
    }

    public function getDisplayCurrenciesAttribute(): array
    {
        return $this->attributes['display_currencies']
            ? json_decode($this->attributes['display_currencies'], true) ?? []
            : [];
    }

    public function usesCustomRates(): bool
    {
        return $this->use_custom_rates;
    }

    public function hasAutoUpdate(): bool
    {
        return $this->auto_update_rates;
    }
}
