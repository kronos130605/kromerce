<?php

namespace App\Repositories\Store;

use App\Models\Store;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Schema;

class StoreStatisticsRepository extends BaseRepository
{
    public function __construct(Store $model)
    {
        parent::__construct($model);
    }

    public function getProductsCount(int $storeId): int
    {
        if (!Schema::hasTable('products')) {
            return 0;
        }
        
        try {
            return $this->model->newQuery()
                ->where('id', $storeId)
                ->withCount('products')
                ->value('products_count') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getActiveProductsCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return 0;
        }
        return $store->products()->where('status', 'active')->count();
    }

    public function getCategoriesCount(int $storeId): int
    {
        if (!Schema::hasTable('product_categories')) {
            return 0;
        }
        
        try {
            return $this->model->newQuery()
                ->where('id', $storeId)
                ->withCount('categories')
                ->value('categories_count') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getOrdersCount(int $storeId): int
    {
        if (!Schema::hasTable('orders')) {
            return 0;
        }
        
        try {
            return $this->model->newQuery()
                ->where('id', $storeId)
                ->withCount('orders')
                ->value('orders_count') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getTotalRevenue(int $storeId): float
    {
        if (!Schema::hasTable('orders')) {
            return 0;
        }
        
        try {
            return $this->model->newQuery()
                ->where('id', $storeId)
                ->withSum('orders', 'total_amount')
                ->value('orders_sum_total_amount') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getRecentOrders(int $storeId, int $limit = 5): \Illuminate\Support\Collection
    {
        if (!Schema::hasTable('orders')) {
            return collect();
        }
        
        try {
            $store = $this->getById($storeId);
            if (!$store) {
                return collect();
            }
            return $store->orders()
                ->latest()
                ->take($limit)
                ->get(['id', 'order_number', 'total_amount', 'status', 'created_at']);
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function getLowStockProductsCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return 0;
        }
        return $store->products()
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
    }

    public function getContactsCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        return $store ? $store->contacts()->count() : 0;
    }

    public function getPrimaryContactsCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return 0;
        }
        return $store->contacts()->where('is_primary', true)->count();
    }

    public function getPublicContactsCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return 0;
        }
        return $store->contacts()->where('is_public', true)->count();
    }

    public function getPaymentMethodsCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        return $store ? $store->paymentMethods()->count() : 0;
    }

    public function getActivePaymentMethodsCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return 0;
        }
        return $store->paymentMethods()->where('is_enabled', true)->count();
    }

    public function getUsersCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        return $store ? $store->users()->count() : 0;
    }

    public function getActiveUsersCount(int $storeId): int
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return 0;
        }
        return $store->users()->where('is_active', true)->count();
    }
}
