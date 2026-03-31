<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnswerVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer_id',
        'user_id',
        'session_id',
        'is_helpful',
        'ip_address',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function answer(): BelongsTo
    {
        return $this->belongsTo(ProductAnswer::class, 'answer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
