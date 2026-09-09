<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\User;

class DestinationIndexService
{
    private const PER_PAGE_OPTIONS = [8, 16, 32, 64];

    public function __construct(private DemoTravelContentService $demoTravelContentService)
    {
    }

    public function build(array $filters, User $user): array
    {
        $baseQuery = Destination::query();
        $search = $this->stringFilter($filters, 'search');
        $filter = $this->stringFilter($filters, 'filter');

        $query = (clone $baseQuery)
            ->withCount('offers')
            ->with('media')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('country', 'like', "%{$search}%")
                        ->orWhere('continent', 'like', "%{$search}%")
                        ->orWhere('region', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(in_array($filter, [Destination::STATUS_PUBLISHED, Destination::STATUS_DRAFT], true), fn ($query) => $query->where('status', $filter))
            ->when($filter === 'featured', fn ($query) => $query->featured());

        return [
            'destinations' => $query->latest()->paginate($this->perPage($filters))->withQueryString(),
            'mediaAssets' => \App\Models\MediaAsset::latest()->get(),
            'showDemoContentCta' => $user->isAdmin() && $this->demoTravelContentService->needsDemoContent($user),
            'stats' => [
                ['label' => 'Destinations', 'value' => (clone $baseQuery)->count(), 'note' => 'All destinations', 'tone' => 'violet'],
                ['label' => 'Published', 'value' => (clone $baseQuery)->published()->count(), 'note' => 'Live now', 'tone' => 'emerald'],
                ['label' => 'Featured', 'value' => (clone $baseQuery)->featured()->count(), 'note' => 'Highlighted places', 'tone' => 'orange'],
                ['label' => 'Draft', 'value' => (clone $baseQuery)->where('status', Destination::STATUS_DRAFT)->count(), 'note' => 'Not published', 'tone' => 'blue'],
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
