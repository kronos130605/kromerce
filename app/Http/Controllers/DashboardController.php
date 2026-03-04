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
    private DashboardRoutingService $dashboardRoutingService;

    public function __construct(
        DashboardRoutingService $dashboardRoutingService
    ) {
        $this->dashboardRoutingService = $dashboardRoutingService;
    }

    /**
     * Display the appropriate dashboard based on user role and tenant.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Usar DashboardRoutingService para obtener o asignar tenant
        $tenant = $this->dashboardRoutingService->getOrAssignTenantForUser($user);

        if (!$tenant) {
            // No se pudo asignar tenant - mostrar dashboard de customer básico
            return Inertia::render('modules/dashboard/pages/DashboardCustomer', [
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

            return Inertia::render('modules/dashboard/pages/Error', [
                'error' => 'Unable to load dashboard data',
                'user' => $user,
                'tenant' => $tenant,
                'details' => $e->getMessage(),
            ]);
        }

        return Inertia::render($dashboardView, $dashboardData);
    }
}
