<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function __construct(
        private ProductReviewService $reviewService
    ) {}

    public function index(Request $request, Product $product): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'rating', 'verified_purchase', 'sort_by', 'sort_order', 'per_page']);
            $reviews = $this->reviewService->getReviewsForProduct($product->id, $filters);

            return $this->success($reviews);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve reviews', 500);
        }
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'title' => 'required|string|max:200',
                'comment' => 'required|string|max:2000',
                'verified_purchase' => 'boolean',
            ]);

            $validated['product_id'] = $product->id;
            $validated['user_id'] = auth()->id();
            $validated['store_id'] = $product->store_id;
            $validated['status'] = 'pending';

            $review = $this->reviewService->createReview($validated);

            return $this->success($review, 'Review submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create review: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Product $product, string $review): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rating' => 'sometimes|integer|min:1|max:5',
                'title' => 'sometimes|string|max:200',
                'comment' => 'sometimes|string|max:2000',
            ]);

            $updated = $this->reviewService->updateReview($review, $validated);

            return $this->success($updated, 'Review updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update review: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Product $product, string $review): JsonResponse
    {
        try {
            $this->reviewService->deleteReview($review);

            return $this->success(null, 'Review deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete review: ' . $e->getMessage(), 500);
        }
    }

    public function moderate(Request $request, Product $product, string $review): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:approved,rejected,flagged',
                'moderation_notes' => 'nullable|string|max:500',
            ]);

            $moderated = $this->reviewService->moderateReview(
                $review,
                $validated['status'],
                auth()->id(),
                $validated['moderation_notes'] ?? null
            );

            return $this->success($moderated, 'Review moderated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to moderate review: ' . $e->getMessage(), 500);
        }
    }

    public function vote(Request $request, Product $product, string $review): JsonResponse
    {
        try {
            $validated = $request->validate([
                'is_helpful' => 'required|boolean',
            ]);

            $this->reviewService->voteReview(
                $review,
                auth()->id(),
                $validated['is_helpful']
            );

            return $this->success(null, 'Vote recorded successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to record vote: ' . $e->getMessage(), 500);
        }
    }

    public function stats(Product $product): JsonResponse
    {
        try {
            $stats = $this->reviewService->getProductRatingStats($product->id);

            return $this->success($stats);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve stats', 500);
        }
    }
}
