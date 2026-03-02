<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserRoleRepository
{
    /**
     * Get user roles in tenant
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
     * Assign role to user in tenant
     */
    public function assignRoleToUserInTenant(User $user, Tenant $tenant, string $role): bool
    {
        try {
            DB::beginTransaction();
            
            // Update tenant_users pivot
            $user->tenants()->syncWithoutDetaching([
                $tenant->id => ['role' => $role]
            ]);
            
            // Update Spatie model_has_roles
            $this->assignSpatieRoleForTenant($user, $role, $tenant);
            
            DB::commit();
            
            Log::info('Role assigned to user in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error assigning role to user in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Remove role from user in tenant
     */
    public function removeRoleFromUserInTenant(User $user, Tenant $tenant): bool
    {
        try {
            DB::beginTransaction();
            
            // Remove from tenant_users pivot
            $user->tenants()->detach($tenant->id);
            
            // Remove from Spatie model_has_roles
            $this->removeSpatieRoleForTenant($user, $tenant);
            
            DB::commit();
            
            Log::info('Role removed from user in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error removing role from user in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get all users with their roles in specific tenant
     */
    public function getUsersWithRolesInTenant(Tenant $tenant): array
    {
        try {
            return $tenant->users()
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
            Log::error('Error getting users with roles in tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Check if user has specific role in tenant
     */
    public function userHasRoleInTenant(User $user, Tenant $tenant, string $role): bool
    {
        try {
            $userRole = $this->getUserRoleInTenant($user, $tenant);
            return $userRole === $role;
        } catch (\Exception $e) {
            Log::error('Error checking user role in tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
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
     * Assign Spatie role for tenant context
     */
    private function assignSpatieRoleForTenant(User $user, string $role, Tenant $tenant): void
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
            Log::error('Error assigning Spatie role for tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove Spatie role for tenant context
     */
    private function removeSpatieRoleForTenant(User $user, Tenant $tenant): void
    {
        try {
            $user->roles()->detach();
        } catch (\Exception $e) {
            Log::error('Error removing Spatie role for tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sync roles between tenant_users and model_has_roles
     */
    public function syncRoles(User $user, Tenant $tenant): bool
    {
        try {
            $pivotRole = $this->getUserRoleInTenant($user, $tenant);
            $spatieRole = $this->getSpatieRoleNames($user);
            
            if ($pivotRole && !in_array($pivotRole, $spatieRole)) {
                // Update Spatie to match pivot
                $this->assignSpatieRoleForTenant($user, $pivotRole, $tenant);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error syncing roles', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
