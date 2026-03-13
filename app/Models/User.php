<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Traits\DarkModePreferences;
use App\Models\Store;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, BelongsToTenant, DarkModePreferences;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar_url',
        'is_active',
        'dark_mode',
        'theme_preferences',
        'language',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'dark_mode' => 'boolean',
            'theme_preferences' => 'array',
            'language' => 'string',
        ];
    }

    public function ownedStores()
    {
        return $this->hasMany(Store::class, 'owner_id');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function currentStore()
    {
        return $this->stores()->first();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    public function isBusinessOwner()
    {
        return $this->hasRole('business_owner');
    }

    public function isCustomer()
    {
        return $this->hasRole('customer');
    }
}
