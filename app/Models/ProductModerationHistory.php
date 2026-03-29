<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModerationHistory extends Model
{
    use HasFactory;

    protected $table = 'product_moderation_history';

    protected $fillable = [
        'product_id',
        'from_status',
        'to_status',
        'moderated_by',
        'reason',
        'notes',
        'changes_requested',
    ];

    protected $casts = [
        'changes_requested' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
