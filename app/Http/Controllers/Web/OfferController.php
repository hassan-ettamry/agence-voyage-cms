<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Offer\StoreOfferRequest;
use App\Http\Requests\Offer\UpdateOfferRequest;
use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Services\DashboardStatsService;
use App\Services\OfferIndexService;
use App\Services\OfferService;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function __construct(
        private OfferService $offerService,
        private OfferIndexService $offerIndexService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Offer::class);

        return view('offers.index', $this->offerIndexService->build($request->query(), $request->user()));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Offer::class);

        return view('offers.index', array_merge(
            $this->offerIndexService->build($request->query(), $request->user()),
            ['openCreateModal' => true]
        ));
    }

    public function store(StoreOfferRequest $request)
    {
        $this->authorize('create', Offer::class);

        $this->offerService->create($request->validated(), $request->user());
        DashboardStatsService::forgetFor($request->user());

        return redirect()->route('offers.index')->with('success', 'Offre creee avec succes.');
    }

    public function edit(Offer $offer)
    {
        $this->authorize('update', $offer);

        $destinations = Destination::orderBy('name')->get();
        $mediaAssets = MediaAsset::latest()->get();

        return view('offers.edit', compact('offer', 'destinations', 'mediaAssets'));
    }

    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $this->authorize('update', $offer);

        $this->offerService->update($offer, $request->validated(), $request->user());
        DashboardStatsService::forgetFor($request->user());

        return redirect()->route('offers.index')->with('success', 'Offre mise a jour.');
    }

    public function destroy(Offer $offer)
    {
        $this->authorize('delete', $offer);

        $offer->delete();
        DashboardStatsService::forgetFor(auth()->user());

        return redirect()->route('offers.index')->with('success', 'Offre supprimee.');
    }
}
