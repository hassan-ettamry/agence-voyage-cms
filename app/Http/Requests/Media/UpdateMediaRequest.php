<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'copyright_holder' => ['nullable', 'string', 'max:255'],
            'license' => ['nullable', 'string', 'max:100'],
            'source_url' => ['nullable', 'url:http,https', 'max:2048'],
        ];
    }
}
