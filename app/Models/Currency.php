<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Currency extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'code',
        'name',
        'symbol',
        'flag_emoji',
        'decimal_places',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });

        // Ensure only one default currency
        static::saving(function ($model) {
            if ($model->is_default) {
                static::where('is_default', true)
                    ->where('id', '!=', $model->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('code');
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return "{$this->flag_emoji} {$this->code} - {$this->name}";
    }

    public function getFormattedSymbolAttribute(): string
    {
        return $this->symbol ?: $this->code;
    }

    // Helpers
    public static function getDefault(): ?self
    {
        return static::default()->first();
    }

    public static function getByCode(string $code): ?self
    {
        return static::where('code', strtoupper($code))->first();
    }

    public static function getActiveList(): array
    {
        return static::active()->ordered()->get()->mapWithKeys(function ($currency) {
            return [$currency->code => $currency->display_name];
        })->toArray();
    }

    public function formatAmount(float $amount): string
    {
        $formatted = number_format($amount, $this->decimal_places);
        return "{$this->symbol}{$formatted}";
    }
}
