<?php

namespace App\Http\Controllers;

use App\Services\ProductPricingService;
use App\Repositories\ProductRepository;
use App\Services\CurrencyRateService;
use App\Repositories\BusinessCurrencyConfigRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private ProductPricingService $pricingService;
    private ProductRepository $productRepo;
    private CurrencyRateService $currencyService;
    private BusinessCurrencyConfigRepository $configRepo;

    public function __construct(
        ProductPricingService $pricingService,
        ProductRepository $productRepo,
        CurrencyRateService $currencyService,
        BusinessCurrencyConfigRepository $configRepo
    ) {
        $this->pricingService = $pricingService;
        $this->productRepo = $productRepo;
        $this->currencyService = $currencyService;
        $this->configRepo = $configRepo;
    }

    /**
     * Display the appropriate dashboard based on user role.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $tenant = $user->tenant;
        
        // Determine which dashboard to show based on user role
        $dashboardView = $this->getDashboardView($user);
        
        // Get dashboard data using services and repositories
        $dashboardData = $this->getDashboardData($tenant);
        
        // Pass common data to all dashboards
        $commonData = array_merge([
            'auth' => [
                'user' => $user,
            ],
        ], $dashboardData);
        
        return Inertia::render($dashboardView, $commonData);
    }

    /**
     * Determine which dashboard view to show.
     */
    private function getDashboardView($user): string
    {
        // Logic to determine dashboard based on user role
        return 'Dashboard/Index';
    }

    /**
     * Get dashboard data using services and repositories.
     */
    private function getDashboardData($tenant): array
    {
        return [
            'statistics' => $this->productRepo->getStatistics($tenant->id),
            'currencyConfig' => $this->configRepo->getByTenantId($tenant->id),
            'recentProducts' => $this->productRepo->getLatestForTenant($tenant->id, 5),
        ];
    }
}
