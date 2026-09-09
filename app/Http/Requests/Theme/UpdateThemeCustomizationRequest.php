<?php

namespace App\Http\Requests\Theme;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateThemeCustomizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        foreach (config('site-theme.colors', []) as $key) {
            $rules[$key] = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];
        }

        $rules['bodyFont'] = ['required', Rule::in(array_values(config('site-theme.fonts', [])))];
        $rules['headingFont'] = ['required', Rule::in(array_values(config('site-theme.fonts', [])))];
        $rules['radius'] = ['required', Rule::in(config('site-theme.radii', []))];
        $rules['shadow'] = ['required', Rule::in(array_keys(config('site-theme.shadows', [])))];

        return $rules;
    }
}
