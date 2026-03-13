<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRoleRepository
{
    /**
     * Get user roles in store
     */
    public function getUserRoleInStore(User $user, Store $store): ?string
    {
        try {
            return $user->stores()
                ->where('store_id', $store->id)
                ->first()
                ?->pivot->role ?? null;
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
     * Get user roles from Spatie
     */
    public function getSpatieRoles(User $user): Collection
    {
        try {
            return $user->roles()->get();
        } catch (\Exception $e) {
            Log::error('Error getting Spatie roles', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Get user role names from Spatie
     */
    public function getSpatieRoleNames(User $user): array
    {
        try {
            return $user->roles->pluck('name')->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting Spatie role names', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Assign role to user in store
     */
    public function assignRoleToUserInStore(User $user, Store $store, string $role): bool
    {
        try {
            DB::beginTransaction();
            
            // Update store_users pivot
            $user->stores()->syncWithoutDetaching([
                $store->id => ['role' => $role]
            ]);
            
            // Update Spatie model_has_roles
            $this->assignSpatieRoleForStore($user, $role, $store);
            
            DB::commit();
            
            Log::info('Role assigned to user in store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
            ]);
            
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
     * Remove role from user in store
     */
    public function removeRoleFromUserInStore(User $user, Store $store): bool
    {
        try {
            DB::beginTransaction();
            
            // Remove from store_users pivot
            $user->stores()->detach($store->id);
            
            // Remove from Spatie model_has_roles
            $this->removeSpatieRoleForStore($user, $store);
            
            DB::commit();
            
            Log::info('Role removed from user in store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
            ]);
            
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
     * Get all users with their roles in specific store
     */
    public function getUsersWithRolesInStore(Store $store): array
    {
        try {
            return $store->users()
                ->withPivot('role')
                ->get()
                ->map(function ($user) {
                    return [
                        'user' => $user,
                        'role' => $user->pivot->role,
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting users with roles in store', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Check if user has specific role in store
     */
    public function userHasRoleInStore(User $user, Store $store, string $role): bool
    {
        try {
            $userRole = $this->getUserRoleInStore($user, $store);
            return $userRole === $role;
        } catch (\Exception $e) {
            Log::error('Error checking user role in store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get available roles from Spatie
     */
    public function getAvailableRoles(): array
    {
        try {
            return DB::table('roles')
                ->pluck('name')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting available roles', [
                'error' => $e->getMessage(),
            ]);
            return [];
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
            
            // Assign new role
            $roleModel = DB::table('roles')->where('name', $role)->first();
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
     * Sync roles between store_users and model_has_roles
     */
    public function syncRoles(User $user, Store $store): bool
    {
        try {
            $pivotRole = $this->getUserRoleInStore($user, $store);
            $spatieRole = $this->getSpatieRoleNames($user);
            
            if ($pivotRole && !in_array($pivotRole, $spatieRole)) {
                // Update Spatie to match pivot
                $this->assignSpatieRoleForStore($user, $pivotRole, $store);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing roles', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
