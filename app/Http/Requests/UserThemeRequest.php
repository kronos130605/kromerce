<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserThemeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // User can update their own theme preferences
    }
    
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'dark_mode' => 'sometimes|boolean',
            'theme_preferences' => 'sometimes|array',
            'theme_preferences.*' => 'string',
            'language' => 'sometimes|string|max:5|in:en,es,fr,de,it,pt',
        ];
    }
    
    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'dark_mode.boolean' => 'Dark mode must be true or false',
            'theme_preferences.array' => 'Theme preferences must be an array',
            'language.in' => 'Language must be one of: en, es, fr, de, it, pt',
            'language.max' => 'Language code must not exceed 5 characters',
        ];
    }
}
