<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\MediaAsset;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class DemoTravelContentService
{
    public function preview(User $user): array
    {
        $state = $this->contentState($user);
        $destinations = collect($this->catalog('destinations'))
            ->map(fn (array $item) => [
                ...$item,
                'exists' => $this->destinationBySlug($user, $item['slug']) !== null,
            ])
            ->values()
            ->all();
        $offers = collect($this->catalog('offers'))
            ->map(fn (array $item) => [
                ...$item,
                'exists' => $this->offerBySlug($user, $item['slug']) !== null,
            ])
            ->values()
            ->all();

        return [
            'state' => $state,
            'minimums' => $this->minimums(),
            'needs_demo_content' => $this->needsDemoContent($user),
            'has_missing_catalog_items' => collect($destinations)->contains(fn ($item) => ! $item['exists'])
                || collect($offers)->contains(fn ($item) => ! $item['exists']),
            'destinations' => $destinations,
            'offers' => $offers,
        ];
    }

    public function apply(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $result = [
                'media_created' => 0,
                'media_reused' => 0,
                'destinations_created' => 0,
                'destinations_skipped' => 0,
                'offers_created' => 0,
                'offers_skipped' => 0,
            ];

            $media = [];

            foreach ($this->catalog('media') as $key => $asset) {
                [$mediaAsset, $created] = $this->ensureMedia($user, $key, $asset);
                $media[$key] = $mediaAsset;
                $result[$created ? 'media_created' : 'media_reused']++;
            }

            foreach ($this->catalog('destinations') as $item) {
                $existing = $this->destinationBySlug($user, $item['slug']);

                if ($existing !== null) {
                    $result['destinations_skipped']++;
                    continue;
                }

                $destination = Destination::withoutGlobalScopes()->create([
                    'agency_id' => $user->agency_id,
                    'name' => $item['name'],
                    'slug' => $item['slug'],
                    'country' => $item['country'],
                    'description' => $item['description'],
                    'is_featured' => $item['is_featured'],
                    'status' => Destination::STATUS_PUBLISHED,
                ]);

                if (! empty($item['media']) && isset($media[$item['media']])) {
                    $destination->media()->attach($media[$item['media']]->id, ['sort_order' => 0]);
                }

                $result['destinations_created']++;
            }

            foreach ($this->catalog('offers') as $item) {
                $existing = $this->offerBySlug($user, $item['slug']);

                if ($existing !== null) {
                    $result['offers_skipped']++;
                    continue;
                }

                $destinationSlug = $item['destination_slug'] ?? $item['destination'] ?? null;

                if (! is_string($destinationSlug) || $destinationSlug === '') {
                    throw new InvalidArgumentException("Missing destination slug for demo offer [{$item['slug']}].");
                }

                $destination = $this->destinationBySlug($user, $destinationSlug);

                if ($destination === null) {
                    throw new InvalidArgumentException("Missing demo destination [{$destinationSlug}].");
                }

                Offer::withoutGlobalScopes()->create([
                    'agency_id' => $user->agency_id,
                    'destination_id' => $destination->id,
                    'media_asset_id' => isset($media[$item['media']]) ? $media[$item['media']]->id : null,
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'duration_days' => $item['duration_days'],
                    'is_special' => $item['is_special'],
                    'status' => Offer::STATUS_PUBLISHED,
                ]);

                $result['offers_created']++;
            }

            DashboardStatsService::forgetFor($user);

            return [
                ...$result,
                'state' => $this->contentState($user),
            ];
        });
    }

    public function needsDemoContent(User $user): bool
    {
        $state = $this->contentState($user);
        $minimums = $this->minimums();

        return $state['published_destinations'] < $minimums['published_destinations']
            || $state['featured_destinations'] < $minimums['featured_destinations']
            || $state['published_offers'] < $minimums['published_offers']
            || $state['special_offers'] < $minimums['special_offers'];
    }

    public function contentState(User $user): array
    {
        return [
            'published_destinations' => Destination::withoutGlobalScopes()
                ->where('agency_id', $user->agency_id)
                ->published()
                ->count(),
            'featured_destinations' => Destination::withoutGlobalScopes()
                ->where('agency_id', $user->agency_id)
                ->published()
                ->featured()
                ->count(),
            'published_offers' => Offer::withoutGlobalScopes()
                ->where('agency_id', $user->agency_id)
                ->published()
                ->count(),
            'special_offers' => Offer::withoutGlobalScopes()
                ->where('agency_id', $user->agency_id)
                ->published()
                ->special()
                ->count(),
        ];
    }

    private function ensureMedia(User $user, string $key, array $asset): array
    {
        $filename = "{$key}.svg";
        $path = "agencies/{$user->agency_id}/media/demo/{$filename}";
        $existing = MediaAsset::withoutGlobalScopes()
            ->where('agency_id', $user->agency_id)
            ->where('path', $path)
            ->first();

        if ($existing !== null) {
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $this->assetContents($key, $asset));
            }

            return [$existing, false];
        }

        $contents = $this->assetContents($key, $asset);
        Storage::disk('public')->put($path, $contents);

        return [
            MediaAsset::withoutGlobalScopes()->create([
                'agency_id' => $user->agency_id,
                'filename' => $filename,
                'original_name' => $filename,
                'path' => $path,
                'mime_type' => 'image/svg+xml',
                'size' => strlen($contents),
                'title' => $asset['title'],
                'alt_text' => $asset['alt_text'],
            ]),
            true,
        ];
    }

    private function destinationBySlug(User $user, string $slug): ?Destination
    {
        return Destination::withoutGlobalScopes()
            ->where('agency_id', $user->agency_id)
            ->where('slug', $slug)
            ->first();
    }

    private function offerBySlug(User $user, string $slug): ?Offer
    {
        return Offer::withoutGlobalScopes()
            ->where('agency_id', $user->agency_id)
            ->where('slug', $slug)
            ->first();
    }

    private function assetContents(string $key, array $asset): string
    {
        $path = resource_path("demo/travel/{$key}.svg");

        if (is_file($path)) {
            return (string) file_get_contents($path);
        }

        $title = htmlspecialchars((string) ($asset['title'] ?? Str::headline($key)), ENT_QUOTES, 'UTF-8');
        $primary = htmlspecialchars((string) ($asset['colors'][0] ?? '#2563eb'), ENT_QUOTES, 'UTF-8');
        $secondary = htmlspecialchars((string) ($asset['colors'][1] ?? '#0f172a'), ENT_QUOTES, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="760" viewBox="0 0 1200 760" role="img" aria-label="{$title}">
  <defs>
    <linearGradient id="g" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0" stop-color="{$primary}"/>
      <stop offset="1" stop-color="{$secondary}"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="760" fill="url(#g)"/>
  <circle cx="950" cy="150" r="170" fill="rgba(255,255,255,.18)"/>
  <circle cx="210" cy="610" r="220" fill="rgba(255,255,255,.12)"/>
  <text x="84" y="392" fill="#fff" font-family="Arial, Helvetica, sans-serif" font-size="72" font-weight="700">{$title}</text>
</svg>
SVG;
    }

    private function catalog(string $key): array
    {
        return config("travel-demo.{$key}", []);
    }

    private function minimums(): array
    {
        return [
            'published_destinations' => (int) config('travel-demo.minimums.published_destinations', 3),
            'featured_destinations' => (int) config('travel-demo.minimums.featured_destinations', 3),
            'published_offers' => (int) config('travel-demo.minimums.published_offers', 3),
            'special_offers' => (int) config('travel-demo.minimums.special_offers', 3),
        ];
    }
}
