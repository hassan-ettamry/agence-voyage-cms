<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\Offer;
use App\Support\TravelCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PublicCatalogService
{
    public const PER_PAGE = [6, 9, 12];

    public const DESTINATION_SORTS = ['featured', 'latest', 'name_asc', 'name_desc'];

    public const OFFER_SORTS = ['special', 'latest', 'price_asc', 'price_desc', 'duration_asc', 'duration_desc', 'name_asc'];

    public function destinations(Request $request, string $agencyId, array $props = []): array
    {
        $filters = Validator::make(
            $this->withDefaults($request, $props, 'featured'),
            [
                'q' => ['nullable', 'string', 'max:100'],
                'country' => ['nullable', 'string', 'max:100'],
                'continent' => ['nullable', Rule::in(array_keys(TravelCatalog::CONTINENTS))],
                'type' => ['nullable', Rule::in(array_keys(TravelCatalog::TRAVEL_TYPES))],
                'month' => ['nullable', 'integer', 'between:1,12'],
                'featured' => ['nullable', Rule::in(['1'])],
                'sort' => ['required', Rule::in(self::DESTINATION_SORTS)],
                'view' => ['required', Rule::in(['grid', 'list'])],
                'per_page' => ['required', 'integer', Rule::in(self::PER_PAGE)],
                'page' => ['nullable', 'integer', 'min:1'],
            ]
        )->validate();

        $query = Destination::withoutGlobalScopes()
            ->forAgency($agencyId)
            ->published()
            ->with(['agency', 'media']);

        $query->when($filters['q'] ?? null, function ($query, string $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('practical_information', 'like', "%{$search}%");
            });
        });
        $query->when($filters['country'] ?? null, fn ($query, string $country) => $query->where('country', $country));
        $query->inContinent($filters['continent'] ?? null)
            ->ofTravelType($filters['type'] ?? null)
            ->idealInMonth($filters['month'] ?? null);
        $query->when($filters['featured'] ?? null, fn ($query) => $query->featured());

        match ($filters['sort']) {
            'latest' => $query->latest(),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        $items = $query->paginate((int) $filters['per_page'])->withQueryString();
        $countries = Destination::withoutGlobalScopes()
            ->forAgency($agencyId)
            ->published()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return $this->payload($items, $filters, [
            'countries' => $countries,
            'continents' => TravelCatalog::CONTINENTS,
            'travelTypes' => TravelCatalog::TRAVEL_TYPES,
            'months' => TravelCatalog::MONTHS,
        ]);
    }

    public function offers(Request $request, string $agencyId, array $props = []): array
    {
        $input = $this->withDefaults($request, $props, 'special');
        $maxPriceRules = ['nullable', 'numeric', 'min:0'];

        if (filled($input['min_price'] ?? null)) {
            $maxPriceRules[] = 'gte:min_price';
        }

        $filters = Validator::make($input, [
            'q' => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => $maxPriceRules,
            'duration' => ['nullable', 'integer', 'min:1', 'max:365'],
            'continent' => ['nullable', Rule::in(array_keys(TravelCatalog::CONTINENTS))],
            'type' => ['nullable', Rule::in(array_keys(TravelCatalog::TRAVEL_TYPES))],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'special' => ['nullable', Rule::in(['1'])],
            'sort' => ['required', Rule::in(self::OFFER_SORTS)],
            'view' => ['required', Rule::in(['grid', 'list'])],
            'per_page' => ['required', 'integer', Rule::in(self::PER_PAGE)],
            'page' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $query = Offer::withoutGlobalScopes()
            ->forAgency($agencyId)
            ->published()
            ->with([
                'agency',
                'media',
                'destination' => fn ($query) => $query
                    ->withoutGlobalScopes()
                    ->forAgency($agencyId)
                    ->published()
                    ->with('media'),
            ]);

        $query->when($filters['q'] ?? null, function ($query, string $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('practical_information', 'like', "%{$search}%");
            });
        });
        $query->when($filters['destination'] ?? null, fn ($query, string $slug) => $query->whereHas(
            'destination',
            fn ($destination) => $destination
                ->withoutGlobalScopes()
                ->forAgency($agencyId)
                ->published()
                ->where('slug', $slug)
        ));
        $query->when($filters['min_price'] ?? null, fn ($query, $price) => $query->where('price', '>=', $price));
        $query->when($filters['max_price'] ?? null, fn ($query, $price) => $query->where('price', '<=', $price));
        $query->when($filters['duration'] ?? null, fn ($query, $days) => $query->where('duration_days', '<=', $days));
        $query->when(
            ($filters['continent'] ?? null) || ($filters['type'] ?? null) || ($filters['month'] ?? null),
            fn ($query) => $query->whereHas('destination', fn ($destination) => $destination
                ->withoutGlobalScopes()
                ->forAgency($agencyId)
                ->published()
                ->inContinent($filters['continent'] ?? null)
                ->ofTravelType($filters['type'] ?? null)
                ->idealInMonth($filters['month'] ?? null))
        );
        $query->when($filters['special'] ?? null, fn ($query) => $query->special());

        match ($filters['sort']) {
            'latest' => $query->latest(),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'duration_asc' => $query->orderBy('duration_days'),
            'duration_desc' => $query->orderByDesc('duration_days'),
            'name_asc' => $query->orderBy('title'),
            default => $query->orderByDesc('is_special')->latest(),
        };

        $items = $query->paginate((int) $filters['per_page'])->withQueryString();
        $destinations = Destination::withoutGlobalScopes()
            ->forAgency($agencyId)
            ->published()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return $this->payload($items, $filters, [
            'destinations' => $destinations,
            'continents' => TravelCatalog::CONTINENTS,
            'travelTypes' => TravelCatalog::TRAVEL_TYPES,
            'months' => TravelCatalog::MONTHS,
        ]);
    }

    private function withDefaults(Request $request, array $props, string $fallbackSort): array
    {
        return array_merge([
            'sort' => $props['defaultSort'] ?? $fallbackSort,
            'view' => $props['defaultView'] ?? 'grid',
            'per_page' => (int) ($props['itemsPerPage'] ?? 9),
        ], $request->query());
    }

    private function payload($items, array $filters, array $options): array
    {
        return array_merge($options, [
            'items' => $items,
            'filters' => $filters,
            'view' => $filters['view'],
            'sort' => $filters['sort'],
            'perPage' => (int) $filters['per_page'],
        ]);
    }
}
