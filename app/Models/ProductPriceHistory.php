<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'currency',
        'old_price',
        'new_price',
        'change_reason',
        'changed_by',
        'notes',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the price history.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who made the change.
     */
    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get the price difference.
     */
    public function getPriceDifferenceAttribute(): float
    {
        return $this->new_price - $this->old_price;
    }

    /**
     * Get the price difference percentage.
     */
    public function getPriceDifferencePercentageAttribute(): float
    {
        if ($this->old_price == 0) {
            return 0;
        }

        return round((($this->new_price - $this->old_price) / $this->old_price) * 100, 2);
    }

    /**
     * Check if price increased.
     */
    public function isPriceIncrease(): bool
    {
        return $this->new_price > $this->old_price;
    }

    /**
     * Check if price decreased.
     */
    public function isPriceDecrease(): bool
    {
        return $this->new_price < $this->old_price;
    }

    /**
     * Scope to get price changes for a specific currency.
     */
    public function scopeCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope to get price changes by reason.
     */
    public function scopeReason($query, string $reason)
    {
        return $query->where('change_reason', $reason);
    }

    /**
     * Scope to get price changes in date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
