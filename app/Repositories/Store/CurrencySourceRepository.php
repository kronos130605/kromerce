<?php

namespace App\Repositories\Store;

use App\Models\CurrencySource;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class CurrencySourceRepository extends BaseRepository
{
    protected array $allowedFields = [
        'name', 'code', 'type', 'provider_class', 'is_active',
        'is_global_default', 'base_url', 'config', 'auth_type',
        'default_credentials', 'last_tested_at', 'last_test_success',
        'last_test_message', 'success_count', 'failure_count'
    ];

    public function __construct(CurrencySource $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active sources.
     */
    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    /**
     * Get global default source.
     */
    public function getGlobalDefault(): ?CurrencySource
    {
        return $this->model->globalDefault()->first();
    }

    /**
     * Find by code.
     */
    public function findByCode(string $code): ?CurrencySource
    {
        return $this->getFirstBy(['code' => $code, 'is_active' => true]);
    }

    /**
     * Get sources by type.
     */
    public function getByType(string $type): Collection
    {
        return $this->getBy(['type' => $type, 'is_active' => true]);
    }

    /**
     * Set global default (unset others first).
     */
    public function setGlobalDefault(string $sourceId): bool
    {
        // Unset current default
        $this->model->where('is_global_default', true)->update(['is_global_default' => false]);
        
        // Set new default
        return $this->updateBy(['id' => $sourceId], ['is_global_default' => true]);
    }

    /**
     * Get sources with statistics.
     */
    public function getWithStatistics(): Collection
    {
        return $this->model
            ->selectRaw('*, (success_count / NULLIF(success_count + failure_count, 0) * 100) as reliability_score')
            ->orderBy('is_global_default', 'desc')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Test connection and update status.
     */
    public function testAndUpdateStatus(string $sourceId, bool $success, ?string $message = null): bool
    {
        $source = $this->find($sourceId);
        
        if (!$source) {
            return false;
        }

        if ($success) {
            $source->recordSuccess();
        } else {
            $source->recordFailure($message ?? 'Connection test failed');
        }

        return true;
    }
}
