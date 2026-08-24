<?php

namespace App\Http\Requests\Account;

use App\Support\SecureImageRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'bio' => ['nullable', 'string', 'max:160'],
            'avatar' => SecureImageRules::rules(),
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }
}
