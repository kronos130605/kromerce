<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreBadgeAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'badge_id',
        'earned_at',
        'expires_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function badge()
    {
        return $this->belongsTo(StoreBadge::class, 'badge_id');
    }
}
