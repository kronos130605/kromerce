<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $store = $this->route('store');
        
        // Store owners can always manage their stores
        if ($store && $store->owner_id === auth()->id()) {
            return true;
        }

        // Admins can manage all stores
        if (auth()->user()->hasRole('admin')) {
            return true;
        }

        // Store managers can update but not delete
        if (in_array($this->method(), ['update', 'patch']) && auth()->user()->hasRole('manager')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_-]+$', Rule::unique('stores')->ignore($this->route('store'))],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'string', 'max:255'],
            'banner' => ['nullable', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:retail,wholesale,marketplace'],
            'status' => ['required', 'string', 'in:active,inactive,maintenance,suspended'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'website_url' => ['nullable', 'string', 'max:255', 'url'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'verified_business' => ['boolean'],
            
            // Currency configuration
            'default_currency' => ['required', 'string', 'size:3', 'in:USD,EUR,CUP'],
            'display_currencies' => ['required', 'array', 'min:1', 'max:5'],
            'display_currencies.*' => ['required', 'string', 'size:3', 'in:USD,EUR,CUP'],
        ];

        // For PATCH requests, make fields optional
        if ($this->isMethod('patch')) {
            foreach (['name', 'slug', 'business_type', 'status', 'default_currency'] as $field) {
                $rules[$field] = array_filter($rules[$field], function ($rule) {
                    return !in_array('required', $rule);
                });
            }
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The store name is required.',
            'name.max' => 'The store name may not be greater than 255 characters.',
            'slug.required' => 'The store slug is required.',
            'slug.unique' => 'The store slug has already been taken.',
            'slug.regex' => 'The store slug may only contain letters, numbers, hyphens, and underscores.',
            'business_type.required' => 'The business type is required.',
            'business_type.in' => 'The selected business type is invalid.',
            'status.required' => 'The store status is required.',
            'status.in' => 'The selected status is invalid.',
            'default_currency.required' => 'The default currency is required.',
            'default_currency.in' => 'The selected default currency is invalid.',
            'default_currency.size' => 'The currency code must be 3 characters.',
            'display_currencies.required' => 'At least one display currency is required.',
            'display_currencies.min' => 'At least one display currency is required.',
            'display_currencies.max' => 'You may select up to 5 display currencies.',
            'display_currencies.*.required' => 'Each currency code is required.',
            'display_currencies.*.in' => 'The selected currency is invalid.',
            'display_currencies.*.size' => 'Each currency code must be 3 characters.',
            'website_url.url' => 'The website URL must be a valid URL.',
        ];
    }

    /**
     * Get custom attributes for validator.
     */
    public function attributes(): array
    {
        return [
            'display_currencies' => 'display currencies',
        ];
    }
}
