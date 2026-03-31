<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'store_id',
        'order_id',
        'title',
        'content',
        'rating',
        'quality_rating',
        'value_rating',
        'service_rating',
        'verified_purchase',
        'is_anonymous',
        'status',
        'moderated_by',
        'moderated_at',
        'moderation_notes',
        'helpful_count',
        'not_helpful_count',
        'images',
        'videos',
        'ip_address',
    ];

    protected $casts = [
        'rating' => 'integer',
        'quality_rating' => 'integer',
        'value_rating' => 'integer',
        'service_rating' => 'integer',
        'verified_purchase' => 'boolean',
        'is_anonymous' => 'boolean',
        'moderated_at' => 'datetime',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'images' => 'array',
        'videos' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class, 'review_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ReviewResponse::class, 'review_id');
    }
}
