<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\DarkModePreferences;
use App\Models\Store;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, DarkModePreferences;

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
        'store_id',
        'first_name',
        'last_name',
        'username',
        'avatar',
        'bio',
        'birthdate',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'locale',
        'timezone',
        'currency',
        'theme',
        'dark_mode',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'status',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'newsletter_subscribed',
        'newsletter_subscribed_at',
        'marketing_emails',
        'sms_notifications',
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
            'birthdate' => 'date',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'dark_mode' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'newsletter_subscribed' => 'boolean',
            'newsletter_subscribed_at' => 'datetime',
            'marketing_emails' => 'boolean',
            'sms_notifications' => 'boolean',
        ];
    }

    public function ownedStores()
    {
        return $this->hasMany(Store::class, 'owner_id');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_users')
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
