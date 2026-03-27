<?php

namespace App\Repositories\Product;

use App\Models\ProductCategory;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductCategoryRepository extends BaseRepository
{
    public function __construct(ProductCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get categories for store.
     */
    public function getForStore(string $storeId): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('level')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get root categories for store.
     */
    public function getRootForStore(string $storeId): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get child categories.
     */
    public function getChildren(int $parentId): Collection
    {
        return $this->model
            ->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get category with descendants.
     */
    public function getWithDescendants(int $categoryId): ?ProductCategory
    {
        return $this->model
            ->with('descendants')
            ->find($categoryId);
    }

    /**
     * Get category tree for store.
     */
    public function getTreeForStore(string $storeId): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->with(['children' => function ($query) {
                $query->orderBy('name');
            }])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active categories for store.
     */
    public function getActiveForStore(string $storeId): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create category with level calculation.
     */
    public function createCategory(array $data): ProductCategory
    {
        if (isset($data['parent_id']) && $data['parent_id']) {
            $parent = $this->getById($data['parent_id']);
            $data['level'] = $parent ? $parent->level + 1 : 1;
        } else {
            $data['level'] = 1;
        }

        return $this->create($data);
    }

    /**
     * Update category with level recalculation.
     */
    public function updateCategory(int $id, array $data): bool
    {
        $category = $this->getById($id);

        if (!$category) {
            return false;
        }

        // Recalculate level if parent changed
        if (isset($data['parent_id']) && $data['parent_id'] !== $category->parent_id) {
            if ($data['parent_id']) {
                $parent = $this->getById($data['parent_id']);
                $data['level'] = $parent ? $parent->level + 1 : 1;
            } else {
                $data['level'] = 1;
            }
        }

        return $this->update($id, $data);
    }

    /**
     * Get category statistics.
     */
    public function getStatistics(string $storeId): array
    {
        $categories = $this->model->where('store_id', $storeId);

        return [
            'total_categories' => $categories->count(),
            'active_categories' => $categories->where('is_active', true)->count(),
            'root_categories' => $categories->whereNull('parent_id')->count(),
            'max_level' => $categories->max('level'),
        ];
    }

    /**
     * Count categories for store.
     */
    public function countForStore(string $storeId): int
    {
        return $this->model->where('store_id', $storeId)->count();
    }

    /**
     * Check if category has products.
     */
    public function hasProducts(int $categoryId): bool
    {
        return $this->model
            ->find($categoryId)
            ->products()
            ->exists();
    }

    /**
     * Get categories with product count.
     */
    public function getWithProductCount(string $storeId): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }
}
