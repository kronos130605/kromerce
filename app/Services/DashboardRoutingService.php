<?php

namespace App\Services;

use App\Models\Store;
use App\Models\User;
use App\Repositories\Store\StoreStatisticsRepository;
use Illuminate\Support\Facades\Log;

class DashboardRoutingService
{
    public function __construct(
        private StoreService $storeService,
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
            $stores = $this->storeService->getUserStores($user);

            if ($stores->isNotEmpty()) {
                // Return current store if exists
                return $this->storeService->getUserCurrentStore($user);
            }

            // Create default store if none exists
            return $this->storeService->getOrCreateDefaultStoreForUser($user);

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
            // Check if user has business role even without store
            $userPrimaryRole = $this->roleService->getUserPrimaryRole($user);
            $businessRoles = config('roles.business_roles', ['business_owner']);

            // If user has business role but no store, show business dashboard
            if (!$store && in_array($userPrimaryRole, $businessRoles)) {
                return config('roles.dashboard_views.business', 'Business/Index');
            }

            // Si no hay store y no es rol de negocio, es un customer
            if (!$store) {
                return config('roles.dashboard_views.customer', 'Customer/Index');
            }

            if ($userPrimaryRole && in_array($userPrimaryRole, $businessRoles)) {
                // Usuario con rol de negocio - usar nuevo dashboard
                return config('roles.dashboard_views.business', 'Business/Index');
            } else {
                // Usuario sin rol de negocio - usar dashboard de customer
                return config('roles.dashboard_views.customer', 'Customer/Index');
            }

        } catch (\Exception $e) {
            Log::error('Error determining dashboard view', [
                'user_id' => $user->id,
                'store_id' => $store?->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback a customer dashboard
            return config('roles.dashboard_views.customer', 'Customer/Index');
        }
    }

    /**
     * Get available stores for user
     */
    public function getUserStores(User $user): array
    {
        try {
            // Obtener stores del usuario usando StoreService
            $stores = $this->storeService->getUserStores($user);

            return [
                'stores' => $stores,
                'current_store' => $this->storeService->getUserCurrentStore($user),
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
            if (!$this->storeService->getUserStores($user)->contains('id', $store->id)) {
                Log::warning('User trying to switch to unauthorized store', [
                    'user_id' => $user->id,
                    'store_id' => $store->id,
                ]);
                return false;
            }

            // Cambiar store actual
            return $this->storeService->setUserCurrentStore($user, $store);

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
     * Get dashboard data for user based on view and store
     */
    public function getDashboardDataForUser(User $user, ?Store $store, string $dashboardView): array
    {
        try {
            // Get user stores data efficiently
            $userStoresData = $this->getUserStores($user);

            $data = [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ], // Isolated user data - no model relationships
                'store' => $store ? [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                ] : null, // Isolated store data - no model relationships
                'stores' => [], // Empty array to avoid any relationship loading
            ];

            // Add store-specific data if store exists
            if ($store) {
                $data['statistics'] = $this->getStoreStatistics($store);
            }

            // Add view-specific data
            switch ($dashboardView) {
                case 'Business/Index':
                case 'modules/dashboard/pages/DashboardBusiness':
                    $data['activeTab'] = 'overview';
                    $data['canManageStore'] = $this->canUserManageStore($user, $store);
                    break;

                case 'Customer/Index':
                    $data['recentOrders'] = $this->getCustomerRecentOrders($user);
                    $data['wishlist'] = $this->getCustomerWishlist($user);
                    break;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Error getting dashboard data for user', [
                'user_id' => $user->id,
                'store_id' => $store?->id,
                'dashboard_view' => $dashboardView,
                'error' => $e->getMessage(),
            ]);

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'store' => $store ? [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                ] : null,
                'stores' => collect(), // Empty collection to avoid issues
                'error' => 'Failed to load dashboard data'
            ];
        }
    }

    /**
     * Check if user can manage store
     */
    private function canUserManageStore(User $user, ?Store $store): bool
    {
        if (!$store) {
            return false;
        }

        // Store owners can always manage their stores
        if ($store->owner_id === $user->id) {
            return true;
        }

        $userPrimaryRole = $this->roleService->getUserPrimaryRole($user);
        $manageableRoles = ['super_admin', 'business_owner', 'admin', 'manager'];

        return $userPrimaryRole ? in_array($userPrimaryRole, $manageableRoles) : false;
    }

    /**
     * Get customer's recent orders
     */
    private function getCustomerRecentOrders(User $user): array
    {
        // TODO: Implement customer orders logic
        return [];
    }

    /**
     * Get customer's wishlist
     */
    private function getCustomerWishlist(User $user): array
    {
        // TODO: Implement customer wishlist logic
        return [];
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
