<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreBadge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'type',
        'requirements',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'requirements' => 'array',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(StoreBadgeAssignment::class, 'badge_id');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_badge_assignments', 'badge_id', 'store_id')
            ->withPivot(['earned_at', 'expires_at', 'is_active', 'notes'])
            ->withTimestamps();
    }
}
