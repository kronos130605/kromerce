<?php

namespace App\Http\Controllers;

use App\Helpers\TranslationHelper;
use App\Services\DashboardRoutingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardRoutingService $dashboardRoutingService,
    ) {}

    /**
     * Display appropriate dashboard based on user role and store.
     */
    public function index(Request $request): Response
    {
        try {
            $user = $request->user();

            // Use DashboardRoutingService to get or assign store for user
            $store = $this->dashboardRoutingService->getOrAssignStoreForUser($user);

            // Get dashboard view and data using service
            $dashboardView = $this->dashboardRoutingService->getDashboardViewForUser($user, $store);
            $dashboardData = $this->dashboardRoutingService->getDashboardDataForUser($user, $store, $dashboardView);

            // For business users, use the new SPA structure
            if ($dashboardView === 'modules/dashboard/pages/DashboardBusiness') {
                return Inertia::render('Business/Index', array_merge($dashboardData, [
                    'activeTab'      => 'overview',
                    'translations'   => TranslationHelper::forPreset('dashboard'),
                ]));
            }

            return Inertia::render($dashboardView, array_merge($dashboardData, [
                'translations' => TranslationHelper::forPreset('dashboard'),
            ]));

        } catch (\Exception $e) {
            Log::error('Dashboard error', [
                'user_id' => $request->user()?->id,
                'store_id' => session('current_store_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('modules/dashboard/pages/Error', [
                'error' => 'Failed to load dashboard',
                'message' => $e->getMessage(),
                'user' => $request->user(),
            ]);
        }
    }
}
