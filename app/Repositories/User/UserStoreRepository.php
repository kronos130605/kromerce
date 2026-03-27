<?php

namespace App\Repositories\User;

use App\Models\Store;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class UserStoreRepository extends BaseRepository
{
    protected array $allowedFields = [
        'user_id', 'store_id', 'is_active', 'joined_at'
    ];

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Attach user to store.
     */
    public function attachUserToStore(User $user, Store $store): bool
    {
        return $user->stores()->attach($store->id, [
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    /**
     * Check if user has any stores.
     */
    public function userHasStores(User $user): bool
    {
        return $user->stores()->exists();
    }

    /**
     * Get user's first store.
     */
    public function getUserFirstStore(User $user): ?Store
    {
        return $user->stores()->first();
    }

    /**
     * Get user's current store.
     */
    public function getUserCurrentStore(User $user): ?Store
    {
        // Get the most recent store association
        return $user->stores()
            ->wherePivot('is_active', true)
            ->latest('joined_at')
            ->first();
    }

    /**
     * Set user's current store.
     */
    public function setUserCurrentStore(User $user, Store $store): bool
    {
        // Deactivate all current store associations
        $user->stores()->updateExistingPivot(['is_active' => false]);

        // Set the new store as current
        return $user->stores()->updateExistingPivot($store->id, [
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    /**
     * Get all stores for user.
     */
    public function getUserStores(User $user): Collection
    {
        return $user->stores()
            ->wherePivot('is_active', true)
            ->get();
    }

    /**
     * Check if user has access to a specific store.
     */
    public function userHasAccessToStore(User $user, int $storeId): bool
    {
        return $user->stores()->where('stores.id', $storeId)->exists();
    }

    /**
     * Remove user from store.
     */
    public function detachUserFromStore(User $user, Store $store): bool
    {
        return $user->stores()->detach($store->id);
    }

    /**
     * Check if role is valid according to config
     */
    public function isValidRole(string $role): bool
    {
        $availableRoles = config('roles.available_roles', []);
        return isset($availableRoles[$role]);
    }

    /**
     * Check if role is a business role according to config
     */
    public function isBusinessRole(string $role): bool
    {
        $businessRoles = config('roles.business_roles', []);
        return in_array($role, $businessRoles);
    }

    /**
     * Get available roles for assignment
     */
    public function getAvailableRoles(): array
    {
        return config('roles.available_roles', []);
    }

    /**
     * Get business roles for assignment
     */
    public function getBusinessRoles(): array
    {
        return config('roles.business_roles', []);
    }
}
