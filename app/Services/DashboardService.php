<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductTagRepository;
use App\Repositories\BusinessCurrencyConfigRepository;
use App\Services\CurrencyRateService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardService
{
    private ProductRepository $productRepo;
    private ProductCategoryRepository $categoryRepo;
    private ProductTagRepository $tagRepo;
    private BusinessCurrencyConfigRepository $configRepo;
    private CurrencyRateService $currencyService;

    public function __construct(
        ProductRepository $productRepo,
        ProductCategoryRepository $categoryRepo,
        ProductTagRepository $tagRepo,
        BusinessCurrencyConfigRepository $configRepo,
        CurrencyRateService $currencyService
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->tagRepo = $tagRepo;
        $this->configRepo = $configRepo;
        $this->currencyService = $currencyService;
    }

    /**
     * Get complete dashboard data for a tenant.
     */
    public function getDashboardData($tenant): array
    {
        try {
            return [
                'auth' => [
                    'user' => auth()->user(),
                ],
                'current_store' => [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                ],
                'statistics' => $this->getBusinessStatistics($tenant),
                'recentActivity' => $this->getRecentActivity($tenant),
                'currencyStatus' => $this->getCurrencyStatus($tenant),
                'topProducts' => $this->getTopProducts($tenant),
                'alerts' => $this->getBusinessAlerts($tenant),
                'chartData' => $this->getChartData($tenant),
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard data error', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Unable to load dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Get business statistics.
     */
    public function getBusinessStatistics($tenant): array
    {
        return [
            'totalProducts' => $this->productRepo->countForTenant($tenant->id),
            'activeProducts' => $this->productRepo->countActiveForTenant($tenant->id),
            'lowStockProducts' => $this->productRepo->countLowStockForTenant($tenant->id),
            'totalCategories' => $this->categoryRepo->countForTenant($tenant->id),
            'totalTags' => $this->tagRepo->countForTenant($tenant->id),
            'recentProducts' => $this->productRepo->getLatestForTenant($tenant->id, 5),
        ];
    }

    /**
     * Get recent activity for the business.
     */
    public function getRecentActivity($tenant): array
    {
        return [
            'recentPriceChanges' => $this->productRepo->getRecentPriceChanges($tenant->id, 10),
            'newProducts' => $this->productRepo->getLatestForTenant($tenant->id, 5),
            'stockUpdates' => $this->productRepo->getRecentStockUpdates($tenant->id, 5),
        ];
    }

    /**
     * Get currency configuration status.
     */
    public function getCurrencyStatus($tenant): array
    {
        try {
            $config = $this->configRepo->getByTenantId($tenant->id);

            if (!$config) {
                return [
                    'configured' => false,
                    'message' => 'Currency configuration not set up',
                    'setupRequired' => true,
                ];
            }

            return [
                'configured' => true,
                'defaultCurrency' => $config->default_currency,
                'displayCurrencies' => $config->display_currencies,
                'lastRateUpdate' => $config->last_rate_update,
                'autoUpdateEnabled' => $config->auto_update_rates,
                'useCustomRates' => $config->use_custom_rates,
                'currentRates' => $this->currencyService->getSupportedCurrenciesWithRates($tenant->id),
                'setupRequired' => false,
            ];
        } catch (\Exception $e) {
            Log::error('Currency status error', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'configured' => false,
                'message' => 'Error loading currency configuration',
                'setupRequired' => true,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get top performing products.
     */
    public function getTopProducts($tenant): array
    {
        try {
            return [
                'byRevenue' => $this->productRepo->getTopByRevenue($tenant->id, 5),
                'byViews' => $this->productRepo->getTopByViews($tenant->id, 5),
                'bySales' => $this->productRepo->getTopBySales($tenant->id, 5),
            ];
        } catch (\Exception $e) {
            Log::error('Top products error', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'byRevenue' => [],
                'byViews' => [],
                'bySales' => [],
            ];
        }
    }

    /**
     * Get business alerts and notifications.
     */
    public function getBusinessAlerts($tenant): array
    {
        $alerts = [];

        try {
            // Low stock alerts
            $lowStock = $this->productRepo->countLowStockForTenant($tenant->id);
            if ($lowStock > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "{$lowStock} products with low stock",
                    'action' => 'view-products',
                    'priority' => 'high',
                    'count' => $lowStock,
                ];
            }

            // Currency update alerts
            $config = $this->configRepo->getByTenantId($tenant->id);
            if ($config && $config->auto_update_rates) {
                $daysSinceUpdate = $config->last_rate_update ?
                    Carbon::parse($config->last_rate_update)->diffInDays(now()) : 999;

                if ($daysSinceUpdate > 7) {
                    $alerts[] = [
                        'type' => 'info',
                        'message' => "Currency rates not updated for {$daysSinceUpdate} days",
                        'action' => 'update-rates',
                        'priority' => 'medium',
                        'daysSinceUpdate' => $daysSinceUpdate,
                    ];
                }
            }

            // No currency configuration alert
            if (!$config) {
                $alerts[] = [
                    'type' => 'error',
                    'message' => 'Currency configuration not set up',
                    'action' => 'setup-currency',
                    'priority' => 'high',
                ];
            }

        } catch (\Exception $e) {
            Log::error('Alerts error', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            $alerts[] = [
                'type' => 'error',
                'message' => 'Error loading alerts',
                'action' => 'refresh',
                'priority' => 'medium',
            ];
        }

        return $alerts;
    }

    /**
     * Get chart data for dashboard.
     */
    public function getChartData($tenant): array
    {
        try {
            return [
                'monthlyRevenue' => $this->productRepo->getMonthlyRevenue($tenant->id, 12),
                'productGrowth' => $this->productRepo->getProductGrowth($tenant->id, 6),
                'currencyPerformance' => $this->currencyService->getCurrencyPerformance($tenant->id, 30),
            ];
        } catch (\Exception $e) {
            Log::error('Chart data error', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'monthlyRevenue' => [],
                'productGrowth' => [],
                'currencyPerformance' => [],
            ];
        }
    }
}
