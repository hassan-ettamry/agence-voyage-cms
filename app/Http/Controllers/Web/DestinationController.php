<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Destination\StoreDestinationRequest;
use App\Http\Requests\Destination\UpdateDestinationRequest;
use App\Models\Destination;
use App\Services\DashboardStatsService;
use App\Services\DestinationIndexService;
use App\Services\DestinationService;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function __construct(
        private DestinationService $destinationService,
        private DestinationIndexService $destinationIndexService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Destination::class);

        return view('destinations.index', $this->destinationIndexService->build($request->query(), $request->user()));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Destination::class);

        return view('destinations.index', array_merge(
            $this->destinationIndexService->build($request->query(), $request->user()),
            ['openCreateModal' => true]
        ));
    }

    public function store(StoreDestinationRequest $request)
    {
        $this->authorize('create', Destination::class);

        $this->destinationService->create($request->validated(), $request->user());
        DashboardStatsService::forgetFor($request->user());

        return redirect()->route('destinations.index')->with('success', 'Destination creee avec succes.');
    }

    public function edit(Destination $destination)
    {
        $this->authorize('update', $destination);

        $destination->load('media');
        $mediaAssets = \App\Models\MediaAsset::latest()->get();

        return view('destinations.edit', compact('destination', 'mediaAssets'));
    }

    public function update(UpdateDestinationRequest $request, Destination $destination)
    {
        $this->authorize('update', $destination);

        $this->destinationService->update($destination, $request->validated(), $request->user());
        DashboardStatsService::forgetFor($request->user());

        return redirect()->route('destinations.index')->with('success', 'Destination mise a jour.');
    }

    public function destroy(Destination $destination)
    {
        $this->authorize('delete', $destination);

        try {
            $this->destinationService->delete($destination);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        DashboardStatsService::forgetFor(auth()->user());

        return redirect()->route('destinations.index')->with('success', 'Destination supprimee.');
    }
}
