<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisputeMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'user_id',
        'sender_type',
        'message',
        'attachments',
        'is_internal',
        'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function dispute()
    {
        return $this->belongsTo(OrderDispute::class, 'dispute_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
