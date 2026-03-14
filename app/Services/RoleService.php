<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use App\Repositories\User\RoleRepository;
use App\Repositories\User\UserRoleRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService
{
    private UserRoleRepository $userRoleRepository;
    private RoleRepository $roleRepository;

    // Cache key pattern
    private const CACHE_KEY_PREFIX = 'user_role_';
    private const CACHE_TTL = 300; // 5 minutes

    public function __construct(
        UserRoleRepository $userRoleRepository,
        RoleRepository $roleRepository
    ) {
        $this->userRoleRepository = $userRoleRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get user role in store with caching
     */
    public function getUserRoleInStore(User $user, Store $store): ?string
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $user->id . '_' . $store->id;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user, $store) {

            return $this->calculateUserRoleInStore($user, $store);
        });
    }

    /**
     * Calculate user role in store (actual logic)
     */
    private function calculateUserRoleInStore(User $user, Store $store): ?string
    {
        try {
            // First attempt: Get from store_user pivot
            $pivotRole = $this->userRoleRepository->getUserRoleInStore($user, $store);

            if ($pivotRole) {
                // Verify and sync with model_has_roles if needed
                $this->syncRoleWithSpatie($user, $pivotRole, $store);
                return $pivotRole;
            }

            // Second attempt: Get from model_has_roles
            $spatieRole = $this->getSpatieRoleForStore($user, $store);

            if ($spatieRole) {
                // Update store_user pivot with Spatie role
                $this->updateStorePivotRole($user, $store, $spatieRole);
                return $spatieRole;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error getting user role in store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Clear user role cache (for when roles change)
     */
    public function clearUserRoleCache(User $user, Store $store): void
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $user->id . '_' . $store->id;
        Cache::forget($cacheKey);
    }

    /**
     * Assign role to user in store (both tables)
     */
    public function assignRoleToUserInStore(User $user, Store $store, string $role): bool
    {
        try {
            DB::beginTransaction();

            // Update store_user pivot
            $user->stores()->syncWithoutDetaching([
                $store->id => ['role' => $role]
            ]);

            // Update Spatie model_has_roles
            $this->assignSpatieRoleForStore($user, $role, $store);

            DB::commit();

            // Clear cache after role change
            $this->clearUserRoleCache($user, $store);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning role to user in store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Remove role from user in store (both tables)
     */
    public function removeRoleFromUserInStore(User $user, Store $store): bool
    {
        try {
            DB::beginTransaction();

            // Remove from store_user pivot
            $user->stores()->detach($store->id);

            // Remove from Spatie model_has_roles
            $this->removeSpatieRoleForStore($user, $store);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing role from user in store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }


    /**
     * Get all users with their roles in store
     */
    public function getUsersWithRolesInStore(Store $store): array
    {
        return $this->userRoleRepository->getUsersWithRolesInStore($store);
    }

    /**
     * Sync role with Spatie model_has_roles
     */
    private function syncRoleWithSpatie(User $user, string $role, Store $store): void
    {
        try {
            $spatieRole = $this->getSpatieRoleForStore($user, $store);

            if (!$spatieRole || $spatieRole !== $role) {
                // Update Spatie role to match pivot
                $this->assignSpatieRoleForStore($user, $role, $store);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing role with Spatie', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get Spatie role for store context
     */
    private function getSpatieRoleForStore(User $user, Store $store): ?string
    {
        try {
            // Get roles from Spatie with store context
            $roles = $user->roles()->get()->pluck('name')->toArray();

            // Use repository to get highest priority role
            return $this->roleRepository->getHighestPriorityRole($roles);
        } catch (\Exception $e) {
            Log::error('Error getting Spatie role for store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Assign Spatie role for store context
     */
    private function assignSpatieRoleForStore(User $user, string $role, Store $store): void
    {
        try {
            // Remove existing roles first
            $user->roles()->detach();

            // Assign new role using repository
            $roleModel = $this->roleRepository->getFirstBy([
                'name' => $role
            ]);

            if ($roleModel) {
                $user->assignRole($roleModel);
            }
        } catch (\Exception $e) {
            Log::error('Error assigning Spatie role for store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove Spatie role for store context
     */
    private function removeSpatieRoleForStore(User $user, Store $store): void
    {
        try {
            $user->roles()->detach();
        } catch (\Exception $e) {
            Log::error('Error removing Spatie role for store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update store pivot role
     */
    private function updateStorePivotRole(User $user, Store $store, string $role): void
    {
        try {
            $user->stores()->syncWithoutDetaching([
                $store->id => ['role' => $role]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating store pivot role', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if user has specific role in store
     */
    public function userHasRoleInStore(User $user, Store $store, string $role): bool
    {
        $userRole = $this->getUserRoleInStore($user, $store);
        return $userRole === $role;
    }

    /**
     * Get all available roles
     */
    public function getAvailableRoles(): array
    {
        try {
            return $this->roleRepository->getAllNames();
        } catch (\Exception $e) {
            Log::error('Error getting available roles', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get user's primary role from Laravel permissions
     */
    public function getUserPrimaryRole(User $user): ?string
    {
        try {
            // Get the first role from Laravel's Spatie Permission package
            $roles = $user->getRoleNames();
            
            if ($roles->isEmpty()) {
                return null;
            }

            // Get role priority from config
            $rolePriority = config('roles.role_priority');

            // Find the role with highest priority (lowest number)
            $primaryRole = null;
            $highestPriority = PHP_INT_MAX;

            foreach ($roles as $role) {
                $priority = $rolePriority[$role] ?? 999;
                if ($priority < $highestPriority) {
                    $highestPriority = $priority;
                    $primaryRole = $role;
                }
            }

            return $primaryRole;
        } catch (\Exception $e) {
            Log::error('Error getting user primary role', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
