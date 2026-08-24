<?php

namespace App\Http\Requests\Destination;

use App\Http\Requests\Concerns\NormalizesTravelCatalogInput;
use App\Models\Destination;
use App\Support\TravelCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDestinationRequest extends FormRequest
{
    use NormalizesTravelCatalogInput;

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $destination = $this->route('destination');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('destinations', 'slug')
                    ->where('agency_id', $this->user()->agency_id)
                    ->ignore($destination?->id),
            ],
            'country' => ['required', 'string', 'max:255'],
            'continent' => ['nullable', Rule::in(array_keys(TravelCatalog::CONTINENTS))],
            'region' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:20000'],
            'travel_types' => ['nullable', 'array', 'max:9'],
            'travel_types.*' => ['string', 'distinct', Rule::in(array_keys(TravelCatalog::TRAVEL_TYPES))],
            'ideal_months' => ['nullable', 'array', 'max:12'],
            'ideal_months.*' => ['integer', 'distinct', 'between:1,12'],
            'practical_information' => ['nullable', 'string', 'max:10000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in([Destination::STATUS_DRAFT, Destination::STATUS_PUBLISHED])],
            'media_ids' => ['nullable', 'array', 'max:50'],
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
            'travel_types' => $this->normalizedStringList('travel_types'),
            'ideal_months' => $this->normalizedIntegerList('ideal_months'),
        ]);
    }
}
