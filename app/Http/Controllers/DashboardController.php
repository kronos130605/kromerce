<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\DashboardService;
use App\Services\DashboardRoutingService;
use App\Services\ProductPricingService;
use App\Services\RoleService;
use App\Services\TenantService;
use App\Repositories\ProductRepository;
use App\Services\CurrencyRateService;
use App\Repositories\BusinessCurrencyConfigRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;
    private ProductPricingService $pricingService;
    private ProductRepository $productRepo;
    private CurrencyRateService $currencyService;
    private BusinessCurrencyConfigRepository $configRepo;
    private RoleService $roleService;
    private DashboardRoutingService $dashboardRoutingService;

    public function __construct(
        DashboardService $dashboardService,
        ProductPricingService $pricingService,
        ProductRepository $productRepo,
        CurrencyRateService $currencyService,
        BusinessCurrencyConfigRepository $configRepo,
        RoleService $roleService,
        DashboardRoutingService $dashboardRoutingService
    ) {
        $this->dashboardService = $dashboardService;
        $this->pricingService = $pricingService;
        $this->productRepo = $productRepo;
        $this->currencyService = $currencyService;
        $this->configRepo = $configRepo;
        $this->roleService = $roleService;
        $this->dashboardRoutingService = $dashboardRoutingService;
    }

    /**
     * Display the appropriate dashboard based on user role and tenant.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Debug: Log user info
        \Log::info('Dashboard access attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'is_business_owner' => $user->isBusinessOwner(),
            'current_tenant_id' => $user->currentTenant()?->id,
            'all_tenants' => $user->tenants->pluck('id')->toArray(),
        ]);

        // Usar DashboardRoutingService para obtener o asignar tenant
        $tenant = $this->dashboardRoutingService->getOrAssignTenantForUser($user);
        
        if (!$tenant) {
            // No se pudo asignar tenant - mostrar dashboard de customer básico
            return Inertia::render('DashboardCustomer', [
                'auth' => [
                    'user' => $user,
                ],
                'user_role' => 'customer',
                'tenant' => null,
            ]);
        }

        // Determinar qué dashboard mostrar
        $dashboardView = $this->dashboardRoutingService->getDashboardViewForUser($user, $tenant);

        // Obtener datos del dashboard
        try {
            $dashboardData = $this->dashboardRoutingService->getDashboardDataForUser($user, $tenant, $dashboardView);
        } catch (\Exception $e) {
            // Log error y mostrar página de error
            \Log::error('Dashboard data error', [
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Dashboard/Error', [
                'error' => 'Unable to load dashboard data',
                'user' => $user,
                'tenant' => $tenant,
                'details' => $e->getMessage(),
            ]);
        }

        return Inertia::render($dashboardView, $dashboardData);
    }
}
