<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository extends BaseRepository
{
    protected array $allowedFields = [
        'id',
        'uuid',
        'store_id',
        'customer_id',
        'order_number',
        'status',
        'payment_status',
        'fulfillment_status',
        'currency',
        'total',
        'created_at',
        'updated_at',
    ];

    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * Get paginated orders for store with filters.
     */
    public function paginateForStore(int $storeId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with(['customer', 'items'])
            ->where('store_id', $storeId);

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('uuid', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Order by created_at desc by default
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->paginate($perPage);
    }

    /**
     * Get order statistics for store.
     */
    public function getStatistics(int $storeId): array
    {
        $baseQuery = $this->model->where('store_id', $storeId);

        return [
            'total_orders' => (int) $baseQuery->clone()->count(),
            'pending_orders' => (int) $baseQuery->clone()->where('status', 'pending')->count(),
            'processing_orders' => (int) $baseQuery->clone()->where('status', 'processing')->count(),
            'shipped_orders' => (int) $baseQuery->clone()->where('status', 'shipped')->count(),
            'delivered_orders' => (int) $baseQuery->clone()->where('status', 'delivered')->count(),
            'cancelled_orders' => (int) $baseQuery->clone()->where('status', 'cancelled')->count(),
            'total_revenue' => (float) $baseQuery->clone()->where('payment_status', 'paid')->sum('total'),
            'pending_revenue' => (float) $baseQuery->clone()->where('payment_status', 'pending')->sum('total'),
        ];
    }

    /**
     * Get order by UUID for store.
     */
    public function getByUuidForStore(string $uuid, int $storeId): ?Order
    {
        return $this->model
            ->where('uuid', $uuid)
            ->where('store_id', $storeId)
            ->first();
    }
}
