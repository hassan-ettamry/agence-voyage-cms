@php
    $props = is_array($props ?? null) ? $props : [];

    $show = static function (string $key, bool $default = true) use ($props): bool {
        if (!array_key_exists($key, $props)) {
            return $default;
        }

        return !in_array($props[$key], ['no', 'false', false, 0, '0'], true);
    };

    $limit = max(1, min((int) ($props['limit'] ?? 6), 12));
    $columns = max(1, min((int) ($props['columns'] ?? 3), 4));
    $gap = max(8, min((int) ($props['gap'] ?? 24), 64));
    $source = $props['source'] ?? 'latest';
    $sort = $props['sort'] ?? 'latest';

    $query = \App\Models\Destination::published()->with('media');

    if ($source === 'featured') {
        $query->featured();
    }

    match ($sort) {
        'oldest' => $query->oldest(),
        'name' => $query->orderBy('name'),
        default => $query->latest(),
    };

    $destinations = $query->limit($limit)->get();

    $showImage = $show('showImage');
    $showTitle = $show('showTitle');
    $showDescription = $show('showDescription');
    $showMeta = $show('showMeta');
    $showCta = $show('showCta');
    $buttonText = $props['buttonText'] ?? 'View destination';
    $sectionPadding = max(0, min((int) ($props['padding'] ?? 40), 120));
    $marginTop = is_numeric($props['marginTop'] ?? null) ? (int) $props['marginTop'] : 0;
    $marginBottom = is_numeric($props['marginBottom'] ?? null) ? (int) $props['marginBottom'] : 0;
@endphp

<section
    data-node-id="{{ $nodeId }}"
    data-type="{{ $type }}"
    style="padding-top: {{ $sectionPadding }}px; padding-bottom: {{ $sectionPadding }}px; margin-top: {{ $marginTop }}px; margin-bottom: {{ $marginBottom }}px;"
>
    @if(!empty($props['title']))
        <h2 class="mb-6 text-2xl font-bold" style="color: var(--site-text, #111827); font-family: var(--site-heading-font, ui-sans-serif, system-ui, sans-serif);">
            {{ $props['title'] }}
        </h2>
    @endif

    @if($destinations->isEmpty() && $isEditor)
        <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-400">
            No published destinations match this source.
        </div>
    @endif

    <div class="grid" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr)); gap: {{ $gap }}px;">
        @foreach($destinations as $destination)
            @php($cover = $destination->media->first())
            <a
                href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->destination($destination->agency, $destination) }}"
                @if($isEditor) onclick="return false" @endif
                class="overflow-hidden border transition hover:-translate-y-0.5"
                style="background-color: var(--site-surface, #ffffff); border-color: var(--site-border, #f3f4f6); border-radius: var(--site-radius, 14px); box-shadow: var(--site-shadow, none);"
            >
                @if($showImage)
                    <div class="aspect-video" style="background-color: color-mix(in srgb, var(--site-primary, #2563eb) 12%, white);">
                        @if($cover)
                            <img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" class="h-full w-full object-cover">
                        @elseif($isEditor)
                            <div class="flex h-full items-center justify-center text-sm" style="color: var(--site-muted, #94a3b8);">
                                Destination image
                            </div>
                        @endif
                    </div>
                @endif

                <div class="p-5">
                    @if($showMeta && $destination->country)
                        <div class="text-xs font-semibold uppercase" style="color: var(--site-primary, #6366f1);">
                            {{ $destination->country }}
                        </div>
                    @endif

                    @if($showTitle)
                        <h3 class="mt-1 text-lg font-semibold" style="color: var(--site-text, #111827); font-family: var(--site-heading-font, ui-sans-serif, system-ui, sans-serif);">
                            {{ $destination->name }}
                        </h3>
                    @endif

                    @if($showDescription)
                        <p class="mt-2 line-clamp-2 text-sm" style="color: var(--site-muted, #6b7280);">
                            {{ $destination->description }}
                        </p>
                    @endif

                    @if($showCta)
                        <span class="mt-4 inline-flex text-sm font-semibold" style="color: var(--site-primary, #2563eb);">
                            {{ $buttonText }}
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
