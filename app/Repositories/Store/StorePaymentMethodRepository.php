<?php

namespace App\Repositories\Store;

use App\Models\StorePaymentMethod;
use App\Repositories\BaseRepository;

class StorePaymentMethodRepository extends BaseRepository
{
    public function __construct(StorePaymentMethod $model)
    {
        parent::__construct($model);
    }

    public function getEnabledMethods(int $storeId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function countEnabled(int $storeId): int
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('is_enabled', true)
            ->count();
    }
}
