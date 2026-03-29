<?php

namespace App\Repositories\Product;

use App\Models\ProductAnswer;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class ProductAnswerRepository extends BaseRepository
{
    public function __construct(ProductAnswer $model)
    {
        parent::__construct($model);
    }

    public function getByQuestion(string $questionId, array $with = []): Collection
    {
        $query = $this->model->where('question_id', $questionId);
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getApprovedByQuestion(string $questionId): Collection
    {
        return $this->model
            ->where('question_id', $questionId)
            ->where('status', 'approved')
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPendingAnswers(int $limit = null): Collection
    {
        $query = $this->model
            ->where('status', 'pending')
            ->with(['user', 'question.product'])
            ->orderBy('created_at', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function incrementHelpfulCount(string $answerId): bool
    {
        $answer = $this->getById($answerId);
        return $answer ? $answer->increment('helpful_count') > 0 : false;
    }

    public function decrementHelpfulCount(string $answerId): bool
    {
        $answer = $this->getById($answerId);
        return $answer ? $answer->decrement('helpful_count') > 0 : false;
    }

    public function incrementNotHelpfulCount(string $answerId): bool
    {
        $answer = $this->getById($answerId);
        return $answer ? $answer->increment('not_helpful_count') > 0 : false;
    }

    public function decrementNotHelpfulCount(string $answerId): bool
    {
        $answer = $this->getById($answerId);
        return $answer ? $answer->decrement('not_helpful_count') > 0 : false;
    }
}
