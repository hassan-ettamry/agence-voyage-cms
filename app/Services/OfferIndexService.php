<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Models\User;

class OfferIndexService
{
    private const PER_PAGE_OPTIONS = [8, 16, 32, 64];

    public function __construct(private DemoTravelContentService $demoTravelContentService)
    {
    }

    public function build(array $filters, User $user): array
    {
        $baseQuery = Offer::query();
        $search = $this->stringFilter($filters, 'search');
        $filter = $this->stringFilter($filters, 'filter');
        $destinationId = $this->stringFilter($filters, 'destination_id');

        $query = (clone $baseQuery)
            ->with(['destination', 'media'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(in_array($filter, [Offer::STATUS_PUBLISHED, Offer::STATUS_DRAFT], true), fn ($query) => $query->where('status', $filter))
            ->when($filter === 'special', fn ($query) => $query->special())
            ->when($destinationId !== '', fn ($query) => $query->where('destination_id', $destinationId));

        return [
            'offers' => $query->latest()->paginate($this->perPage($filters))->withQueryString(),
            'destinations' => Destination::orderBy('name')->get(),
            'mediaAssets' => MediaAsset::latest()->get(),
            'showDemoContentCta' => $user->isAdmin() && $this->demoTravelContentService->needsDemoContent($user),
            'stats' => [
                ['label' => 'Offers', 'value' => (clone $baseQuery)->count(), 'note' => 'All offers', 'tone' => 'violet'],
                ['label' => 'Published', 'value' => (clone $baseQuery)->published()->count(), 'note' => 'Live now', 'tone' => 'emerald'],
                ['label' => 'Special', 'value' => (clone $baseQuery)->special()->count(), 'note' => 'Promotions', 'tone' => 'orange'],
                ['label' => 'Draft', 'value' => (clone $baseQuery)->where('status', Offer::STATUS_DRAFT)->count(), 'note' => 'Not published', 'tone' => 'blue'],
            ],
        ];
    }

    private function perPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 8);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 8;
    }

    private function stringFilter(array $filters, string $key): string
    {
        $value = $filters[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }
}
