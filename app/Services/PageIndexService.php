<?php

namespace App\Services;

use App\Models\Page;
use App\Models\User;

class PageIndexService
{
    private const PER_PAGE_OPTIONS = [8, 16, 32, 64];

    public function build(array $filters, User $user): array
    {
        $baseQuery = Page::query();
        $search = $this->stringFilter($filters, 'search');
        $statusFilter = $this->stringFilter($filters, 'filter');
        $status = $this->stringFilter($filters, 'status');

        $query = (clone $baseQuery)
            ->with('menuItems.menu')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(in_array($statusFilter, [
                Page::STATUS_PUBLISHED,
                Page::STATUS_DRAFT,
            ], true), function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            });

        $pages = $query->latest()
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return [
            'pages' => $pages,
            'menus' => app(MenuService::class)->optionsForUser($user),
            'stats' => [
                [
                    'label' => 'Total Pages',
                    'value' => (clone $baseQuery)->count(),
                    'note' => 'All pages',
                    'tone' => 'violet',
                    'icon' => 'page',
                ],
                [
                    'label' => 'Published',
                    'value' => (clone $baseQuery)->where('status', Page::STATUS_PUBLISHED)->count(),
                    'note' => 'Live pages',
                    'tone' => 'emerald',
                    'icon' => 'check',
                ],
                [
                    'label' => 'Draft',
                    'value' => (clone $baseQuery)->where('status', Page::STATUS_DRAFT)->count(),
                    'note' => 'Not published',
                    'tone' => 'orange',
                    'icon' => 'pencil',
                ],
                [
                    'label' => 'Recently Created',
                    'value' => (clone $baseQuery)->where('created_at', '>=', now()->subMonth())->count(),
                    'note' => 'Last 30 days',
                    'tone' => 'blue',
                    'icon' => 'calendar',
                ],
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
