<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    protected $fillable = [
        'domain',
        'store_id',
    ];

    /**
     * Get the store that owns the domain.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the store that owns the domain (legacy alias - deprecated, use store()).
     * @deprecated Use store() instead
     */
    public function tenant(): BelongsTo
    {
        return $this->store();
    }
}
