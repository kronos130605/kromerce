<?php

namespace App\Repositories\Store;

use App\Models\Store;
use App\Repositories\BaseRepository;

class StoreStatisticsRepository extends BaseRepository
{
    public function __construct(Store $model)
    {
        parent::__construct($model);
    }

    public function getProductsCount(int $storeId): int
    {
        return $this->model::find($storeId)->products()->count();
    }

    public function getActiveProductsCount(int $storeId): int
    {
        return $this->model::find($storeId)->products()
            ->where('status', 'active')
            ->count();
    }

    public function getCategoriesCount(int $storeId): int
    {
        return $this->model::find($storeId)->categories()->count();
    }

    public function getOrdersCount(int $storeId): int
    {
        return $this->model::find($storeId)->orders()->count();
    }

    public function getTotalRevenue(int $storeId): float
    {
        return $this->model::find($storeId)->orders()->sum('total_amount');
    }

    public function getRecentOrders(int $storeId, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model::find($storeId)->orders()
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getLowStockProductsCount(int $storeId): int
    {
        return $this->model::find($storeId)->products()
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
    }

    public function getContactsCount(int $storeId): int
    {
        return $this->model::find($storeId)->contacts()->count();
    }

    public function getPrimaryContactsCount(int $storeId): int
    {
        return $this->model::find($storeId)->contacts()
            ->where('is_primary', true)
            ->count();
    }

    public function getPublicContactsCount(int $storeId): int
    {
        return $this->model::find($storeId)->contacts()
            ->where('is_public', true)
            ->count();
    }

    public function getPaymentMethodsCount(int $storeId): int
    {
        return $this->model::find($storeId)->paymentMethods()->count();
    }

    public function getActivePaymentMethodsCount(int $storeId): int
    {
        return $this->model::find($storeId)->paymentMethods()
            ->where('is_enabled', true)
            ->count();
    }

    public function getUsersCount(int $storeId): int
    {
        return $this->model::find($storeId)->users()->count();
    }

    public function getActiveUsersCount(int $storeId): int
    {
        return $this->model::find($storeId)->users()
            ->where('is_active', true)
            ->count();
    }
}
