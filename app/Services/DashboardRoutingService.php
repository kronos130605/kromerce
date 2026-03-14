<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use App\Repositories\Store\StoreStatisticsRepository;
use Illuminate\Support\Facades\Log;

class DashboardRoutingService
{
    public function __construct(
        private TenantService $tenantService,
        private RoleService $roleService,
        private StoreStatisticsRepository $statisticsRepo
    ) {}

    /**
     * Get or assign store for user
     */
    public function getOrAssignStoreForUser(User $user): ?Store
    {
        try {
            // Get existing stores for user
            $stores = $this->tenantService->getUserStores($user);

            if ($stores->isNotEmpty()) {
                // Return current store if exists
                return $this->tenantService->getUserCurrentStore($user);
            }

            // Create default store if none exists
            return $this->tenantService->getOrCreateDefaultStoreForUser($user);

        } catch (\Exception $e) {
            Log::error('Error getting or assigning store for user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Determine which dashboard view to show based on user role and store
     */
    public function getDashboardViewForUser(User $user, ?Store $store): string
    {
        try {
            // Si no hay store, es un customer
            if (!$store) {
                Log::info('No store found, showing customer dashboard', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
                return 'Customer/Index';
            }

            // Usar RoleService para obtener el rol del usuario en el store
            // Ahora usa cache, así que no hay redundancia real con HandleInertiaRequests
            $userRoleInStore = $this->roleService->getUserRoleInStore($user, $store);

            // Verificar roles de negocio
            $businessRoles = ['business_owner', 'owner', 'admin', 'manager', 'employee'];

            if (in_array($userRoleInStore, $businessRoles)) {
                // Usuario con rol de negocio - usar nuevo dashboard
                return 'Business/Index';
            } else {
                // Usuario sin rol de negocio - usar dashboard de customer
                return 'Customer/Index';
            }

        } catch (\Exception $e) {
            Log::error('Error determining dashboard view', [
                'user_id' => $user->id,
                'store_id' => $store?->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback a customer dashboard
            return 'Customer/Index';
        }
    }

    /**
     * Get available stores for user
     */
    public function getUserStores(User $user): array
    {
        try {
            // Obtener stores del usuario usando TenantService
            $stores = $this->tenantService->getUserStores($user);

            return [
                'stores' => $stores,
                'current_store' => $this->tenantService->getUserCurrentStore($user),
                'total_stores' => $stores->count(),
            ];

        } catch (\Exception $e) {
            Log::error('Error getting user stores', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'stores' => collect(),
                'current_store' => null,
                'total_stores' => 0,
            ];
        }
    }

    /**
     * Switch user to different store
     */
    public function switchUserStore(User $user, Store $store): bool
    {
        try {
            // Verificar que el usuario tiene acceso a este store
            if (!$this->tenantService->getUserStores($user)->contains('id', $store->id)) {
                Log::warning('User trying to switch to unauthorized store', [
                    'user_id' => $user->id,
                    'store_id' => $store->id,
                ]);
                return false;
            }

            // Cambiar store actual
            return $this->tenantService->setUserCurrentStore($user, $store);

        } catch (\Exception $e) {
            Log::error('Error switching user store', [
                'user_id' => $user->id,
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get store statistics for dashboard
     */
    public function getStoreStatistics(Store $store): array
    {
        try {
            return [
                'total_products' => $this->statisticsRepo->getProductsCount($store->id),
                'active_products' => $this->statisticsRepo->getActiveProductsCount($store->id),
                'total_categories' => $this->statisticsRepo->getCategoriesCount($store->id),
                'total_orders' => $this->statisticsRepo->getOrdersCount($store->id),
                'total_revenue' => $this->statisticsRepo->getTotalRevenue($store->id),
                'recent_orders' => $this->statisticsRepo->getRecentOrders($store->id),
                'low_stock_products' => $this->statisticsRepo->getLowStockProductsCount($store->id),
            ];

        } catch (\Exception $e) {
            Log::error('Error getting store statistics', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
