<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'language' => ['required', Rule::in(['en', 'fr', 'es', 'ar'])],
            'timezone' => ['required', 'string', 'max:100'],
            'date_format' => ['required', Rule::in(['M d, Y', 'd M Y', 'Y-m-d'])],
            'time_format' => ['required', Rule::in(['12h', '24h'])],
            'email_notifications' => ['nullable', 'boolean'],
            'product_updates' => ['nullable', 'boolean'],
            'security_alerts' => ['nullable', 'boolean'],
        ];
    }
}
