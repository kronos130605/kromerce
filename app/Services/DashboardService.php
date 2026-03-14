<?php

namespace App\Services;

use App\Repositories\BusinessCurrencyConfigRepository;
use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductTagRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
     * Get complete dashboard data for a store.
     */
    public function getDashboardData($store): array
    {
        try {
            return [
                'auth' => [
                    'user' => auth()->user(),
                ],
                'current_store' => [
                    'id' => $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                ],
                'statistics' => $this->getBusinessStatistics($store),
                'recentActivity' => $this->getRecentActivity($store),
                'currencyStatus' => $this->getCurrencyStatus($store),
                'topProducts' => $this->getTopProducts($store),
                'alerts' => $this->getBusinessAlerts($store),
                'chartData' => $this->getChartData($store),
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard data error', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Unable to load dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Get business statistics.
     */
    public function getBusinessStatistics($store): array
    {
        return [
            'totalProducts' => $this->productRepo->countForStore($store->id),
            'activeProducts' => $this->productRepo->countActiveForStore($store->id),
            'lowStockProducts' => $this->productRepo->countLowStockForStore($store->id),
            'totalCategories' => $this->categoryRepo->countForStore($store->id),
            'totalTags' => $this->tagRepo->countForStore($store->id),
            'recentProducts' => $this->productRepo->getLatestForStore($store->id, 5),
        ];
    }

    /**
     * Get recent activity for the business.
     */
    public function getRecentActivity($store): array
    {
        return [
            'recentPriceChanges' => $this->productRepo->getRecentPriceChanges($store->id, 10),
            'newProducts' => $this->productRepo->getLatestForStore($store->id, 5),
            'stockUpdates' => $this->productRepo->getRecentStockUpdates($store->id, 5),
        ];
    }

    /**
     * Get currency configuration status.
     */
    public function getCurrencyStatus($store): array
    {
        try {
            $config = $this->configRepo->getByStoreId($store->id);

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
                'currentRates' => $this->currencyService->getSupportedCurrenciesWithRates($store->id),
                'setupRequired' => false,
            ];
        } catch (\Exception $e) {
            Log::error('Currency status error', [
                'store_id' => $store->id,
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
    public function getTopProducts($store): array
    {
        try {
            return [
                'byRevenue' => $this->productRepo->getTopByRevenue($store->id, 5),
                'byViews' => $this->productRepo->getTopByViews($store->id, 5),
                'bySales' => $this->productRepo->getTopBySales($store->id, 5),
            ];
        } catch (\Exception $e) {
            Log::error('Top products error', [
                'store_id' => $store->id,
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
    public function getBusinessAlerts($store): array
    {
        $alerts = [];

        try {
            // Low stock alerts
            $lowStock = $this->productRepo->countLowStockForStore($store->id);
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
            $config = $this->configRepo->getByStoreId($store->id);
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
                'store_id' => $store->id,
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
    public function getChartData($store): array
    {
        try {
            return [
                'monthlyRevenue' => $this->productRepo->getMonthlyRevenue($store->id, 12),
                'productGrowth' => $this->productRepo->getProductGrowth($store->id, 6),
                'currencyPerformance' => $this->currencyService->getCurrencyPerformance($store->id, 30),
            ];
        } catch (\Exception $e) {
            Log::error('Chart data error', [
                'store_id' => $store->id,
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
