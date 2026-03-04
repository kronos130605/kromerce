<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tenant;
use App\Repositories\UserRoleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RoleService
{
    private UserRoleRepository $userRoleRepository;

    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }
    /**
     * Get user role in tenant with synchronization
     */
    public function getUserRoleInTenant(User $user, Tenant $tenant): ?string
    {
        try {
            // First attempt: Get from tenant_user pivot
            $pivotRole = $this->userRoleRepository->getUserRoleInTenant($user, $tenant);

            if ($pivotRole) {
                // Verify and sync with model_has_roles if needed
                $this->syncRoleWithSpatie($user, $pivotRole, $tenant);
                return $pivotRole;
            }

            // Second attempt: Get from model_has_roles
            $spatieRole = $this->getSpatieRoleForTenant($user, $tenant);

            if ($spatieRole) {
                // Update tenant_user pivot with Spatie role
                $this->updateTenantPivotRole($user, $tenant, $spatieRole);
                return $spatieRole;
            }

            return null;

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
     * Assign role to user in tenant (both tables)
     */
    public function assignRoleToUserInTenant(User $user, Tenant $tenant, string $role): bool
    {
        try {
            DB::beginTransaction();

            // Update tenant_user pivot
            $user->tenants()->syncWithoutDetaching([
                $tenant->id => ['role' => $role]
            ]);

            // Update Spatie model_has_roles
            $this->assignSpatieRoleForTenant($user, $role, $tenant);

            DB::commit();

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
     * Remove role from user in tenant (both tables)
     */
    public function removeRoleFromUserInTenant(User $user, Tenant $tenant): bool
    {
        try {
            DB::beginTransaction();

            // Remove from tenant_user pivot
            $user->tenants()->detach($tenant->id);

            // Remove from Spatie model_has_roles
            $this->removeSpatieRoleForTenant($user, $tenant);

            DB::commit();

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
     * Get all users with their roles in tenant
     */
    public function getUsersWithRolesInTenant(Tenant $tenant): array
    {
        return $this->userRoleRepository->getUsersWithRolesInTenant($tenant);
    }

    /**
     * Sync role with Spatie model_has_roles
     */
    private function syncRoleWithSpatie(User $user, string $role, Tenant $tenant): void
    {
        try {
            $spatieRole = $this->getSpatieRoleForTenant($user, $tenant);

            if (!$spatieRole || $spatieRole !== $role) {
                // Update Spatie role to match pivot
                $this->assignSpatieRoleForTenant($user, $role, $tenant);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing role with Spatie', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get Spatie role for tenant context
     */
    private function getSpatieRoleForTenant(User $user, Tenant $tenant): ?string
    {
        try {
            // Get roles from Spatie with tenant context
            $roles = $user->roles()->get()->pluck('name')->toArray();

            // Prioridad de roles para determinar el rol principal
            $rolePriority = [
                'owner' => 1,
                'business_owner' => 2,
                'admin' => 3,
                'manager' => 4,
                'employee' => 5,
                'customer' => 6,
            ];

            $selectedRole = null;
            $highestPriority = PHP_INT_MAX;

            foreach ($roles as $role) {
                if (isset($rolePriority[$role]) && $rolePriority[$role] < $highestPriority) {
                    $selectedRole = $role;
                    $highestPriority = $rolePriority[$role];
                }
            }

            return $selectedRole;
        } catch (\Exception $e) {
            Log::error('Error getting Spatie role for tenant', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            return null;
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
            $roleModel = Role::where('name', $role)->first();
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
     * Update tenant pivot role
     */
    private function updateTenantPivotRole(User $user, Tenant $tenant, string $role): void
    {
        try {
            $user->tenants()->syncWithoutDetaching([
                $tenant->id => ['role' => $role]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating tenant pivot role', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if user has specific role in tenant
     */
    public function userHasRoleInTenant(User $user, Tenant $tenant, string $role): bool
    {
        $userRole = $this->getUserRoleInTenant($user, $tenant);
        return $userRole === $role;
    }

    /**
     * Get all available roles
     */
    public function getAvailableRoles(): array
    {
        try {
            return Role::pluck('name')->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting available roles', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }
}
