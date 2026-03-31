<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'user_id',
        'session_id',
        'email',
        'items',
        'total',
        'currency',
        'recovery_email_sent',
        'recovery_email_sent_at',
        'recovery_token',
        'recovered_at',
        'converted_to_order_id',
        'ip_address',
        'user_agent',
        'abandoned_at',
    ];

    protected $casts = [
        'items' => 'array',
        'total' => 'decimal:2',
        'recovery_email_sent' => 'boolean',
        'recovery_email_sent_at' => 'datetime',
        'recovered_at' => 'datetime',
        'abandoned_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
