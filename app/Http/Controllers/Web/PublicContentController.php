<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Destination;
use App\Models\Offer;
use App\Services\MenuService;
use App\Services\PublicContentCache;
use App\Services\PublicSiteUrl;
use App\Support\TravelCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicContentController extends Controller
{
    public function destinations(Request $request, MenuService $menuService)
    {
        $agency = $this->publicAgency($request);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'continent' => ['nullable', 'in:'.implode(',', array_keys(TravelCatalog::CONTINENTS))],
            'type' => ['nullable', 'in:'.implode(',', array_keys(TravelCatalog::TRAVEL_TYPES))],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'featured' => ['nullable', 'in:1'],
        ]);
        $query = Destination::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->with(['media']);

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

        $destinations = $query
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();
        $countries = Destination::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');

        return view('frontend.destinations.index', [
            'destinations' => $destinations,
            'countries' => $countries,
            'continents' => TravelCatalog::CONTINENTS,
            'travelTypes' => TravelCatalog::TRAVEL_TYPES,
            'months' => TravelCatalog::MONTHS,
            'filters' => $filters,
            'siteAgency' => $agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => 'Destinations',
            'metaDescription' => 'Explore our travel destinations.',
        ]);
    }

    public function destination(
        Request $request,
        string $agencySlug,
        string $destinationSlug,
        MenuService $menuService
    ) {
        $agency = $this->publicAgency($request);
        $cacheKey = PublicContentCache::destinationKey($agency->id, $destinationSlug);
        $destination = Cache::remember($cacheKey, now()->addHour(), fn () => Destination::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->with(['media', 'offers' => fn ($query) => $query
                ->withoutGlobalScopes()
                ->forAgency($agency->id)
                ->published()
                ->with('media')])
            ->where('slug', $destinationSlug)
            ->firstOrFail());

        return view('frontend.destinations.show', [
            'destination' => $destination,
            'siteAgency' => $agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => $destination->name,
            'metaDescription' => str($destination->description)->limit(150),
        ]);
    }

    public function offers(Request $request, MenuService $menuService)
    {
        $agency = $this->publicAgency($request);
        $maxPriceRules = ['nullable', 'numeric', 'min:0'];
        if ($request->filled('min_price')) {
            $maxPriceRules[] = 'gte:min_price';
        }
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'destination' => ['nullable', 'string', 'max:120'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => $maxPriceRules,
            'duration' => ['nullable', 'integer', 'min:1', 'max:365'],
            'continent' => ['nullable', 'in:'.implode(',', array_keys(TravelCatalog::CONTINENTS))],
            'type' => ['nullable', 'in:'.implode(',', array_keys(TravelCatalog::TRAVEL_TYPES))],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'special' => ['nullable', 'in:1'],
        ]);
        $query = Offer::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->with(['destination' => fn ($query) => $query
                ->withoutGlobalScopes()
                ->forAgency($agency->id)
                ->published(), 'media']);

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
            fn ($destination) => $destination->withoutGlobalScopes()
                ->forAgency($agency->id)
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
                ->forAgency($agency->id)
                ->published()
                ->inContinent($filters['continent'] ?? null)
                ->ofTravelType($filters['type'] ?? null)
                ->idealInMonth($filters['month'] ?? null))
        );
        $query->when($filters['special'] ?? null, fn ($query) => $query->special());

        $offers = $query
            ->orderByDesc('is_special')
            ->latest()
            ->paginate(12)
            ->withQueryString();
        $destinations = Destination::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('frontend.offers.index', [
            'offers' => $offers,
            'destinations' => $destinations,
            'continents' => TravelCatalog::CONTINENTS,
            'travelTypes' => TravelCatalog::TRAVEL_TYPES,
            'months' => TravelCatalog::MONTHS,
            'filters' => $filters,
            'siteAgency' => $agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => 'Offers',
            'metaDescription' => 'Discover our current travel offers.',
        ]);
    }

    public function offer(
        Request $request,
        string $agencySlug,
        string $offerSlug,
        MenuService $menuService
    ) {
        $agency = $this->publicAgency($request);
        $cacheKey = PublicContentCache::offerKey($agency->id, $offerSlug);
        $offer = Cache::remember($cacheKey, now()->addHour(), fn () => Offer::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->with(['destination' => fn ($query) => $query
                ->withoutGlobalScopes()
                ->forAgency($agency->id)
                ->published()
                ->with('media'), 'media'])
            ->where('slug', $offerSlug)
            ->firstOrFail());

        abort_if($offer->destination && $offer->destination->agency_id !== $agency->id, 404);

        $relatedOffers = Offer::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->with(['destination' => fn ($query) => $query
                ->withoutGlobalScopes()
                ->forAgency($agency->id)
                ->published(), 'media'])
            ->whereKeyNot($offer->id)
            ->when($offer->destination_id, fn ($query) => $query->where('destination_id', $offer->destination_id))
            ->limit(3)
            ->get();

        return view('frontend.offers.show', [
            'offer' => $offer,
            'relatedOffers' => $relatedOffers,
            'siteAgency' => $agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => $offer->title,
            'metaDescription' => str($offer->description)->limit(150),
        ]);
    }

    public function legacyDestinations(): never
    {
        abort(404);
    }

    public function legacyOffers(): never
    {
        abort(404);
    }

    public function legacyDestination(string $slug, PublicSiteUrl $urls)
    {
        $destinations = Destination::withoutGlobalScopes()
            ->published()
            ->where('slug', $slug)
            ->whereHas('agency', fn ($query) => $query->where('status', 'active'))
            ->with('agency')
            ->limit(2)
            ->get();

        abort_unless($destinations->count() === 1, 404);
        $destination = $destinations->first();

        return redirect()->to($urls->destination($destination->agency, $destination), 301);
    }

    public function legacyOffer(string $slug, PublicSiteUrl $urls)
    {
        $offers = Offer::withoutGlobalScopes()
            ->published()
            ->where('slug', $slug)
            ->whereHas('agency', fn ($query) => $query->where('status', 'active'))
            ->with('agency')
            ->limit(2)
            ->get();

        abort_unless($offers->count() === 1, 404);
        $offer = $offers->first();

        return redirect()->to($urls->offer($offer->agency, $offer), 301);
    }

    private function publicAgency(Request $request): Agency
    {
        $agency = $request->attributes->get('publicAgency');

        abort_unless($agency instanceof Agency, 404);

        return $agency;
    }
}
