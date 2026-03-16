<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use App\Services\StoreService;

class ProductPolicy
{
    private function getCurrentStoreId(User $user): ?int
    {
        $storeId = session('current_store_id');
        if ($storeId) {
            return (int) $storeId;
        }

        $storeService = app(StoreService::class);
        $store = $storeService->getUserCurrentStore($user);

        return $store?->id;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        $storeId = $this->getCurrentStoreId($user);
        return $storeId ? $product->store_id === $storeId : false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        $storeId = $this->getCurrentStoreId($user);
        return $storeId ? $product->store_id === $storeId : false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        $storeId = $this->getCurrentStoreId($user);
        return $storeId ? $product->store_id === $storeId : false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        $storeId = $this->getCurrentStoreId($user);
        return $storeId ? $product->store_id === $storeId : false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        $storeId = $this->getCurrentStoreId($user);
        return $storeId ? $product->store_id === $storeId : false;
    }
}
