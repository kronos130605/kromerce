<?php

namespace App\Http\Controllers;

use App\Services\DashboardRoutingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
            $tenant = $this->validateTenant();
            
            // Get dashboard data using service
            $dashboardData = $this->dashboardRoutingService->getDashboardDataForUser($user, $tenant);
            
            return Inertia::render($dashboardData['view'], $dashboardData['data']);
            
        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'user_id' => $request->user()?->id,
                'tenant_id' => tenant()?->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->error('Failed to load dashboard', 500);
        }
    }
}
