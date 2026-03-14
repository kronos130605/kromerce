<?php

namespace App\Repositories\Store;

use App\Models\Store;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StoreRepository extends BaseRepository
{
    protected array $allowedFields = [
        'name', 'slug', 'description', 'logo', 'banner', 'business_type',
        'status', 'tax_id', 'verified_business', 'website_url', 'timezone',
        'owner_id', 'uuid', 'data', 'created_at', 'updated_at'
    ];

    public function __construct(Store $model)
    {
        parent::__construct($model);
    }

    /**
     * Get basic store data for frontend (optimized for Inertia).
     * Returns array instead of model to avoid serialization issues.
     */
    public function getBasicStoreData(int $storeId): ?array
    {
        $store = DB::table('stores')
            ->where('id', $storeId)
            ->select(['id', 'name', 'slug', 'business_type', 'status'])
            ->first();

        if (!$store) {
            return null;
        }

        return [
            'id' => $store->id,
            'name' => $store->name,
            'slug' => $store->slug,
            'business_type' => $store->business_type,
            'status' => $store->status,
        ];
    }

    /**
     * Search stores.
     */
    public function search(string $query): Collection
    {
        return $this->model->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
    }

    /**
     * Get stores with filters.
     */
    public function getWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (isset($filters['business_type'])) {
            $query->where('business_type', $filters['business_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['verified'])) {
            $query->where('verified_business', $filters['verified']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%")
                      ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['owner_id'])) {
            $query->where('owner_id', $filters['owner_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create store directly without model events.
     */
    public function createDirect(array $data): int
    {
        return $this->model->insertGetId($data);
    }

    /**
     * Get available owner ID for store assignment.
     */
    public function getAvailableOwnerId(): ?int
    {
        // Get first user that can be an owner
        $user = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['business_owner', 'admin', 'manager']);
            })->orWhereDoesntHave('roles');
        })
        ->where('is_active', true)
        ->first();

        return $user?->id;
    }
}
