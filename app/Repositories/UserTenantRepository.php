<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserTenantRepository
{
    /**
     * Check if user has any tenants
     */
    public function userHasTenants(User $user): bool
    {
        try {
            return $user->tenants()->exists();
        } catch (\Exception $e) {
            Log::error('Error checking if user has tenants', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get user's first tenant
     */
    public function getUserFirstTenant(User $user): ?Tenant
    {
        try {
            return $user->tenants()->first();
        } catch (\Exception $e) {
            Log::error('Error getting user first tenant', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get user's current tenant
     */
    public function getUserCurrentTenant(User $user): ?Tenant
    {
        try {
            return $user->currentTenant();
        } catch (\Exception $e) {
            Log::error('Error getting user current tenant', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Set user's current tenant
     */
    public function setUserCurrentTenant(User $user, Tenant $tenant): bool
    {
        try {
            // Verify that the user has access to the tenant
            if (!$this->userHasTenant($user, $tenant)) {
                Log::warning('User trying to set current tenant without access', [
                    'user_id' => $user->id,
                    'tenant_id' => $tenant->id,
                ]);
                return false;
            }
            
            $user->current_tenant_id = $tenant->id;
            $user->save();
            
            Log::info('User current tenant updated', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error setting user current tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get all tenants for user
     */
    public function getUserTenants(User $user): Collection
    {
        try {
            return $user->tenants()->withPivot('role')->get();
        } catch (\Exception $e) {
            Log::error('Error getting user tenants', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Check if user has specific tenant
     */
    public function userHasTenant(User $user, Tenant $tenant): bool
    {
        try {
            return $user->tenants()->where('tenant_id', $tenant->id)->exists();
        } catch (\Exception $e) {
            Log::error('Error checking if user has tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Attach user to tenant with role
     */
    public function attachUserToTenant(User $user, Tenant $tenant, string $role = 'customer'): bool
    {
        try {
            DB::beginTransaction();
            
            // Check if already attached
            if (!$this->userHasTenant($user, $tenant)) {
                $user->tenants()->attach($tenant->id, [
                    'role' => $role,
                ]);
            }
            
            // Set as current tenant if user doesn't have one
            if (!$user->current_tenant_id) {
                $user->current_tenant_id = $tenant->id;
                $user->save();
            }
            
            DB::commit();
            
            Log::info('User attached to tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error attaching user to tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Detach user from tenant
     */
    public function detachUserFromTenant(User $user, Tenant $tenant): bool
    {
        try {
            DB::beginTransaction();
            
            $user->tenants()->detach($tenant->id);
            
            // If this was the current tenant, set a new one
            if ($user->current_tenant_id === $tenant->id) {
                $newCurrentTenant = $this->getUserFirstTenant($user);
                if ($newCurrentTenant) {
                    $user->current_tenant_id = $newCurrentTenant->id;
                    $user->save();
                } else {
                    $user->current_tenant_id = null;
                    $user->save();
                }
            }
            
            DB::commit();
            
            Log::info('User detached from tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error detaching user from tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get user role in tenant
     */
    public function getUserRoleInTenant(User $user, Tenant $tenant): ?string
    {
        try {
            return $user->tenants()
                ->where('tenant_id', $tenant->id)
                ->first()
                ?->pivot->role ?? null;
        } catch (\Exception $e) {
            Log::error('Error getting user role in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Update user role in tenant
     */
    public function updateUserRoleInTenant(User $user, Tenant $tenant, string $role): bool
    {
        try {
            $user->tenants()->syncWithoutDetaching([
                $tenant->id => ['role' => $role]
            ]);
            
            Log::info('User role updated in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating user role in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get users count for tenant
     */
    public function getUsersCountForTenant(Tenant $tenant): int
    {
        try {
            return $tenant->users()->count();
        } catch (\Exception $e) {
            Log::error('Error getting users count for tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Get users by role in tenant
     */
    public function getUsersByRoleInTenant(Tenant $tenant, string $role): Collection
    {
        try {
            return $tenant->users()
                ->wherePivot('role', $role)
                ->get();
        } catch (\Exception $e) {
            Log::error('Error getting users by role in tenant', [
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            return collect();
        }
    }
}
