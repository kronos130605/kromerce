<?php

namespace App\Services;

use App\Repositories\Product\ProductReviewRepository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductReviewService
{
    public function __construct(
        private ProductReviewRepository $reviewRepository,
        private ProductRepository $productRepository
    ) {}

    public function getReviewsForProduct(string $productId, array $filters = []): LengthAwarePaginator
    {
        return $this->reviewRepository->paginateByProduct($productId, $filters);
    }

    public function createReview(array $data): mixed
    {
        return DB::transaction(function () use ($data) {
            $review = $this->reviewRepository->create($data);
            
            $this->updateProductRating($review->product_id);
            
            return $this->reviewRepository->getFirstBy(['id' => $review->id]);
        });
    }

    public function updateReview(string $reviewId, array $data): mixed
    {
        return DB::transaction(function () use ($reviewId, $data) {
            $review = $this->reviewRepository->getById($reviewId);
            
            $this->reviewRepository->updateBy(['id' => $reviewId], $data);
            
            $this->updateProductRating($review->product_id);
            
            return $this->reviewRepository->getFirstBy(['id' => $reviewId]);
        });
    }

    public function moderateReview(string $reviewId, string $status, ?string $moderatorId, ?string $notes = null): mixed
    {
        $this->reviewRepository->updateBy(['id' => $reviewId], [
            'status' => $status,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'moderation_notes' => $notes,
        ]);
        
        return $this->reviewRepository->getFirstBy(['id' => $reviewId]);
    }

    public function voteReview(string $reviewId, string $userId, bool $isHelpful): void
    {
        DB::transaction(function () use ($reviewId, $userId, $isHelpful) {
            $review = $this->reviewRepository->getById($reviewId);
            
            $existingVote = $review->votes()
                ->where('user_id', $userId)
                ->first();

            if ($existingVote) {
                if ($existingVote->is_helpful !== $isHelpful) {
                    $existingVote->update(['is_helpful' => $isHelpful]);
                    
                    if ($isHelpful) {
                        $this->reviewRepository->incrementHelpfulCount($reviewId);
                        $this->reviewRepository->decrementNotHelpfulCount($reviewId);
                    } else {
                        $this->reviewRepository->decrementHelpfulCount($reviewId);
                        $this->reviewRepository->incrementNotHelpfulCount($reviewId);
                    }
                }
            } else {
                $review->votes()->create([
                    'user_id' => $userId,
                    'is_helpful' => $isHelpful,
                ]);
                
                if ($isHelpful) {
                    $this->reviewRepository->incrementHelpfulCount($reviewId);
                } else {
                    $this->reviewRepository->incrementNotHelpfulCount($reviewId);
                }
            }
        });
    }

    public function deleteReview(string $reviewId): bool
    {
        return DB::transaction(function () use ($reviewId) {
            $review = $this->reviewRepository->getById($reviewId);
            $productId = $review->product_id;
            
            $deleted = $this->reviewRepository->deleteBy(['id' => $reviewId]) > 0;
            
            if ($deleted) {
                $this->updateProductRating($productId);
            }
            
            return $deleted;
        });
    }

    public function getProductRatingStats(string $productId): array
    {
        $distribution = $this->reviewRepository->getRatingDistribution($productId);
        $total = $distribution->sum('count');
        $average = $total > 0 
            ? $distribution->sum(fn($r) => $r->rating * $r->count) / $total 
            : 0;

        return [
            'average_rating' => round($average, 2),
            'total_reviews' => $total,
            'rating_distribution' => [
                5 => $distribution->get(5)?->count ?? 0,
                4 => $distribution->get(4)?->count ?? 0,
                3 => $distribution->get(3)?->count ?? 0,
                2 => $distribution->get(2)?->count ?? 0,
                1 => $distribution->get(1)?->count ?? 0,
            ],
        ];
    }

    private function updateProductRating(string $productId): void
    {
        $stats = $this->getProductRatingStats($productId);
        
        $this->productRepository->updateBy(['id' => $productId], [
            'average_rating' => $stats['average_rating'],
            'total_reviews' => $stats['total_reviews'],
        ]);
    }
}
