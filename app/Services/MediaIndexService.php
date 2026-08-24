<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\User;

class MediaIndexService
{
    private const PER_PAGE_OPTIONS = [8, 16, 24, 48];
    private const TYPE_OPTIONS = ['all', 'images', 'videos', 'documents', 'used', 'unused'];
    private const SORT_OPTIONS = ['latest', 'oldest', 'name', 'size'];

    public function build(array $filters, User $user): array
    {
        $search = $this->stringFilter($filters, 'search');
        $type = $this->optionFilter($filters, 'type', self::TYPE_OPTIONS, 'all');
        $sort = $this->optionFilter($filters, 'sort', self::SORT_OPTIONS, 'latest');
        $perPage = $this->perPage($filters);
        $baseQuery = MediaAsset::query();

        $query = (clone $baseQuery)
            ->withCount(['destinations', 'offers'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('original_name', 'like', "%{$search}%")
                        ->orWhere('alt_text', 'like', "%{$search}%")
                        ->orWhere('copyright_holder', 'like', "%{$search}%")
                        ->orWhere('license', 'like', "%{$search}%");
                });
            });

        $this->applyTypeFilter($query, $type);
        $this->applySort($query, $sort);

        $total = (clone $baseQuery)->count();
        $used = (clone $baseQuery)->where(fn ($query) => $this->whereUsed($query))->count();
        $storageSize = (clone $baseQuery)->sum('size');

        return [
            'mediaAssets' => $query->paginate($perPage)->withQueryString(),
            'filters' => [
                'search' => $search,
                'type' => $type,
                'sort' => $sort,
                'per_page' => $perPage,
            ],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'typeCounts' => [
                'all' => $total,
                'images' => (clone $baseQuery)->where('mime_type', 'like', 'image/%')->count(),
                'videos' => (clone $baseQuery)->where('mime_type', 'like', 'video/%')->count(),
                'documents' => (clone $baseQuery)
                    ->where('mime_type', 'not like', 'image/%')
                    ->where('mime_type', 'not like', 'video/%')
                    ->count(),
                'used' => $used,
                'unused' => (clone $baseQuery)
                    ->whereDoesntHave('destinations')
                    ->whereDoesntHave('offers')
                    ->count(),
            ],
            'stats' => [
                ['label' => 'Media', 'value' => $total, 'note' => 'Images', 'tone' => 'indigo'],
                ['label' => 'Used', 'value' => $used, 'note' => 'Linked', 'tone' => 'emerald'],
                ['label' => 'Storage', 'value' => $this->formatBytes($storageSize), 'note' => 'Total size', 'tone' => 'amber'],
                ['label' => 'This Month', 'value' => (clone $baseQuery)->where('created_at', '>=', now()->startOfMonth())->count(), 'note' => 'Uploaded', 'tone' => 'sky'],
            ],
        ];
    }

    public function picker(array $filters): array
    {
        $search = $this->stringFilter($filters, 'search');

        return MediaAsset::query()
            ->where('mime_type', 'like', 'image/%')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('original_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (MediaAsset $media) => [
                'id' => $media->id,
                'title' => $media->title,
                'original_name' => $media->original_name,
                'alt_text' => $media->alt_text,
                'url' => $media->url,
                'path' => $media->path,
                'copyright_holder' => $media->copyright_holder,
                'license' => $media->license,
                'source_url' => $media->source_url,
            ])
            ->all();
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

    private function optionFilter(array $filters, string $key, array $options, string $default): string
    {
        $value = $this->stringFilter($filters, $key);

        return in_array($value, $options, true) ? $value : $default;
    }

    private function applyTypeFilter($query, string $type): void
    {
        match ($type) {
            'images' => $query->where('mime_type', 'like', 'image/%'),
            'videos' => $query->where('mime_type', 'like', 'video/%'),
            'documents' => $query
                ->where('mime_type', 'not like', 'image/%')
                ->where('mime_type', 'not like', 'video/%'),
            'used' => $query->where(fn ($subQuery) => $this->whereUsed($subQuery)),
            'unused' => $query->whereDoesntHave('destinations')->whereDoesntHave('offers'),
            default => null,
        };
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest(),
            'name' => $query->orderByRaw('COALESCE(NULLIF(title, ""), original_name) asc'),
            'size' => $query->orderByDesc('size'),
            default => $query->latest(),
        };
    }

    private function whereUsed($query)
    {
        return $query->whereHas('destinations')->orWhereHas('offers');
    }

    private function formatBytes(int|float $bytes): string
    {
        if ($bytes <= 0) {
            return '0 KB';
        }

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }

        return number_format($bytes / 1024, 1).' KB';
    }
}
