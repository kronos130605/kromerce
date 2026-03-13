<?php

namespace App\Services;

use App\Models\User;
use App\Models\Store;
use App\Repositories\StoreRepository;
use App\Repositories\UserTenantRepository;
use Illuminate\Support\Facades\Log;

class TenantService
{
    private StoreRepository $storeRepository;
    private UserTenantRepository $userTenantRepository;

    public function __construct(
        StoreRepository $storeRepository,
        UserTenantRepository $userTenantRepository
    ) {
        $this->storeRepository = $storeRepository;
        $this->userTenantRepository = $userTenantRepository;
    }

    /**
     * Get or create default store for user based on role
     */
    public function getOrCreateDefaultStoreForUser(User $user): ?Store
    {
        try {
            $userRoles = $user->roles->pluck('name')->toArray();

            // Determinar store slug según rol prioritario
            $storeSlug = $this->getDefaultStoreSlugForRoles($userRoles);

            // Buscar store existente
            $store = $this->userTenantRepository->getUserFirstStore($user);

            if (!$store) {
                // Crear store por defecto
                $store = $this->createDefaultStore($storeSlug, $userRoles);
            }

            return $store;

        } catch (\Exception $e) {
            Log::error('Error getting or creating default store for user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Assign store to user with appropriate role
     */
    public function assignStoreToUser(User $user, Store $store, ?string $role = null): bool
    {
        try {
            $role = $role ?? $this->getDefaultRoleForUser($user);

            // Usar UserTenantRepository para asignar store
            return $this->userTenantRepository->attachUserToStore($user, $store, $role);

        } catch (\Exception $e) {
            Log::error('Error assigning store to user', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'role' => $role,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get default role for user in store
     */
    public function getDefaultRoleForUser(User $user): string
    {
        $userRoles = $user->roles->pluck('name')->toArray();

        // Prioridad de roles para store
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
     * Get default store slug based on user roles
     */
    public function getDefaultStoreSlugForRoles(array $userRoles): string
    {
        // Si tiene rol de negocio, usar store de negocio por defecto
        $businessRoles = ['business_owner', 'admin', 'manager', 'employee'];

        foreach ($businessRoles as $role) {
            if (in_array($role, $userRoles)) {
                return 'business-default';
            }
        }

        // Si solo es customer, usar store de customer por defecto
        return 'customers-default';
    }

    /**
     * Create default store with appropriate settings
     */
    public function createDefaultStore(string $slug, array $userRoles): ?Store
    {
        try {
            $settings = $this->getDefaultStoreSettings($slug, $userRoles);
            $uuid = \Illuminate\Support\Str::uuid();

            // Obtener el primer usuario disponible como owner
            $ownerId = $this->storeRepository->getAvailableOwnerId();

            if (!$ownerId) {
                throw new \Exception('No available user found for owner_id');
            }

            // Usar repositorio para crear store
            $storeId = $this->storeRepository->createDirect([
                'uuid' => $uuid,
                'name' => $this->getDefaultStoreName($slug),
                'slug' => $slug,
                'data' => json_encode($settings),
                'status' => 'active',
                'owner_id' => $ownerId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Obtener el store creado
            $store = $this->storeRepository->findById($storeId);

            if (!$store) {
                throw new \Exception('Failed to retrieve created store');
            }

            Log::info('Default store created successfully', [
                'store_id' => $store->id,
                'store_uuid' => $store->uuid,
                'store_slug' => $slug,
                'owner_id' => $ownerId,
                'user_roles' => $userRoles,
            ]);

            return $store;

        } catch (\Exception $e) {
            Log::error('Error creating default store', [
                'slug' => $slug,
                'user_roles' => $userRoles,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Get default store name
     */
    public function getDefaultStoreName(string $slug): string
    {
        return match ($slug) {
            'business-default' => 'Business Default',
            'customers-default' => 'Customers Default',
            default => 'Default Store',
        };
    }

    /**
     * Get default store settings
     */
    public function getDefaultStoreSettings(string $slug, array $userRoles): array
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
     * Check if user has any store
     */
    public function userHasStores(User $user): bool
    {
        return $this->userTenantRepository->userHasStores($user);
    }

    /**
     * Get user's first store
     */
    public function getUserFirstStore(User $user): ?Store
    {
        return $this->userTenantRepository->getUserFirstStore($user);
    }

    /**
     * Get user's current store
     */
    public function getUserCurrentStore(User $user): ?Store
    {
        return $this->userTenantRepository->getUserCurrentStore($user);
    }

    /**
     * Set user's current store
     */
    public function setUserCurrentStore(User $user, Store $store): bool
    {
        return $this->userTenantRepository->setUserCurrentStore($user, $store);
    }

    /**
     * Get all stores for user
     */
    public function getUserStores(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->userTenantRepository->getUserStores($user);
    }

    /**
     * Remove user from store
     */
    public function removeUserFromStore(User $user, Store $store): bool
    {
        return $this->userTenantRepository->detachUserFromStore($user, $store);
    }
}
