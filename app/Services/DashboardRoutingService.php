<?php

namespace App\Services;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class DashboardRoutingService
{
    public function __construct(
        private TenantService $tenantService,
        private RoleService $roleService,
        private ProductService $productService
    ) {}

    /**
     * Determine which dashboard view to show based on user role and tenant
     */
    public function getDashboardViewForUser(User $user, ?Tenant $tenant): string
    {
        try {
            // Si no hay tenant, es un customer
            if (!$tenant) {
                Log::info('No tenant found, showing customer dashboard', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
                return 'Customer/Index';
            }

            // Usar RoleService para obtener el rol del usuario en el tenant
            // Ahora usa cache, así que no hay redundancia real con HandleInertiaRequests
            $userRoleInTenant = $this->roleService->getUserRoleInTenant($user, $tenant);

            // Verificar roles de negocio
            $businessRoles = ['business_owner', 'owner', 'admin', 'manager', 'employee'];

            if (in_array($userRoleInTenant, $businessRoles)) {
                // Usuario con rol de negocio - usar nuevo dashboard
                return 'Business/Index';
            } else {
                // Usuario sin rol de negocio - usar dashboard de customer
                return 'Customer/Index';
            }

        } catch (\Exception $e) {
            Log::error('Error determining dashboard view', [
                'user_id' => $user->id,
                'tenant_id' => $tenant?->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback a dashboard de customer
            return 'Customer/Index';
        }
    }

    /**
     * Get or assign tenant for user
     */
    public function getOrAssignTenantForUser(User $user): ?Tenant
    {
        try {
            // Obtener tenant actual del usuario
            $tenant = $this->tenantService->getUserCurrentTenant($user);

            // Si no tiene tenant actual, obtener el primero disponible
            if (!$tenant) {
                $firstTenant = $this->tenantService->getUserFirstTenant($user);

                if (!$firstTenant) {
                    // Asignar tenant por defecto según rol del usuario
                    $defaultTenant = $this->tenantService->getOrCreateDefaultTenantForUser($user);

                    if ($defaultTenant) {
                        // Asignar tenant por defecto al usuario
                        $assigned = $this->tenantService->assignTenantToUser($user, $defaultTenant);

                        if ($assigned) {
                            $tenant = $defaultTenant;

                            Log::info('User assigned to default tenant', [
                                'user_id' => $user->id,
                                'user_email' => $user->email,
                                'user_roles' => $user->roles->pluck('name')->toArray(),
                                'tenant_id' => $defaultTenant->id,
                                'tenant_slug' => $defaultTenant->slug,
                            ]);
                        } else {
                            Log::error('Failed to assign default tenant to user', [
                                'user_id' => $user->id,
                                'tenant_id' => $defaultTenant->id,
                            ]);
                            return null;
                        }
                    } else {
                        Log::error('Could not create default tenant for user', [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                        ]);
                        return null;
                    }
                } else {
                    // Establecer como tenant actual
                    $this->tenantService->setUserCurrentTenant($user, $firstTenant);
                    $tenant = $firstTenant;
                }
            }

            return $tenant;

        } catch (\Exception $e) {
            Log::error('Error getting or assigning tenant for user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get dashboard data for user
     */
    public function getDashboardDataForUser(User $user, ?Tenant $tenant, string $dashboardView): array
    {
        try {
            // Only get dashboard-specific data, user/tenant info is global
            $dashboardData = [];

            // Get products data for business dashboard
            if ($dashboardView === 'Business/Index' && $tenant) {
                try {
                    $productStatistics = $this->productService->getStatisticsForTenant($tenant);
                    $recentProducts = $this->productService->getLatestProductsForTenant($tenant, 5);
                    $lowStockProducts = $this->productService->getLowStockProductsForTenant($tenant, 5);

                    $dashboardData = [
                        'productStatistics' => $productStatistics,
                        'recentProducts' => $recentProducts,
                        'lowStockProducts' => $lowStockProducts,
                    ];
                } catch (\Exception $e) {
                    Log::error('Error getting product data for dashboard', [
                        'user_id' => $user->id,
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage(),
                    ]);

                    $dashboardData = [
                        'productStatistics' => [],
                        'recentProducts' => [],
                        'lowStockProducts' => [],
                    ];
                }
            }

            return $dashboardData;

        } catch (\Exception $e) {
            Log::error('Error getting dashboard data for user', [
                'user_id' => $user->id,
                'tenant_id' => $tenant?->id,
                'dashboard_view' => $dashboardView,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Check if user can access specific dashboard
     */
    public function userCanAccessDashboard(User $user, string $dashboardView): bool
    {
        try {
            $tenant = $this->tenantService->getUserCurrentTenant($user);

            if (!$tenant) {
                // Sin tenant, solo puede acceder a customer dashboard
                return $dashboardView === 'Customer/Index';
            }

            $userRole = $this->roleService->getUserRoleInTenant($user, $tenant);

            switch ($dashboardView) {
                case 'Business/Index':
                    $businessRoles = ['business_owner', 'admin', 'manager', 'employee'];
                    return in_array($userRole, $businessRoles);

                case 'Customer/Index':
                    return true; // Todos pueden acceder al customer dashboard

                default:
                    return false;
            }

        } catch (\Exception $e) {
            Log::error('Error checking dashboard access', [
                'user_id' => $user->id,
                'dashboard_view' => $dashboardView,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get available dashboards for user
     */
    public function getAvailableDashboardsForUser(User $user): array
    {
        try {
            $tenant = $this->tenantService->getUserCurrentTenant($user);

            $dashboards = [];

            // Customer dashboard siempre disponible
            $dashboards[] = [
                'name' => 'Customer/Index',
                'label' => 'Customer Dashboard',
                'accessible' => true,
            ];

            if ($tenant) {
                $userRole = $this->roleService->getUserRoleInTenant($user, $tenant);
                $businessRoles = ['business_owner', 'admin', 'manager', 'employee'];

                if (in_array($userRole, $businessRoles)) {
                    $dashboards[] = [
                        'name' => 'Business/Index',
                        'label' => 'Business Dashboard',
                        'accessible' => true,
                    ];
                }
            }

            return $dashboards;

        } catch (\Exception $e) {
            Log::error('Error getting available dashboards for user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                [
                    'name' => 'Customer/Index',
                    'label' => 'Customer Dashboard',
                    'accessible' => true,
                ]
            ];
        }
    }
}
