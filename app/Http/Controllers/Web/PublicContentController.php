<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Destination;
use App\Models\Offer;
use App\Models\Page;
use App\Services\CatalogPageStructure;
use App\Services\MenuService;
use App\Services\PageService;
use App\Services\PublicCatalogService;
use App\Services\PublicContentCache;
use App\Services\PublicSiteUrl;
use App\Services\Renderer\PageRenderer;
use App\Support\AgencyContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicContentController extends Controller
{
    public function destinations(
        Request $request,
        MenuService $menuService,
        PublicCatalogService $catalogService,
        CatalogPageStructure $catalogPages,
        PageRenderer $renderer,
        PageService $pageService
    )
    {
        $agency = $this->publicAgency($request);
        $page = $this->catalogPage($agency, 'destinations');
        $node = $page ? $catalogPages->catalogNode($page->structure ?? [], 'destinations') : null;
        $props = $node['props'] ?? $this->destinationCatalogDefaults();
        $catalog = $catalogService->destinations($request, $agency->id, $props);
        $request->attributes->set('destinationCatalog', $catalog);

        if ($page && $node) {
            return $this->renderCatalogPage($page, $agency, $renderer, $pageService, $menuService);
        }

        return view('frontend.destinations.index', [
            'catalogProps' => $props,
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

    public function offers(
        Request $request,
        MenuService $menuService,
        PublicCatalogService $catalogService,
        CatalogPageStructure $catalogPages,
        PageRenderer $renderer,
        PageService $pageService
    )
    {
        $agency = $this->publicAgency($request);
        $page = $this->catalogPage($agency, 'offers');
        $node = $page ? $catalogPages->catalogNode($page->structure ?? [], 'offers') : null;
        $props = $node['props'] ?? $this->offerCatalogDefaults();
        $catalog = $catalogService->offers($request, $agency->id, $props);
        $request->attributes->set('offerCatalog', $catalog);

        if ($page && $node) {
            return $this->renderCatalogPage($page, $agency, $renderer, $pageService, $menuService);
        }

        return view('frontend.offers.index', [
            'catalogProps' => $props,
            'siteAgency' => $agency,
            'menu' => $menuService->getMenu('main'),
            'metaTitle' => 'Offers',
            'metaDescription' => 'Discover our current travel offers.',
        ]);
    }

    private function catalogPage(Agency $agency, string $slug): ?Page
    {
        return Page::withoutGlobalScopes()
            ->forAgency($agency->id)
            ->published()
            ->where('slug', $slug)
            ->first();
    }

    private function renderCatalogPage(
        Page $page,
        Agency $agency,
        PageRenderer $renderer,
        PageService $pageService,
        MenuService $menuService
    ) {
        AgencyContext::set($agency->id);
        $page = $pageService->ensureCanonicalStructure($page);

        return view('frontend.page', [
            'page' => $page,
            'html' => $renderer->render($page->structure ?? [], 'live'),
            'menu' => $menuService->getMenu('main'),
            'siteAgency' => $agency,
            'metaTitle' => data_get($page->meta, 'title') ?: $page->title,
            'metaDescription' => data_get($page->meta, 'description') ?: str($page->title)->append(' — ', $agency->name),
        ]);
    }

    private function destinationCatalogDefaults(): array
    {
        return [
            'catalogMode' => 'yes',
            'eyebrow' => 'DESTINATION COLLECTION',
            'title' => 'Choose your next chapter',
            'intro' => 'Filter by place and travel style, then explore the destination in detail.',
            'defaultView' => 'grid',
            'defaultSort' => 'featured',
            'itemsPerPage' => '9',
            'columns' => 3,
            'imageRatio' => '4/3',
            'showViewAll' => 'no',
        ];
    }

    private function offerCatalogDefaults(): array
    {
        return [
            'catalogMode' => 'yes',
            'eyebrow' => 'CURATED JOURNEYS',
            'title' => 'Find your perfect journey',
            'intro' => 'Compare complete itinerary ideas and refine the collection around your plans.',
            'defaultView' => 'grid',
            'defaultSort' => 'special',
            'itemsPerPage' => '9',
            'columns' => 3,
            'imageRatio' => '4/3',
            'showViewAll' => 'no',
        ];
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
