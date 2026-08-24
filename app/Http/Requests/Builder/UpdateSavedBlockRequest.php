<?php

namespace App\Http\Requests\Builder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSavedBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('savedBlock')) === true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('builder_saved_blocks')->where('agency_id', $this->user()->agency_id)->ignore($this->route('savedBlock')->id),
            ],
            'category' => ['nullable', 'string', 'max:80'],
        ];
    }
}
