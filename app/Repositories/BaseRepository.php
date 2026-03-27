<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseRepository
{
    protected Model $model;
    protected array $allowedFields = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records.
     */
    public function getAll(array $columns = ['*']): Collection
    {
        return $this->model->all($columns);
    }

    /**
     * Get record by ID.
     */
    public function getById(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Get records by criteria.
     */
    public function getBy(array $criteria, array $columns = ['*']): Collection
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $key => $value) {
            if (!in_array($key, $this->allowedFields)) {
                continue; // Skip invalid fields
            }

            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->get($columns);
    }

    /**
     * Get first record by criteria.
     */
    public function getFirstBy(array $criteria, array $columns = ['*']): ?Model
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $key => $value) {
            if (!in_array($key, $this->allowedFields)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first($columns);
    }

    /**
     * Create new record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Create multiple records.
     */
    public function createMany(array $data): Collection
    {
        $created = new Collection();

        foreach ($data as $row) {
            $created->push($this->create($row));
        }

        return $created;
    }

    /**
     * Update record by ID.
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->getById($id);

        if (!$model) {
            return false;
        }

        return $model->update($data);
    }

    /**
     * Update records by criteria.
     */
    public function updateBy(array $criteria, array $data): int
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $key => $value) {
            if (!in_array($key, $this->allowedFields)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->update($data);
    }

    /**
     * Delete record by ID.
     */
    public function delete(int $id): bool
    {
        $model = $this->getById($id);

        if (!$model) {
            return false;
        }

        return $model->delete();
    }

    /**
     * Delete records by criteria.
     */
    public function deleteBy(array $criteria): int
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $key => $value) {
            if (!in_array($key, $this->allowedFields)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->delete();
    }

    /**
     * Get paginated records.
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->newQuery()->paginate($perPage, $columns);
    }

    /**
     * Get paginated records with filters.
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage, $columns);
    }

    /**
     * Apply filters to query.
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if (is_null($value)) {
                continue;
            }

            // Validate field name
            $field = $this->extractFieldName($key);
            if (!in_array($field, $this->allowedFields)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (str_contains($key, '_like')) {
                $query->where($field, 'like', "%{$value}%");
            } elseif (str_contains($key, '_min')) {
                $query->where($field, '>=', $value);
            } elseif (str_contains($key, '_max')) {
                $query->where($field, '<=', $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Count records.
     */
    public function count(array $criteria = []): int
    {
        $query = $this->model->newQuery();

        if (!empty($criteria)) {
            $this->applyFilters($query, $criteria);
        }

        return $query->count();
    }

    /**
     * Check if record exists.
     */
    public function exists(int $id): bool
    {
        return $this->model->newQuery()->where('id', $id)->exists();
    }

    /**
     * Check if record exists by criteria.
     */
    public function existsBy(array $criteria): bool
    {
        $query = $this->model->newQuery();

        foreach ($criteria as $key => $value) {
            if (!in_array($key, $this->allowedFields)) {
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->exists();
    }

    /**
     * Get query builder instance.
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Get model instance.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Find or create record.
     */
    public function firstOrCreate(array $criteria, array $data = []): Model
    {
        return $this->model->firstOrCreate($criteria, $data);
    }

    /**
     * Update or create record.
     */
    public function updateOrCreate(array $criteria, array $data): Model
    {
        return $this->model->updateOrCreate($criteria, $data);
    }

    /**
     * Get all results.
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->model->newQuery()->get($columns);
    }

    /**
     * Get first result.
     */
    public function first(array $columns = ['*']): ?Model
    {
        return $this->model->newQuery()->first($columns);
    }

    
    /**
     * Extract field name from filter key.
     */
    protected function extractFieldName(string $key): string
    {
        return str_replace(['_like', '_min', '_max'], '', $key);
    }

    /**
     * Get allowed fields for filtering.
     */
    protected function getAllowedFields(): array
    {
        return $this->allowedFields;
    }

    /**
     * Set allowed fields for filtering.
     */
    protected function setAllowedFields(array $fields): void
    {
        $this->allowedFields = $fields;
    }
}
