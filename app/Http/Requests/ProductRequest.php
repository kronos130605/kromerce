<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User can manage products if they have tenant access
    }
    
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'category_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'sometimes|boolean',
            'is_on_sale' => 'sometimes|boolean',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
            'search' => 'sometimes|string|max:255',
        ];
    }
    
    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'name.max' => 'Product name must not exceed 255 characters',
            'price.required' => 'Product price is required',
            'price.numeric' => 'Price must be a valid number',
            'category_id.exists' => 'Selected category does not exist',
        ];
    }
}
