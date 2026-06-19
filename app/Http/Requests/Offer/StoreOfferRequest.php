<?php

namespace App\Http\Requests\Offer;

use App\Models\Offer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'destination_id' => [
                'nullable',
                'uuid',
                Rule::exists('destinations', 'id')->where('agency_id', $this->user()->agency_id),
            ],
            'media_asset_id' => [
                'nullable',
                'uuid',
                Rule::exists('media_assets', 'id')->where('agency_id', $this->user()->agency_id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('offers', 'slug')->where('agency_id', $this->user()->agency_id),
            ],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'is_special' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([Offer::STATUS_DRAFT, Offer::STATUS_PUBLISHED])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_special' => $this->boolean('is_special'),
            'status' => $this->input('status', Offer::STATUS_DRAFT),
        ]);
    }
}
