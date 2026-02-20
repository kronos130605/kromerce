<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\HasTenantDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

class Tenant extends Model implements TenantWithDatabase
{
    use HasTenantDatabase, HasDomains;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'custom_domain',
        'is_active',
        'branding_config',
        'owner_id',
    ];

    protected $casts = [
        'branding_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tenant_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getBrandingAttribute()
    {
        return array_merge([
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'logo_url' => null,
            'favicon_url' => null,
            'custom_css' => null,
            'theme' => 'light',
        ], $this->branding_config ?? []);
    }

    public function getDomainAttribute()
    {
        return $this->domains->first()?->domain;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (!$tenant->slug) {
                $tenant->slug = str()->slug($tenant->name);
            }
        });
    }
}
