<?php

namespace App\Http\Requests\Media;

use App\Support\SecureImageRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $singleRules = SecureImageRules::rules();
        $singleRules[0] = 'required_without:files';
        $batchRules = SecureImageRules::rules(required: true);

        return [
            'file' => $singleRules,
            'files' => ['nullable', 'array', 'min:1', 'max:10', 'required_without:file'],
            'files.*' => $batchRules,
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'copyright_holder' => ['nullable', 'string', 'max:255'],
            'license' => ['nullable', 'string', 'max:100'],
            'source_url' => ['nullable', 'url:http,https', 'max:2048'],
        ];
    }
}
