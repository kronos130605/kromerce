<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value',
        'label',
        'color',
        'image',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the attribute that owns the value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    /**
     * Scope to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get display value with formatting.
     */
    public function getDisplayValue(): string
    {
        if ($this->attribute->isVisual() && $this->color) {
            return $this->label;
        }

        return $this->label ?? $this->value;
    }

    /**
     * Get visual representation (color or image).
     */
    public function getVisualRepresentation(): array
    {
        if ($this->attribute->isVisual()) {
            return [
                'type' => $this->color ? 'color' : 'image',
                'value' => $this->color ?? $this->image,
                'label' => $this->label,
            ];
        }

        return [
            'type' => 'text',
            'value' => $this->value,
            'label' => $this->label,
        ];
    }
}
