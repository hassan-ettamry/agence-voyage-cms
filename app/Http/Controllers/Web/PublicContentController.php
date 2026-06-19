<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Offer;
use App\Services\MenuService;

class PublicContentController extends Controller
{
    public function destinations(MenuService $menuService)
    {
        $destinations = Destination::withoutGlobalScopes()
            ->published()
            ->with(['media', 'agency.theme'])
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12);

        return view('frontend.destinations.index', [
            'destinations' => $destinations,
            'siteAgency' => $destinations->first()?->agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => 'Destinations',
            'metaDescription' => 'Explore our travel destinations.',
        ]);
    }

    public function destination(string $slug, MenuService $menuService)
    {
        $destination = Destination::withoutGlobalScopes()
            ->published()
            ->with(['agency.theme', 'media', 'offers' => fn ($query) => $query->published()->with('media')])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('frontend.destinations.show', [
            'destination' => $destination,
            'siteAgency' => $destination->agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => $destination->name,
            'metaDescription' => str($destination->description)->limit(150),
        ]);
    }

    public function offers(MenuService $menuService)
    {
        $offers = Offer::withoutGlobalScopes()
            ->published()
            ->with(['agency.theme', 'destination', 'media'])
            ->orderByDesc('is_special')
            ->latest()
            ->paginate(12);

        return view('frontend.offers.index', [
            'offers' => $offers,
            'siteAgency' => $offers->first()?->agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => 'Offers',
            'metaDescription' => 'Discover our current travel offers.',
        ]);
    }

    public function offer(string $slug, MenuService $menuService)
    {
        $offer = Offer::withoutGlobalScopes()
            ->published()
            ->with(['agency.theme', 'destination.media', 'media'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('frontend.offers.show', [
            'offer' => $offer,
            'siteAgency' => $offer->agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => $offer->title,
            'metaDescription' => str($offer->description)->limit(150),
        ]);
    }
}
