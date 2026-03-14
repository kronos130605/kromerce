<?php

namespace App\Repositories\User;

use App\Models\Store;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class UserStoreRepository extends BaseRepository
{
    protected array $allowedFields = [
        'user_id', 'store_id', 'role', 'is_active', 'joined_at'
    ];

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Attach user to store.
     */
    public function attachUserToStore(User $user, Store $store, string $role): bool
    {
        return $this->model->stores()->attach($store->id, [
            'role' => $role,
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    /**
     * Check if user has any stores.
     */
    public function userHasStores(User $user): bool
    {
        return $this->model->stores()->exists();
    }

    /**
     * Get user's first store.
     */
    public function getUserFirstStore(User $user): ?Store
    {
        return $this->model->stores()->first();
    }

    /**
     * Get user's current store.
     */
    public function getUserCurrentStore(User $user): ?Store
    {
        // Get the most recent store association
        return $this->model->stores()
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
        $this->model->stores()->updateExistingPivot(['is_active' => false]);

        // Set the new store as current
        return $this->model->stores()->updateExistingPivot($store->id, [
            'is_active' => true,
            'joined_at' => now(),
        ]);
    }

    /**
     * Get all stores for user.
     */
    public function getUserStores(User $user): Collection
    {
        return $this->model->stores()
            ->wherePivot('is_active', true)
            ->get();
    }

    /**
     * Remove user from store.
     */
    public function detachUserFromStore(User $user, Store $store): bool
    {
        return $this->model->stores()->detach($store->id);
    }
}
