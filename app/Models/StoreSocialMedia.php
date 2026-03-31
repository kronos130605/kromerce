<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSocialMedia extends Model
{
    use HasFactory;

    protected $table = 'store_social_media';

    protected $fillable = [
        'store_id',
        'platform',
        'url',
        'handle',
        'followers',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'followers' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
