<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Repositories\TenantRepository;
use App\Repositories\UserTenantRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantService
{
    private TenantRepository $tenantRepository;
    private UserTenantRepository $userTenantRepository;

    public function __construct(
        TenantRepository $tenantRepository,
        UserTenantRepository $userTenantRepository
    ) {
        $this->tenantRepository = $tenantRepository;
        $this->userTenantRepository = $userTenantRepository;
    }
    /**
     * Get or create default tenant for user based on role
     */
    public function getOrCreateDefaultTenantForUser(User $user): ?Tenant
    {
        try {
            $userRoles = $user->roles->pluck('name')->toArray();
            
            // Determinar tenant slug según rol prioritario
            $tenantSlug = $this->getDefaultTenantSlugForRoles($userRoles);
            
            // Buscar tenant existente
            $tenant = $this->tenantRepository->findBySlug($tenantSlug);
            
            if (!$tenant) {
                // Crear tenant por defecto
                $tenant = $this->createDefaultTenant($tenantSlug, $userRoles);
            }
            
            return $tenant;
            
        } catch (\Exception $e) {
            Log::error('Error getting or creating default tenant for user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            
            return null;
        }
    }

    /**
     * Assign tenant to user with appropriate role
     */
    public function assignTenantToUser(User $user, Tenant $tenant, ?string $role = null): bool
    {
        try {
            $role = $role ?? $this->getDefaultRoleForUser($user);
            
            // Usar repositorio para asignar tenant
            return $this->userTenantRepository->attachUserToTenant($user, $tenant, $role);
            
        } catch (\Exception $e) {
            Log::error('Error assigning tenant to user', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get default role for user in tenant
     */
    public function getDefaultRoleForUser(User $user): string
    {
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Prioridad de roles para tenant
        $rolePriority = [
            'business_owner' => 1,
            'admin' => 2,
            'manager' => 3,
            'employee' => 4,
            'customer' => 5,
        ];
        
        $defaultRole = 'customer';
        $highestPriority = PHP_INT_MAX;
        
        foreach ($userRoles as $role) {
            if (isset($rolePriority[$role]) && $rolePriority[$role] < $highestPriority) {
                $defaultRole = $role;
                $highestPriority = $rolePriority[$role];
            }
        }
        
        return $defaultRole;
    }

    /**
     * Get default tenant slug based on user roles
     */
    public function getDefaultTenantSlugForRoles(array $userRoles): string
    {
        // Si tiene rol de negocio, usar tenant de negocio por defecto
        $businessRoles = ['business_owner', 'admin', 'manager', 'employee'];
        
        foreach ($businessRoles as $role) {
            if (in_array($role, $userRoles)) {
                return 'business-default';
            }
        }
        
        // Si solo es customer, usar tenant de customer por defecto
        return 'customers-default';
    }

    /**
     * Create default tenant with appropriate settings
     */
    public function createDefaultTenant(string $slug, array $userRoles): ?Tenant
    {
        try {
            $settings = $this->getDefaultTenantSettings($slug, $userRoles);
            $uuid = \Illuminate\Support\Str::uuid();
            
            // Obtener el primer usuario disponible como owner
            $ownerId = $this->tenantRepository->getAvailableOwnerId();
            
            if (!$ownerId) {
                throw new \Exception('No available user found for owner_id');
            }
            
            // Usar repositorio para crear tenant
            $tenantId = $this->tenantRepository->createDirect([
                'uuid' => $uuid,
                'name' => $this->getDefaultTenantName($slug),
                'slug' => $slug,
                'data' => json_encode($settings),
                'is_active' => true,
                'owner_id' => $ownerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Obtener el tenant creado
            $tenant = $this->tenantRepository->findById($tenantId);
            
            if (!$tenant) {
                throw new \Exception('Failed to retrieve created tenant');
            }
            
            Log::info('Default tenant created successfully', [
                'tenant_id' => $tenant->id,
                'tenant_uuid' => $tenant->uuid,
                'tenant_slug' => $slug,
                'owner_id' => $ownerId,
                'user_roles' => $userRoles,
            ]);
            
            return $tenant;
            
        } catch (\Exception $e) {
            Log::error('Error creating default tenant', [
                'slug' => $slug,
                'user_roles' => $userRoles,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return null;
        }
    }

    /**
     * Get default tenant name
     */
    public function getDefaultTenantName(string $slug): string
    {
        return match ($slug) {
            'business-default' => 'Business Default',
            'customers-default' => 'Customers Default',
            default => 'Default Tenant',
        };
    }

    /**
     * Get default tenant settings
     */
    public function getDefaultTenantSettings(string $slug, array $userRoles): array
    {
        $baseSettings = [
            'theme' => 'default',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'default_currency' => 'USD',
            'language' => 'es',
            'timezone' => 'America/Mexico_City',
            'enable_notifications' => true,
            'created_by_system' => true,
        ];

        if ($slug === 'customers-default') {
            return array_merge($baseSettings, [
                'show_flash_sales' => true,
                'show_featured_stores' => true,
                'show_ai_recommendations' => true,
                'enable_wishlist' => true,
                'enable_reviews' => true,
                'layout' => [
                    'sidebar_position' => 'left',
                    'product_grid_columns' => 4,
                    'show_product_ratings' => true,
                    'show_product_compare' => true,
                ],
            ]);
        }

        if ($slug === 'business-default') {
            return array_merge($baseSettings, [
                'show_analytics' => true,
                'show_inventory_management' => true,
                'show_order_management' => true,
                'show_customer_management' => true,
                'show_financial_reports' => true,
                'enable_multi_currency' => true,
                'layout' => [
                    'sidebar_position' => 'left',
                    'default_dashboard_view' => 'overview',
                    'show_quick_actions' => true,
                    'show_recent_activity' => true,
                ],
            ]);
        }

        return $baseSettings;
    }

    /**
     * Check if user has any tenant
     */
    public function userHasTenants(User $user): bool
    {
        return $this->userTenantRepository->userHasTenants($user);
    }

    /**
     * Get user's first tenant
     */
    public function getUserFirstTenant(User $user): ?Tenant
    {
        return $this->userTenantRepository->getUserFirstTenant($user);
    }

    /**
     * Get user's current tenant
     */
    public function getUserCurrentTenant(User $user): ?Tenant
    {
        return $this->userTenantRepository->getUserCurrentTenant($user);
    }

    /**
     * Set user's current tenant
     */
    public function setUserCurrentTenant(User $user, Tenant $tenant): bool
    {
        return $this->userTenantRepository->setUserCurrentTenant($user, $tenant);
    }

    /**
     * Get all tenants for user
     */
    public function getUserTenants(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userTenantRepository->getUserTenants($user);
    }

    /**
     * Remove user from tenant
     */
    public function removeUserFromTenant(User $user, Tenant $tenant): bool
    {
        return $this->userTenantRepository->detachUserFromTenant($user, $tenant);
    }
}
