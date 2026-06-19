<?php

namespace App\Http\Requests\Destination;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('destinations', 'slug')->where('agency_id', $this->user()->agency_id),
            ],
            'country' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([Destination::STATUS_DRAFT, Destination::STATUS_PUBLISHED])],
            'media_ids' => ['nullable', 'array'],
            'media_ids.*' => [
                'uuid',
                Rule::exists('media_assets', 'id')->where('agency_id', $this->user()->agency_id),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'status' => $this->input('status', Destination::STATUS_DRAFT),
        ]);
    }
}
