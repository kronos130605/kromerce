<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationQueue extends Model
{
    use HasFactory;

    protected $table = 'moderation_queue';

    protected $fillable = [
        'item_type',
        'item_id',
        'store_id',
        'priority',
        'reason',
        'metadata',
        'assigned_to',
        'assigned_at',
        'status',
    ];

    protected $casts = [
        'metadata' => 'array',
        'assigned_at' => 'datetime',
    ];

    public function item()
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
