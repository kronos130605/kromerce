<?php

namespace App\Repositories\Store;

use App\Models\StoreContact;
use App\Repositories\BaseRepository;

class StoreContactRepository extends BaseRepository
{
    public function __construct(StoreContact $model)
    {
        parent::__construct($model);
    }

    public function countByType(int $storeId, string $type): int
    {
        return $this->model::where('store_id', $storeId)
            ->where('type', $type)
            ->count();
    }
}
