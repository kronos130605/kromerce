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
     * Get all categories.
     */
    public function getAll(array $columns = ['*']): Collection
    {
        return $this->model
            ->orderBy('level')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get root categories.
     */
    public function getRoot(): Collection
    {
        return $this->model
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
     * Get category tree.
     */
    public function getTree(): Collection
    {
        return $this->model
            ->with(['children' => function ($query) {
                $query->orderBy('name');
            }])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active categories.
     */
    public function getActive(): Collection
    {
        return $this->model
            ->where('status', 'active')
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
    public function getStatistics(): array
    {
        return [
            'total_categories' => $this->model->count(),
            'active_categories' => $this->model->where('status', 'active')->count(),
            'root_categories' => $this->model->whereNull('parent_id')->count(),
            'max_level' => $this->model->max('level'),
        ];
    }

    /**
     * Count all categories.
     */
    public function countAll(): int
    {
        return $this->model->count();
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
    public function getWithProductCount(): Collection
    {
        return $this->model
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find category by slug.
     */
    public function findBySlug(string $slug): ?ProductCategory
    {
        return $this->model
            ->where('slug', $slug)
            ->first();
    }
}
