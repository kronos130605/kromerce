<?php

namespace App\Http\Controllers;

use App\Services\DashboardRoutingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardRoutingService $dashboardRoutingService
    ) {}

    /**
     * Display appropriate dashboard based on user role and tenant.
     */
    public function index(Request $request): Response
    {
        try {
            $user = $request->user();
            
            // Use DashboardRoutingService to get or assign tenant for user
            $tenant = $this->dashboardRoutingService->getOrAssignTenantForUser($user);
            
            // Get dashboard view and data using service
            $dashboardView = $this->dashboardRoutingService->getDashboardViewForUser($user, $tenant);
            $dashboardData = $this->dashboardRoutingService->getDashboardDataForUser($user, $tenant, $dashboardView);
            
            // For business users, use the new SPA structure
            if ($dashboardView === 'modules/dashboard/pages/DashboardBusiness') {
                return Inertia::render('Business/Index', array_merge($dashboardData, [
                    'activeTab' => 'overview'
                ]));
            }
            
            return Inertia::render($dashboardView, $dashboardData);
            
        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'user_id' => $request->user()?->id,
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return error page with Inertia instead of JsonResponse
            return Inertia::render('modules/dashboard/pages/Error', [
                'error' => 'Failed to load dashboard',
                'message' => $e->getMessage(),
                'user' => $request->user(),
            ]);
        }
    }
}
