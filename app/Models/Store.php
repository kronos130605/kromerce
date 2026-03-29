<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'banner',
        'email',
        'phone',
        'country',
        'currency',
        'language',
        'business_type',
        'status',
        'tax_id',
        'verified_business',
        'website_url',
        'timezone',
        'owner_id',
        'uuid',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_business' => 'boolean',
        'data' => 'array',
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKey(): string
    {
        return $this->uuid;
    }

    /**
     * Get the owner of the store.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the contacts for the store.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(StoreContact::class);
    }

    /**
     * Get the social media accounts for the store.
     */
    public function socialMedia(): HasMany
    {
        return $this->hasMany(StoreSocialMedia::class);
    }

    /**
     * Get the payment methods for the store.
     */
    public function paymentMethods(): HasMany
    {
        return $this->hasMany(StorePaymentMethod::class);
    }

    /**
     * Get the domains for the store.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Get the shipping zones for the store.
     */
    public function shippingZones(): HasMany
    {
        return $this->hasMany(StoreShippingZone::class);
    }

    /**
     * Get the pickup locations for the store.
     */
    public function pickupLocations(): HasMany
    {
        return $this->hasMany(StorePickupLocation::class);
    }

    /**
     * Get the business hours for the store.
     */
    public function businessHours(): HasMany
    {
        return $this->hasMany(StoreBusinessHour::class);
    }

    /**
     * Get the settings for the store.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(StoreSetting::class);
    }

    /**
     * Get the currency configuration for the store.
     */
    public function currencyConfig(): HasOne
    {
        return $this->hasOne(StoreCurrencyConfig::class);
    }

    /**
     * Get the users associated with the store.
     */
    public function users(): HasMany
    {
        return $this->hasMany(StoreUser::class);
    }

    /**
     * Get the products for the store.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the categories for the store.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }

    /**
     * Get the orders for the store.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope a query to only include active stores.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include verified stores.
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_business', true);
    }

    /**
     * Scope a query to only include stores by business type.
     */
    public function scopeByBusinessType($query, string $type)
    {
        return $query->where('business_type', $type);
    }

    /**
     * Get the store's status as a human readable string.
     */
    public function getStatusAttribute(): string
    {
        $status = $this->getOriginal('status');
        return match($status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'maintenance' => 'Under Maintenance',
            'suspended' => 'Suspended',
            default => $status ?? 'Unknown',
        };
    }

    /**
     * Get the store's business type as a human readable string.
     */
    public function getBusinessTypeAttribute(): string
    {
        $businessType = $this->getOriginal('business_type');
        return match($businessType) {
            'retail' => 'Retail',
            'wholesale' => 'Wholesale',
            'marketplace' => 'Marketplace',
            default => $businessType ?? 'Unknown',
        };
    }

    /**
     * Check if the store is currently active.
     */
    public function isActive(): bool
    {
        return $this->getOriginal('status') === 'active';
    }

    /**
     * Check if the store is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_business;
    }

    /**
     * Get the store's logo URL.
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/logos/' . $this->logo) : null;
    }

    /**
     * Get the store's banner URL.
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/banners/' . $this->banner) : null;
    }
}
