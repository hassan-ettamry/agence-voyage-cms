<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Models\Page;
use App\Models\PageVersion;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Theme;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardStatsService
{
    private const CACHE_PREFIX = 'dashboard_stats_v3_';
    private const PERIOD_DAYS = 30;

    public function __construct(private DemoTravelContentService $demoTravelContentService)
    {
    }

    public function build(User $user): array
    {
        $agency = Agency::withoutGlobalScopes()
            ->with(['theme', 'activeSiteTemplate'])
            ->find($user->agency_id);

        $payload = $this->dashboardPayload($user, $agency);

        return array_merge($payload, [
            'showOnboardingCta' => $user->isAdmin() && $agency !== null && ! $agency->onboardingIsComplete(),
            'showDemoContentCta' => $user->isAdmin() && $this->demoTravelContentService->needsDemoContent($user),
            'builderPage' => Page::query()
                ->orderByRaw("CASE WHEN slug IN ('home', 'accueil') THEN 0 ELSE 1 END")
                ->oldest('created_at')
                ->first(),
        ]);
    }

    public function chartStats(User $user): array
    {
        $agency = Agency::withoutGlobalScopes()
            ->with(['theme', 'activeSiteTemplate'])
            ->find($user->agency_id);

        return $this->analytics($user, $agency);
    }

    public static function forgetFor(?User $user): void
    {
        if ($user === null) {
            return;
        }

        Cache::forget(self::cacheKey($user));
    }

    public static function cacheKey(User $user): string
    {
        return self::CACHE_PREFIX.$user->id;
    }

    private function dashboardPayload(User $user, ?Agency $agency): array
    {
        $period = $this->period();
        $pageCount = Page::count();
        $publishedPageCount = Page::where('status', Page::STATUS_PUBLISHED)->count();
        $draftPageCount = Page::where('status', Page::STATUS_DRAFT)->count();
        $userCount = User::where('agency_id', $user->agency_id)->count();
        $newUsers = User::where('agency_id', $user->agency_id)
            ->whereBetween('created_at', [$period['currentStart'], $period['currentEnd']])
            ->count();
        $mediaCount = MediaAsset::count();
        $usedMediaCount = $this->usedMediaQuery()->count();
        $roleCount = Role::count();
        $permissionCount = Permission::count();
        $themeCount = Theme::count();

        return [
            'dashboardContext' => [
                'agencyName' => $agency?->name ?? 'Agency CMS',
                'periodLabel' => 'Last '.self::PERIOD_DAYS.' days',
                'generatedAt' => now()->format('M d, H:i'),
                'currentTheme' => $agency?->theme?->name ?? 'No active theme',
                'activeTemplate' => $agency?->activeSiteTemplate?->name ?? 'No active template',
                'siteStatus' => $agency?->onboardingIsComplete() ? 'Ready' : 'Setup pending',
                'greetingName' => strtok($user->name ?? 'Admin', ' ') ?: 'Admin',
                'dateRange' => $period['currentStart']->format('M j').' - '.$period['currentEnd']->format('M j'),
            ],
            'summaryMetrics' => $this->summaryMetrics($pageCount, $publishedPageCount, $draftPageCount, $userCount),
            'executiveKpis' => $this->executiveKpis($user, $pageCount, $publishedPageCount, $userCount, $newUsers, $mediaCount, $period),
            'analytics' => $this->analytics($user, $agency),
            'operations' => $this->operations(
                $agency,
                $pageCount,
                $publishedPageCount,
                $draftPageCount,
                $mediaCount,
                $usedMediaCount,
                $userCount,
                $roleCount,
                $permissionCount,
                $themeCount
            ),
            'activityFeed' => $this->activityFeed($user),
            'systemEvents' => $this->systemEvents($roleCount, $permissionCount),
            'quickActions' => $this->quickActions($draftPageCount),
        ];
    }

    private function summaryMetrics(int $pageCount, int $publishedPageCount, int $draftPageCount, int $userCount): array
    {
        return [
            [
                'label' => 'Total Pages',
                'value' => $this->formatNumber($pageCount),
                'icon' => 'document-text',
                'tone' => 'indigo',
            ],
            [
                'label' => 'Published',
                'value' => $this->formatNumber($publishedPageCount),
                'icon' => 'globe-alt',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Drafts',
                'value' => $this->formatNumber($draftPageCount),
                'icon' => 'bell-alert',
                'tone' => 'amber',
            ],
            [
                'label' => 'Active Users',
                'value' => $this->formatNumber($userCount),
                'icon' => 'users',
                'tone' => 'blue',
            ],
        ];
    }

    private function executiveKpis(
        User $user,
        int $pageCount,
        int $publishedPageCount,
        int $userCount,
        int $newUsers,
        int $mediaCount,
        array $period
    ): array {
        $currentPages = Page::whereBetween('created_at', [$period['currentStart'], $period['currentEnd']])->count();
        $previousPages = Page::whereBetween('created_at', [$period['previousStart'], $period['previousEnd']])->count();
        $currentPublished = Page::where('status', Page::STATUS_PUBLISHED)
            ->whereBetween('updated_at', [$period['currentStart'], $period['currentEnd']])
            ->count();
        $previousPublished = Page::where('status', Page::STATUS_PUBLISHED)
            ->whereBetween('updated_at', [$period['previousStart'], $period['previousEnd']])
            ->count();
        $previousNewUsers = User::where('agency_id', $user->agency_id)
            ->whereBetween('created_at', [$period['previousStart'], $period['previousEnd']])
            ->count();
        $currentMedia = MediaAsset::whereBetween('created_at', [$period['currentStart'], $period['currentEnd']])->count();
        $previousMedia = MediaAsset::whereBetween('created_at', [$period['previousStart'], $period['previousEnd']])->count();

        $pageSeries = $this->dailySeries(Page::class, 'created_at', $period['currentStart'], $period['currentEnd']);
        $userSeries = $this->dailySeries(User::class, 'created_at', $period['currentStart'], $period['currentEnd'], fn ($query) => $query->where('agency_id', $user->agency_id));
        $mediaSeries = $this->dailySeries(MediaAsset::class, 'created_at', $period['currentStart'], $period['currentEnd']);

        return [
            $this->kpi(
                'Total Pages',
                $this->formatNumber($pageCount),
                'document-text',
                'indigo',
                $currentPages,
                $previousPages,
                $pageSeries,
                $period,
                "{$currentPages} new"
            ),
            $this->kpi(
                'Published Pages',
                $this->formatNumber($publishedPageCount),
                'globe-alt',
                'emerald',
                $currentPublished,
                $previousPublished,
                $this->dailySeries(Page::class, 'updated_at', $period['currentStart'], $period['currentEnd'], fn ($query) => $query->where('status', Page::STATUS_PUBLISHED)),
                $period,
                "{$publishedPageCount} live"
            ),
            $this->kpi(
                'Active Users',
                $this->formatNumber($userCount),
                'users',
                'blue',
                $newUsers,
                $previousNewUsers,
                $userSeries,
                $period,
                "{$newUsers} new"
            ),
            $this->kpi(
                'New Users',
                $this->formatNumber($newUsers),
                'user-plus',
                'amber',
                $newUsers,
                $previousNewUsers,
                $userSeries,
                $period,
                "{$previousNewUsers} previous"
            ),
            $this->kpi(
                'Media Assets',
                $this->formatNumber($mediaCount),
                'photo',
                'pink',
                $currentMedia,
                $previousMedia,
                $mediaSeries,
                $period,
                "{$currentMedia} uploaded"
            ),
        ];
    }

    private function analytics(User $user, ?Agency $agency): array
    {
        $contentSeries = $this->monthlyContentSeries();
        $userGrowth = $this->monthlySeries(User::class, 'created_at', 12, fn ($query) => $query->where('agency_id', $user->agency_id));
        $publishedCount = Page::where('status', Page::STATUS_PUBLISHED)->count();
        $draftCount = Page::where('status', Page::STATUS_DRAFT)->count();
        $archivedCount = 0;
        $statusTotal = max($publishedCount + $draftCount + $archivedCount, 1);
        $engagementSeries = $this->monthlyEngagementSeries();

        return [
            'contentPublishing' => [
                'id' => 'content-publishing',
                'type' => 'multi-line',
                'title' => 'Content Publishing Trend',
                'summary' => null,
                'max' => $this->niceChartMax(array_merge($contentSeries['pages'], $contentSeries['destinations'], $contentSeries['offers'])),
                'labels' => $contentSeries['labels'],
                'datasets' => [
                    ['label' => 'Pages', 'color' => '#6c3cff', 'values' => $contentSeries['pages']],
                    ['label' => 'Destinations', 'color' => '#16b978', 'values' => $contentSeries['destinations']],
                    ['label' => 'Offers', 'color' => '#2f80ed', 'values' => $contentSeries['offers']],
                ],
            ],
            'userGrowth' => [
                'id' => 'user-growth',
                'type' => 'bar',
                'title' => 'User Growth',
                'summary' => null,
                'max' => $this->niceChartMax($userGrowth['values']),
                'labels' => $userGrowth['labels'],
                'values' => $userGrowth['values'],
                'color' => '#2f80ed',
                'legend' => [
                    ['label' => 'New Users', 'color' => '#2f80ed'],
                ],
            ],
            'contentStatus' => [
                'id' => 'content-status',
                'type' => 'donut',
                'title' => 'Content Status',
                'summary' => null,
                'centerValue' => $this->formatNumber($publishedCount + $draftCount + $archivedCount),
                'centerLabel' => 'Total',
                'segments' => [
                    [
                        'label' => 'Published',
                        'value' => $publishedCount,
                        'percent' => round(($publishedCount / $statusTotal) * 100),
                        'color' => '#16b978',
                    ],
                    [
                        'label' => 'Draft',
                        'value' => $draftCount,
                        'percent' => round(($draftCount / $statusTotal) * 100),
                        'color' => '#f59e0b',
                    ],
                    [
                        'label' => 'Archived',
                        'value' => $archivedCount,
                        'percent' => round(($archivedCount / $statusTotal) * 100),
                        'color' => '#94a3b8',
                    ],
                ],
            ],
            'engagement' => [
                'id' => 'engagement',
                'type' => 'multi-line',
                'title' => 'Engagement Overview',
                'summary' => null,
                'max' => $this->niceChartMax(array_merge($engagementSeries['edits'], $engagementSeries['mediaUsage'])),
                'labels' => $engagementSeries['labels'],
                'datasets' => [
                    ['label' => 'Edits', 'color' => '#6c3cff', 'values' => $engagementSeries['edits']],
                    ['label' => 'Media Usage', 'color' => '#16b978', 'values' => $engagementSeries['mediaUsage']],
                ],
            ],
        ];
    }

    private function operations(
        ?Agency $agency,
        int $pageCount,
        int $publishedPageCount,
        int $draftPageCount,
        int $mediaCount,
        int $usedMediaCount,
        int $userCount,
        int $roleCount,
        int $permissionCount,
        int $themeCount
    ): array {
        $unusedMediaCount = max($mediaCount - $usedMediaCount, 0);

        return [
            [
                'label' => 'Pages',
                'value' => $this->formatNumber($pageCount),
                'icon' => 'document-text',
                'status' => $draftPageCount > 0 ? 'Needs review' : 'Healthy',
                'tone' => $draftPageCount > 0 ? 'amber' : 'emerald',
                'detail' => "{$publishedPageCount} published, {$draftPageCount} drafts",
                'meta' => "{$draftPageCount} drafts",
                'href' => route('pages.index'),
                'action' => 'Review pages',
            ],
            [
                'label' => 'Media Library',
                'value' => $this->formatNumber($mediaCount),
                'icon' => 'photo',
                'status' => $unusedMediaCount > 0 ? 'Cleanup available' : 'Organized',
                'tone' => $unusedMediaCount > 0 ? 'sky' : 'emerald',
                'detail' => "{$usedMediaCount} linked, {$unusedMediaCount} unused",
                'meta' => null,
                'href' => route('media.index'),
                'action' => 'Open library',
            ],
            [
                'label' => 'Themes',
                'value' => $this->formatNumber($themeCount),
                'icon' => 'swatch',
                'status' => $agency?->theme ? 'Active theme' : 'No active theme',
                'tone' => $agency?->theme ? 'emerald' : 'amber',
                'detail' => $agency?->theme?->name ?? 'Select or customize a theme',
                'meta' => null,
                'href' => route('themes.index'),
                'action' => 'Manage themes',
            ],
            [
                'label' => 'Users',
                'value' => $this->formatNumber($userCount),
                'icon' => 'users',
                'status' => 'Team access',
                'tone' => 'indigo',
                'detail' => "{$roleCount} roles configured",
                'meta' => 'active',
                'href' => route('users.index'),
                'action' => 'Review users',
            ],
            [
                'label' => 'Permissions',
                'value' => $this->formatNumber($permissionCount),
                'icon' => 'shield-check',
                'status' => 'Access control',
                'tone' => 'indigo',
                'detail' => "{$permissionCount} permissions across {$roleCount} roles",
                'meta' => null,
                'href' => route('permissions.index'),
                'action' => 'Manage access',
            ],
            [
                'label' => 'Settings',
                'value' => $agency?->onboardingIsComplete() ? 'Ready' : 'Setup',
                'icon' => 'cog-6-tooth',
                'status' => $agency?->onboardingIsComplete() ? 'Operational' : 'Setup pending',
                'tone' => $agency?->onboardingIsComplete() ? 'emerald' : 'amber',
                'detail' => $agency?->activeSiteTemplate?->name ?? 'Agency profile and site setup',
                'meta' => null,
                'href' => route('onboarding.index'),
                'action' => 'Review setup',
            ],
        ];
    }

    private function quickActions(int $draftPageCount): array
    {
        return [
            [
                'label' => 'Create Page',
                'displayLabel' => 'Create New Page',
                'description' => 'Start a new CMS page in the builder.',
                'icon' => 'document-plus',
                'tone' => 'indigo',
                'href' => route('pages.create'),
                'primary' => true,
            ],
            [
                'label' => 'Upload Media',
                'displayLabel' => 'Upload Media',
                'description' => 'Add images and assets to the library.',
                'icon' => 'photo',
                'tone' => 'emerald',
                'href' => route('media.create'),
                'primary' => false,
            ],
            [
                'label' => 'Review Users',
                'displayLabel' => 'Review Users',
                'description' => 'Audit team accounts and roles.',
                'icon' => 'users',
                'tone' => 'blue',
                'href' => route('users.index'),
                'primary' => false,
            ],
            [
                'label' => 'Publish Changes',
                'displayLabel' => 'Publish Changes',
                'description' => $draftPageCount > 0 ? "{$draftPageCount} drafts ready." : 'No draft backlog.',
                'icon' => 'rocket-launch',
                'tone' => 'amber',
                'href' => route('pages.index', ['filter' => Page::STATUS_DRAFT]),
                'primary' => false,
            ],
            [
                'label' => 'Create/Customize Theme',
                'displayLabel' => 'Customize Theme',
                'description' => 'Tune brand tokens and styling.',
                'icon' => 'swatch',
                'tone' => 'pink',
                'href' => route('themes.customize'),
                'primary' => false,
            ],
            [
                'label' => 'Manage Permissions',
                'displayLabel' => 'Manage Permissions',
                'description' => 'Review roles and access rules.',
                'icon' => 'shield-check',
                'tone' => 'indigo',
                'href' => route('permissions.index'),
                'primary' => false,
            ],
        ];
    }

    private function activityFeed(User $user)
    {
        $pages = collect(Page::latest('updated_at')
            ->limit(5)
            ->get()
            ->map(fn (Page $page) => [
                'title' => 'Page updated',
                'description' => $page->title,
                'meta' => '/'.$page->slug,
                'time' => $page->updated_at,
                'tone' => $page->isPublished() ? 'emerald' : 'amber',
                'badge' => strtoupper($page->status),
                'href' => route('pages.edit', $page),
            ])
            ->all());

        $versions = collect(PageVersion::with(['page', 'author'])
            ->whereHas('page')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (PageVersion $version) => [
                'title' => 'Version saved',
                'description' => $version->page?->title ?? 'Page version',
                'meta' => trim('v'.$version->version.' '.($version->author?->name ? 'by '.$version->author->name : '')),
                'time' => $version->created_at,
                'tone' => 'indigo',
                'badge' => 'VERSION',
                'href' => $version->page ? route('pages.versions', $version->page) : route('pages.index'),
            ])
            ->all());

        $users = collect(User::where('agency_id', $user->agency_id)
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (User $teamUser) => [
                'title' => 'User account created',
                'description' => $teamUser->name,
                'meta' => $teamUser->role?->name ?? 'Team member',
                'time' => $teamUser->created_at,
                'tone' => 'sky',
                'badge' => 'USER',
                'href' => route('users.index'),
            ])
            ->all());

        $media = collect(MediaAsset::latest()
            ->limit(3)
            ->get()
            ->map(fn (MediaAsset $asset) => [
                'title' => 'Media uploaded',
                'description' => $asset->title ?: $asset->original_name,
                'meta' => $this->formatBytes($asset->size),
                'time' => $asset->created_at,
                'tone' => 'slate',
                'badge' => 'MEDIA',
                'href' => route('media.index'),
            ])
            ->all());

        return $pages
            ->merge($versions)
            ->merge($users)
            ->merge($media)
            ->sortByDesc('time')
            ->take(8)
            ->values()
            ->all();
    }

    private function systemEvents(int $roleCount, int $permissionCount): array
    {
        return [
            [
                'label' => 'Audit logs',
                'value' => 'Ready',
                'tone' => 'slate',
                'message' => 'Structured audit storage can be connected when event tables are added.',
            ],
            [
                'label' => 'Traffic tracking',
                'value' => 'Pending',
                'tone' => 'amber',
                'message' => 'Analytics widgets are prepared for first-party traffic data.',
            ],
            [
                'label' => 'Access matrix',
                'value' => $this->formatNumber($permissionCount),
                'tone' => 'indigo',
                'message' => "{$roleCount} roles use the current permission catalog.",
            ],
        ];
    }

    private function kpi(string $label, string $value, string $icon, string $tone, int $current, int $previous, array $sparkline, array $period, string $detail): array
    {
        return [
            'label' => $label,
            'value' => $value,
            'icon' => $icon,
            'tone' => $tone,
            'trend' => $this->trend($current, $previous),
            'comparison' => 'vs '.$period['previousStart']->format('M j').' - '.$period['previousEnd']->format('M j'),
            'detail' => $detail,
            'sparkline' => $this->sparkline($sparkline),
            'showSparkline' => true,
            'href' => route('dashboard'),
        ];
    }

    private function trend(int|float $current, int|float $previous): array
    {
        if ($previous <= 0 && $current <= 0) {
            return [
                'direction' => 'neutral',
                'label' => '0%',
                'tone' => 'slate',
            ];
        }

        if ($previous <= 0) {
            return [
                'direction' => 'up',
                'label' => '+100%',
                'tone' => 'emerald',
            ];
        }

        $change = (($current - $previous) / $previous) * 100;

        return [
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'neutral'),
            'label' => ($change > 0 ? '+' : '').round($change).'%',
            'tone' => $change > 0 ? 'emerald' : ($change < 0 ? 'rose' : 'slate'),
        ];
    }

    private function sparkline(array $values): array
    {
        if ($values === []) {
            $values = [0];
        }

        $max = max($values);
        $min = min($values);
        $range = max($max - $min, 1);
        $count = count($values);
        $step = $count > 1 ? 100 / ($count - 1) : 100;

        $points = collect($values)
            ->map(function ($value, $index) use ($min, $range, $step) {
                $x = round($index * $step, 2);
                $y = round(34 - (($value - $min) / $range) * 28, 2);

                return "{$x},{$y}";
            })
            ->implode(' ');

        return [
            'points' => $points,
            'max' => $max,
        ];
    }

    private function period(): array
    {
        $now = now();
        $currentStart = $now->copy()->subDays(self::PERIOD_DAYS - 1)->startOfDay();
        $currentEnd = $now->copy()->endOfDay();
        $previousStart = $currentStart->copy()->subDays(self::PERIOD_DAYS);
        $previousEnd = $currentStart->copy()->subSecond();

        return compact('currentStart', 'currentEnd', 'previousStart', 'previousEnd');
    }

    private function dailySeries(string $modelClass, string $column, Carbon $start, Carbon $end, ?callable $scope = null): array
    {
        $query = $modelClass::query()
            ->whereNotNull($column)
            ->whereBetween($column, [$start, $end]);

        if ($scope) {
            $scope($query);
        }

        $counts = $query
            ->get([$column])
            ->groupBy(fn ($item) => $item->{$column}?->format('Y-m-d'))
            ->map->count();

        $series = [];
        $cursor = $start->copy()->startOfDay();

        while ($cursor <= $end) {
            $series[] = (int) ($counts[$cursor->format('Y-m-d')] ?? 0);
            $cursor->addDay();
        }

        return $series;
    }

    private function monthlySeries(string $modelClass, string $column, int $months, ?callable $scope = null): array
    {
        $start = now()->copy()->startOfMonth()->subMonths($months - 1);
        $end = now()->copy()->endOfMonth();
        $query = $modelClass::query()
            ->whereNotNull($column)
            ->whereBetween($column, [$start, $end]);

        if ($scope) {
            $scope($query);
        }

        $counts = $query
            ->get([$column])
            ->groupBy(fn ($item) => $item->{$column}?->format('Y-m'))
            ->map->count();

        $labels = [];
        $values = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');
            $labels[] = $cursor->format('M');
            $values[] = (int) ($counts[$key] ?? 0);
            $cursor->addMonth();
        }

        return compact('labels', 'values');
    }

    private function monthlyContentSeries(): array
    {
        $pages = $this->monthlySeries(Page::class, 'created_at', 12, fn ($query) => $query->where('status', Page::STATUS_PUBLISHED));
        $destinations = $this->monthlySeries(Destination::class, 'created_at', 12, fn ($query) => $query->where('status', Destination::STATUS_PUBLISHED));
        $offers = $this->monthlySeries(Offer::class, 'created_at', 12, fn ($query) => $query->where('status', Offer::STATUS_PUBLISHED));

        return [
            'labels' => $pages['labels'],
            'pages' => $pages['values'],
            'destinations' => $destinations['values'],
            'offers' => $offers['values'],
        ];
    }

    private function monthlyEngagementSeries(): array
    {
        $versions = $this->monthlySeries(PageVersion::class, 'created_at', 12, fn ($query) => $query->whereHas('page'));
        $media = $this->monthlySeries(MediaAsset::class, 'created_at', 12);

        return [
            'labels' => $versions['labels'],
            'edits' => $versions['values'],
            'mediaUsage' => $media['values'],
        ];
    }

    private function usedMediaQuery()
    {
        return MediaAsset::query()
            ->where(fn ($query) => $query->whereHas('destinations')->orWhereHas('offers'));
    }

    private function formatNumber(int|float $value): string
    {
        return number_format($value);
    }

    private function niceChartMax(array $values): int
    {
        $max = max(1, ...array_map(fn ($value) => (int) $value, $values));

        if ($max <= 3) {
            return 3;
        }

        if ($max <= 6) {
            return 6;
        }

        if ($max <= 10) {
            return 10;
        }

        if ($max <= 20) {
            return 20;
        }

        return (int) ceil($max / 10) * 10;
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
