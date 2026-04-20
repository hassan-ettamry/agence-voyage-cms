<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreComponentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'type' => 'required|string|unique:components|max:255',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'schema_json' => 'required|array',
            'preview_image' => 'nullable|string|url'
        ];
    }
}
