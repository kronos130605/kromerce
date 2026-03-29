<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'name',
        'slug',
        'description',
        'discount_percentage',
        'discount_type',
        'minimum_order_value',
        'is_active',
        'priority',
        'metadata',
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'minimum_order_value' => 'decimal:2',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'metadata' => 'array',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'customer_group_user')
            ->withPivot('assigned_by', 'assigned_at')
            ->withTimestamps();
    }
}
