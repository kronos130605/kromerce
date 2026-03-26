<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_primary' => 'nullable|in:1,0,true,false,on,off,yes,no',
            'order' => 'nullable|integer|min:0',
            'alt' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'An image file is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a jpeg, png, jpg, gif, or webp.',
            'image.max' => 'The image must not exceed 5MB.',
            'is_primary.in' => 'The primary flag must be true or false.',
            'order.integer' => 'The order must be a number.',
            'alt.max' => 'The alt text must not exceed 255 characters.',
            'title.max' => 'The title must not exceed 255 characters.',
        ];
    }

    /**
     * Parse is_primary from various string values to boolean
     */
    public function booleanIsPrimary(): bool
    {
        return in_array($this->input('is_primary'), ['1', 'true', 'on', 'yes'], true);
    }

    /**
     * Get order as integer
     */
    public function integerOrder(int $default): int
    {
        return (int) $this->input('order', $default);
    }
}
