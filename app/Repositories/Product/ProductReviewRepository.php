<?php

namespace App\Repositories\Product;

use App\Models\ProductReview;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductReviewRepository extends BaseRepository
{
    public function __construct(ProductReview $model)
    {
        parent::__construct($model);
    }

    public function paginateByProduct(string $productId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->where('product_id', $productId)
            ->with(['user', 'moderator', 'response']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        if (isset($filters['verified_purchase'])) {
            $query->where('verified_purchase', $filters['verified_purchase']);
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
            ->with(['user'])
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getRatingDistribution(string $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get()
            ->keyBy('rating');
    }

    public function getAverageRating(string $productId): float
    {
        return (float) $this->model
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->avg('rating') ?? 0;
    }

    public function getTotalReviews(string $productId, string $status = 'approved'): int
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('status', $status)
            ->count();
    }

    public function getUserReviewForProduct(string $userId, string $productId): ?ProductReview
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
    }

    public function incrementHelpfulCount(string $reviewId): bool
    {
        $review = $this->getById($reviewId);
        return $review ? $review->increment('helpful_count') > 0 : false;
    }

    public function decrementHelpfulCount(string $reviewId): bool
    {
        $review = $this->getById($reviewId);
        return $review ? $review->decrement('helpful_count') > 0 : false;
    }

    public function incrementNotHelpfulCount(string $reviewId): bool
    {
        $review = $this->getById($reviewId);
        return $review ? $review->increment('not_helpful_count') > 0 : false;
    }

    public function decrementNotHelpfulCount(string $reviewId): bool
    {
        $review = $this->getById($reviewId);
        return $review ? $review->decrement('not_helpful_count') > 0 : false;
    }

    public function getPendingReviews(int $limit = null): Collection
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

    public function getReviewsByStore(string $storeId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->where('store_id', $storeId)
            ->with(['user', 'product']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['rating'])) {
            $query->where('rating', $filters['rating']);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
    }
}
