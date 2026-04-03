<?php

namespace App\Repositories\Currency;

use App\Models\CurrencyRateUpdate;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class CurrencyRateUpdateRepository extends BaseRepository
{
    public function __construct(CurrencyRateUpdate $model)
    {
        parent::__construct($model);
    }

    /**
     * Get recent updates summary.
     */
    public function getRecentSummary(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $updates = $this->model
            ->where('updated_at', '>=', $startDate)
            ->get();

        $successful = $updates->where('success', true);
        $failed = $updates->where('success', false);

        return [
            'total_updates' => $updates->count(),
            'successful_updates' => $successful->count(),
            'failed_updates' => $failed->count(),
            'success_rate' => $updates->count() > 0 ? round(($successful->count() / $updates->count()) * 100, 2) : 0,
            'last_successful_update' => $successful->max('updated_at'),
            'last_failed_update' => $failed->max('updated_at'),
            'currencies_updated' => $this->getCurrenciesUpdated($successful),
            'common_errors' => $this->getCommonErrors($failed),
        ];
    }

    /**
     * Create or update success record.
     */
    public function createSuccess(array $currencies, string $source, int $totalUpdated): CurrencyRateUpdate
    {
        $today = now()->format('Y-m-d');

        return $this->model->updateOrCreate(
            ['update_date' => $today],
            [
                'success' => true,
                'currencies_updated' => json_encode($currencies),
                'source' => $source,
                'total_updated' => $totalUpdated,
                'error_message' => null,
            ]
        );
    }

    /**
     * Create or update failure record.
     */
    public function createFailure(array $currencies, string $source, string $errorMessage): CurrencyRateUpdate
    {
        $today = now()->format('Y-m-d');

        return $this->model->updateOrCreate(
            ['update_date' => $today],
            [
                'success' => false,
                'currencies_updated' => json_encode($currencies),
                'source' => $source,
                'total_updated' => 0,
                'error_message' => $errorMessage,
            ]
        );
    }

    /**
     * Get updates by source.
     */
    public function getBySource(string $source, int $limit = 50): Collection
    {
        return $this->model
            ->where('source', $source)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get successful updates.
     */
    public function getSuccessful(int $limit = 50): Collection
    {
        return $this->model
            ->where('success', true)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get failed updates.
     */
    public function getFailed(int $limit = 50): Collection
    {
        return $this->model
            ->where('success', false)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get updates for date range.
     */
    public function getForDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Get update statistics.
     */
    public function getStatistics(): array
    {
        $total = $this->model->count();
        $successful = $this->model->where('success', true)->count();
        $failed = $this->model->where('success', false)->count();

        return [
            'total_updates' => $total,
            'successful_updates' => $successful,
            'failed_updates' => $failed,
            'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
            'last_update' => $this->model->max('updated_at'),
            'last_successful' => $this->model->where('success', true)->max('updated_at'),
        ];
    }

    /**
     * Clean up old update records.
     */
    public function cleanupOldRecords(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return $this->model
            ->where('updated_at', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Get currencies updated from successful updates.
     */
    private function getCurrenciesUpdated(Collection $successfulUpdates): array
    {
        $currencies = [];

        foreach ($successfulUpdates as $update) {
            $updateCurrencies = json_decode($update->currencies_updated, true) ?? [];
            $currencies = array_merge($currencies, $updateCurrencies);
        }

        return array_unique($currencies);
    }

    /**
     * Get common errors from failed updates.
     */
    private function getCommonErrors(Collection $failedUpdates): array
    {
        $errors = [];

        foreach ($failedUpdates as $update) {
            if ($update->error_message) {
                $errorKey = substr($update->error_message, 0, 100); // First 100 chars
                $errors[$errorKey] = ($errors[$errorKey] ?? 0) + 1;
            }
        }

        arsort($errors);

        return array_slice($errors, 0, 5, true); // Top 5 errors
    }
}
