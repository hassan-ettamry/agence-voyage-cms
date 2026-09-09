<?php

namespace App\Http\Requests\Offer;

use App\Http\Requests\Concerns\NormalizesTravelCatalogInput;
use App\Models\Offer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfferRequest extends FormRequest
{
    use NormalizesTravelCatalogInput;

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
            'description' => ['required', 'string', 'max:30000'],
            'summary' => ['nullable', 'string', 'max:320'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'between:1,365'],
            'itinerary' => ['nullable', 'array', 'max:30'],
            'itinerary.*.day' => ['required', 'integer', 'between:1,30', 'distinct'],
            'itinerary.*.title' => ['required', 'string', 'max:150'],
            'itinerary.*.description' => ['nullable', 'string', 'max:3000'],
            'inclusions' => ['nullable', 'array', 'max:50'],
            'inclusions.*' => ['required', 'string', 'max:255'],
            'exclusions' => ['nullable', 'array', 'max:50'],
            'exclusions.*' => ['required', 'string', 'max:255'],
            'practical_information' => ['nullable', 'string', 'max:10000'],
            'is_special' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([Offer::STATUS_DRAFT, Offer::STATUS_PUBLISHED])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_special' => $this->boolean('is_special'),
            'status' => $this->input('status', Offer::STATUS_DRAFT),
            'itinerary' => $this->normalizedItinerary(),
            'inclusions' => $this->normalizedStringList('inclusions'),
            'exclusions' => $this->normalizedStringList('exclusions'),
        ]);
    }
}
