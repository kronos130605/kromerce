<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductQuestionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductQuestionController extends Controller
{
    public function __construct(
        private ProductQuestionService $questionService
    ) {}

    public function index(Request $request, Product $product): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'has_answer', 'sort_by', 'sort_order', 'per_page']);
            $questions = $this->questionService->getQuestionsForProduct($product->id, $filters);
            
            return $this->success($questions);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve questions', 500);
        }
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        try {
            $validated = $request->validate([
                'question' => 'required|string|max:500',
            ]);

            $validated['product_id'] = $product->id;
            $validated['user_id'] = auth()->id();
            $validated['store_id'] = $product->store_id;
            $validated['status'] = 'pending';

            $question = $this->questionService->createQuestion($validated);
            
            return $this->success($question, 'Question submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create question: ' . $e->getMessage(), 500);
        }
    }

    public function storeAnswer(Request $request, Product $product, string $question): JsonResponse
    {
        try {
            $validated = $request->validate([
                'answer' => 'required|string|max:1000',
            ]);

            $validated['user_id'] = auth()->id();
            $validated['status'] = 'pending';

            $answer = $this->questionService->createAnswer($question, $validated);
            
            return $this->success($answer, 'Answer submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create answer: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Product $product, string $question): JsonResponse
    {
        try {
            $validated = $request->validate([
                'question' => 'sometimes|string|max:500',
            ]);

            $updated = $this->questionService->updateQuestion($question, $validated);
            
            return $this->success(null, 'Question updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update question: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Product $product, string $question): JsonResponse
    {
        try {
            $this->questionService->deleteQuestion($question);
            
            return $this->success(null, 'Question deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete question: ' . $e->getMessage(), 500);
        }
    }

    public function voteAnswer(Request $request, Product $product, string $question, string $answer): JsonResponse
    {
        try {
            $validated = $request->validate([
                'is_helpful' => 'required|boolean',
            ]);

            $this->questionService->voteAnswer(
                $answer,
                auth()->id(),
                $validated['is_helpful']
            );
            
            return $this->success(null, 'Vote recorded successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to record vote: ' . $e->getMessage(), 500);
        }
    }
}
