<?php

namespace App\Repositories\Product;

use App\Models\ProductQuestion;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductQuestionRepository extends BaseRepository
{
    public function __construct(ProductQuestion $model)
    {
        parent::__construct($model);
    }

    public function paginateByProduct(string $productId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->where('product_id', $productId)
            ->with(['user', 'answers.user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['has_answer'])) {
            if ($filters['has_answer']) {
                $query->has('answers');
            } else {
                $query->doesntHave('answers');
            }
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getApprovedByProduct(string $productId, int $limit = null): Collection
    {
        $query = $this->model
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->with(['user', 'answers' => function ($q) {
                $q->where('status', 'approved')->with('user');
            }])
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getUnansweredByProduct(string $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->doesntHave('answers')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingQuestions(int $limit = null): Collection
    {
        $query = $this->model
            ->where('status', 'pending')
            ->with(['user', 'product'])
            ->orderBy('created_at', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getQuestionsByStore(string $storeId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->where('store_id', $storeId)
            ->with(['user', 'product', 'answers']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['has_answer'])) {
            if ($filters['has_answer']) {
                $query->has('answers');
            } else {
                $query->doesntHave('answers');
            }
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function getUserQuestionForProduct(string $userId, string $productId): ?ProductQuestion
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }
}
