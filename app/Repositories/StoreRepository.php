<?php

namespace App\Repositories;

use App\Models\Store;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * Find store by UUID.
     */
    public function findByUuid(string $uuid): ?Store
    {
        return $this->getFirstBy(['uuid' => $uuid]);
    }

    /**
     * Find store by slug.
     */
    public function findBySlug(string $slug): ?Store
    {
        return $this->getFirstBy(['slug' => $slug]);
    }

    /**
     * Get stores by owner.
     */
    public function getByOwner(int $ownerId): Collection
    {
        return $this->getBy(['owner_id' => $ownerId]);
    }

    /**
     * Get active stores.
     */
    public function getActive(): Collection
    {
        return $this->getBy(['status' => 'active']);
    }

    /**
     * Get verified stores.
     */
    public function getVerified(): Collection
    {
        return $this->getBy(['verified_business' => true]);
    }

    /**
     * Get stores by business type.
     */
    public function getByBusinessType(string $businessType): Collection
    {
        return $this->getBy(['business_type' => $businessType]);
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
     * Get stores with relations.
     */
    public function getWithRelations(array $relations = []): Collection
    {
        return $this->model->with($relations)->get();
    }

    /**
     * Create new store.
     */
    public function create(array $data): Store
    {
        return $this->model->create($data);
    }

    /**
     * Update store.
     */
    public function update(Store $store, array $data): Store
    {
        $store->update($data);
        return $store;
    }

    /**
     * Delete store.
     */
    public function delete(Store $store): bool
    {
        return $store->delete();
    }

    /**
     * Get paginated stores.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($perPage);
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
     * Get store by ID.
     */
    public function findById(int $id): ?Store
    {
        return $this->getById($id);
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
        $user = \App\Models\User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['business_owner', 'admin', 'manager']);
            })->orWhereDoesntHave('roles');
        })
        ->where('is_active', true)
        ->first();

        return $user?->id;
    }

    /**
     * Get store statistics.
     */
    public function getStatistics(Store $store): array
    {
        return [
            'total_products' => $store->products()->count(),
            'active_products' => $store->products()->where('status', 'active')->count(),
            'total_contacts' => $store->contacts()->count(),
            'primary_contacts' => $store->contacts()->where('is_primary', true)->count(),
            'public_contacts' => $store->contacts()->where('is_public', true)->count(),
            'total_payment_methods' => $store->paymentMethods()->count(),
            'active_payment_methods' => $store->paymentMethods()->where('is_enabled', true)->count(),
            'total_users' => $store->users()->count(),
            'active_users' => $store->users()->where('is_active', true)->count(),
            'created_at' => $store->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $store->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
