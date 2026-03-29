<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'conditions',
        'action',
        'severity',
        'is_active',
        'trigger_count',
    ];

    protected $casts = [
        'conditions' => 'array',
        'severity' => 'integer',
        'is_active' => 'boolean',
        'trigger_count' => 'integer',
    ];
}
