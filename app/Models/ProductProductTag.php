<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductProductTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'tag_id',
    ];

    /**
     * Get the product that owns the pivot.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the tag that owns the pivot.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(ProductTag::class);
    }
}
