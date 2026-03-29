<?php

namespace App\Services;

use App\Repositories\Product\ProductQuestionRepository;
use App\Repositories\Product\ProductAnswerRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductQuestionService
{
    public function __construct(
        private ProductQuestionRepository $questionRepository,
        private ProductAnswerRepository $answerRepository
    ) {}

    public function getQuestionsForProduct(string $productId, array $filters = []): LengthAwarePaginator
    {
        return $this->questionRepository->paginateByProduct($productId, $filters);
    }

    public function createQuestion(array $data): mixed
    {
        return $this->questionRepository->create($data);
    }

    public function updateQuestion(string $questionId, array $data): bool
    {
        return $this->questionRepository->updateBy(['id' => $questionId], $data) > 0;
    }

    public function deleteQuestion(string $questionId): bool
    {
        return $this->questionRepository->deleteBy(['id' => $questionId]) > 0;
    }

    public function moderateQuestion(string $questionId, string $status, ?string $moderatorId, ?string $notes = null): mixed
    {
        $this->questionRepository->updateBy(['id' => $questionId], [
            'status' => $status,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'moderation_notes' => $notes,
        ]);
        
        return $this->questionRepository->getFirstBy(['id' => $questionId]);
    }

    public function createAnswer(string $questionId, array $data): mixed
    {
        return DB::transaction(function () use ($questionId, $data) {
            $data['question_id'] = $questionId;
            
            $answer = $this->answerRepository->create($data);
            
            $this->questionRepository->updateBy(['id' => $questionId], [
                'answer_count' => DB::raw('answer_count + 1')
            ]);
            
            return $answer;
        });
    }

    public function updateAnswer(string $answerId, array $data): bool
    {
        return $this->answerRepository->updateBy(['id' => $answerId], $data) > 0;
    }

    public function deleteAnswer(string $answerId): bool
    {
        return DB::transaction(function () use ($answerId) {
            $answer = $this->answerRepository->getById($answerId);
            
            if (!$answer) {
                return false;
            }
            
            $deleted = $this->answerRepository->deleteBy(['id' => $answerId]) > 0;
            
            if ($deleted) {
                $this->questionRepository->updateBy(['id' => $answer->question_id], [
                    'answer_count' => DB::raw('answer_count - 1')
                ]);
            }
            
            return $deleted;
        });
    }

    public function moderateAnswer(string $answerId, string $status, ?string $moderatorId, ?string $notes = null): mixed
    {
        $this->answerRepository->updateBy(['id' => $answerId], [
            'status' => $status,
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'moderation_notes' => $notes,
        ]);
        
        return $this->answerRepository->getFirstBy(['id' => $answerId]);
    }

    public function voteAnswer(string $answerId, string $userId, bool $isHelpful): void
    {
        DB::transaction(function () use ($answerId, $userId, $isHelpful) {
            $answer = $this->answerRepository->getById($answerId);
            
            $existingVote = $answer->votes()
                ->where('user_id', $userId)
                ->first();

            if ($existingVote) {
                if ($existingVote->is_helpful !== $isHelpful) {
                    $existingVote->update(['is_helpful' => $isHelpful]);
                    
                    if ($isHelpful) {
                        $this->answerRepository->incrementHelpfulCount($answerId);
                        $this->answerRepository->decrementNotHelpfulCount($answerId);
                    } else {
                        $this->answerRepository->decrementHelpfulCount($answerId);
                        $this->answerRepository->incrementNotHelpfulCount($answerId);
                    }
                }
            } else {
                $answer->votes()->create([
                    'user_id' => $userId,
                    'is_helpful' => $isHelpful,
                ]);
                
                if ($isHelpful) {
                    $this->answerRepository->incrementHelpfulCount($answerId);
                } else {
                    $this->answerRepository->incrementNotHelpfulCount($answerId);
                }
            }
        });
    }

    public function getUnansweredQuestions(string $productId): Collection
    {
        return $this->questionRepository->getUnansweredByProduct($productId);
    }
}
