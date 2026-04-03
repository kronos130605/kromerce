<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class CurrencySource extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'code',
        'type',
        'provider_class',
        'is_active',
        'is_global_default',
        'base_url',
        'config',
        'auth_type',
        'default_credentials',
        'last_tested_at',
        'last_test_success',
        'last_test_message',
        'success_count',
        'failure_count',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_global_default' => 'boolean',
        'last_tested_at' => 'datetime',
        'last_test_success' => 'boolean',
        'success_count' => 'integer',
        'failure_count' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    /**
     * Get business configs using this source.
     */
    public function businessConfigs(): HasMany
    {
        return $this->hasMany(BusinessCurrencyConfig::class, 'source_id');
    }

    /**
     * Get decrypted default credentials.
     */
    public function getDecryptedCredentials(): ?array
    {
        if (empty($this->default_credentials)) {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($this->default_credentials);
            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Set encrypted default credentials.
     */
    public function setEncryptedCredentials(array $credentials): void
    {
        $this->default_credentials = Crypt::encryptString(json_encode($credentials));
    }

    /**
     * Get selector for web scraping.
     */
    public function getSelector(string $key): ?string
    {
        return $this->config['selectors'][$key] ?? null;
    }

    /**
     * Get supported currencies from config.
     */
    public function getSupportedCurrencies(): array
    {
        return $this->config['currencies_supported'] ?? [];
    }

    /**
     * Check if source supports a specific currency pair.
     */
    public function supportsCurrencyPair(string $from, string $to): bool
    {
        $supported = $this->getSupportedCurrencies();
        
        if (empty($supported)) {
            return true; // Assume supports all if not specified
        }

        return in_array($from, $supported) && in_array($to, $supported);
    }

    /**
     * Record a successful fetch.
     */
    public function recordSuccess(): void
    {
        $this->increment('success_count');
        $this->update([
            'last_tested_at' => now(),
            'last_test_success' => true,
        ]);
    }

    /**
     * Record a failed fetch.
     */
    public function recordFailure(string $message): void
    {
        $this->increment('failure_count');
        $this->update([
            'last_tested_at' => now(),
            'last_test_success' => false,
            'last_test_message' => $message,
        ]);
    }

    /**
     * Get success rate percentage.
     */
    public function getSuccessRate(): float
    {
        $total = $this->success_count + $this->failure_count;
        
        if ($total === 0) {
            return 100.0;
        }

        return round(($this->success_count / $total) * 100, 2);
    }

    /**
     * Scope for active sources.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for global default.
     */
    public function scopeGlobalDefault($query)
    {
        return $query->where('is_global_default', true)->where('is_active', true);
    }
}
