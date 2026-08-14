<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Destination;
use App\Models\Offer;
use App\Services\MenuService;
use App\Services\PublicContentCache;
use App\Services\PublicSiteUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicContentController extends Controller
{
    public function destinations(Request $request, MenuService $menuService)
    {
        $agency = $this->publicAgency($request);
        $destinations = Destination::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->with(['media'])
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12);

        return view('frontend.destinations.index', [
            'destinations' => $destinations,
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
        $offers = Offer::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->with(['destination' => fn ($query) => $query->published(), 'media'])
            ->orderByDesc('is_special')
            ->latest()
            ->paginate(12);

        return view('frontend.offers.index', [
            'offers' => $offers,
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

        return view('frontend.offers.show', [
            'offer' => $offer,
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
