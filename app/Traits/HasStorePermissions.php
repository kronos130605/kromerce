<?php

namespace App\Traits;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;

trait HasStorePermissions
{
    /**
     * Check if user can manage specific store.
     */
    public function canManageStore(Store $store): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Store owners can always manage their stores
        if ($store->owner_id === $user->id) {
            return true;
        }

        // Admins can manage all stores
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return true;
        }

        // Check if user is assigned to this store
        $storeUser = $store->users()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$storeUser) {
            return false;
        }

        // Store managers can update but not delete
        if (in_array($user->role, ['manager', 'admin'])) {
            return true;
        }

        // Employees can only view
        if ($user->role === 'employee') {
            return false;
        }

        return false;
    }

    /**
     * Check if user can view store.
     */
    public function canViewStore(Store $store): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Store owners can always view their stores
        if ($store->owner_id === $user->id) {
            return true;
        }

        // Admins can view all stores
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return true;
        }

        // Check if user is assigned to this store
        return $store->users()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Check if user can delete store.
     */
    public function canDeleteStore(Store $store): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Store owners can delete their stores
        if ($store->owner_id === $user->id) {
            return true;
        }

        // Admins can delete all stores
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return true;
        }

        // Managers cannot delete stores
        if ($user->role === 'manager') {
            return false;
        }

        // Employees cannot delete stores
        if ($user->role === 'employee') {
            return false;
        }

        return false;
    }

    /**
     * Get stores user can access.
     */
    public function getAccessibleStores(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect();
        }

        // Admins can access all stores
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return Store::all();
        }

        // Other users can only access their assigned stores
        return Store::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->where('is_active', true);
        })->orWhere('owner_id', $user->id)->get();
    }

    /**
     * Check if user has specific permission in store.
     */
    public function hasStorePermission(Store $store, string $permission): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Store owners have all permissions
        if ($store->owner_id === $user->id) {
            return true;
        }

        // Admins have all permissions
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return true;
        }

        // Check user's specific permissions in the store
        $storeUser = $store->users()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$storeUser) {
            return false;
        }

        return $storeUser->hasPermission($permission);
    }
}
