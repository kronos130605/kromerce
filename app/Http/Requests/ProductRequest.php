<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Repositories\Currency\CurrencyRateGlobalRepository;
use App\Repositories\Currency\CurrencyRateBusinessRepository;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    private CurrencyRateGlobalRepository $currencyRateGlobalRepo;
    private CurrencyRateBusinessRepository $currencyRateBusinessRepo;

    public function __construct(
        CurrencyRateGlobalRepository $currencyRateGlobalRepo,
        CurrencyRateBusinessRepository $currencyRateBusinessRepo
    ) {
        $this->currencyRateGlobalRepo = $currencyRateGlobalRepo;
        $this->currencyRateBusinessRepo = $currencyRateBusinessRepo;
    }

    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $product = $this->route('product');

        if ($product instanceof Product) {
            return match($this->method()) {
                'DELETE' => $user->can('delete', $product),
                'PUT', 'PATCH' => $user->can('update', $product),
                default => $user->can('view', $product),
            };
        }

        return match($this->method()) {
            'POST' => $user->can('create', Product::class),
            default => $user->can('viewAny', Product::class),
        };
    }
    
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // Basic product info
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            
            // Pricing
            'base_price' => 'required|numeric|min:0|max:999999.99',
            'base_sale_price' => 'nullable|numeric|min:0|max:999999.99|lt:base_price',
            'cost_price' => 'nullable|numeric|min:0|max:999999.99',
            'base_currency' => [
                'required',
                'string',
                'size:3',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $code = strtoupper((string) $value);
                    $storeId = $this->route('store')?->id ?? $this->input('store_id');

                    $existsInGlobal = $this->currencyRateGlobalRepo->currencyExists($code);
                    
                    $existsInBusiness = $storeId 
                        ? $this->currencyRateBusinessRepo->currencyExists($storeId, $code)
                        : false;

                    if (!$existsInGlobal && !$existsInBusiness) {
                        $fail('Selected currency is not supported');
                    }
                },
            ],
            
            // Stock management
            'manage_stock' => 'sometimes|boolean',
            'stock_quantity' => 'required_if:manage_stock,true|integer|min:0',
            'low_stock_threshold' => 'required_if:manage_stock,true|integer|min:1',
            
            // Status and visibility
            'status' => 'required|in:active,inactive,draft',
            'featured' => 'sometimes|boolean',
            'is_on_sale' => 'sometimes|boolean',
            
            // Organization
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:product_categories,id',
            
            // Filters for listing
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
            'search' => 'sometimes|string|max:255',
            'in_stock' => 'sometimes|boolean',
        ];

        // For updates, make sku and slug unique except for current product
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $productId = $this->route('product');
            $rules['sku'] = 'nullable|string|max:100|unique:products,sku,' . $productId;
            $rules['slug'] = 'nullable|string|max:255|unique:products,slug,' . $productId;
        }

        return $rules;
    }
    
    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            // Basic info
            'name.required' => 'Product name is required',
            'name.max' => 'Product name must not exceed 255 characters',
            'description.max' => 'Description must not exceed 2000 characters',
            'sku.unique' => 'SKU must be unique',
            'slug.unique' => 'Slug must be unique',
            
            // Pricing
            'base_price.required' => 'Base price is required',
            'base_price.numeric' => 'Base price must be a valid number',
            'base_sale_price.lt' => 'Sale price must be less than base price',
            'base_currency.required' => 'Currency is required',
            'base_currency.exists' => 'Selected currency is not supported',
            
            // Stock
            'stock_quantity.required_if' => 'Stock quantity is required when stock management is enabled',
            'stock_quantity.min' => 'Stock quantity cannot be negative',
            'low_stock_threshold.required_if' => 'Low stock threshold is required when stock management is enabled',
            'low_stock_threshold.min' => 'Low stock threshold must be at least 1',
            
            // Status
            'status.required' => 'Product status is required',
            'status.in' => 'Invalid status selected',
            
            // Organization
            'category_ids.*.exists' => 'One or more selected categories are invalid',
        ];
    }
}
