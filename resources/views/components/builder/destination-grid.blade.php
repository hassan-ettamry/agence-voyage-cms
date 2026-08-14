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
        <h2 class="site-heading mb-8 text-3xl font-bold" style="color: var(--site-text, #111827);">
            {{ $props['title'] }}
        </h2>
    @endif

    @if($destinations->isEmpty())
        <div class="site-card border-dashed p-8 text-center text-sm" style="color: var(--site-muted);">
            {{ $isEditor ? 'No published destinations match this source.' : 'New destinations are coming soon.' }}
        </div>
    @endif

    <div class="site-card-grid grid" style="--site-card-columns: {{ $columns }}; gap: {{ $gap }}px;">
        @foreach($destinations as $destination)
            @php($cover = $destination->media->first())
            <a
                href="{{ $isEditor ? '#' : app(\App\Services\PublicSiteUrl::class)->destination($destination->agency, $destination) }}"
                @if($isEditor) onclick="return false" @endif
                class="site-card group overflow-hidden no-underline"
                style="background-color: var(--site-surface, #ffffff); border-color: var(--site-border, #f3f4f6); border-radius: var(--site-radius, 14px); box-shadow: var(--site-shadow, none);"
            >
                @if($showImage)
                    <div class="aspect-video" style="background-color: color-mix(in srgb, var(--site-primary, #2563eb) 12%, white);">
                        @if($cover)
                            <img src="{{ $cover->url }}" alt="{{ $cover->alt_text ?? $destination->name }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
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
                        <h3 class="site-heading mt-2 text-xl font-semibold" style="color: var(--site-text, #111827);">
                            {{ $destination->name }}
                        </h3>
                    @endif

                    @if($showDescription)
                        <p class="mt-2 line-clamp-2 text-sm" style="color: var(--site-muted, #6b7280);">
                            {{ $destination->description }}
                        </p>
                    @endif

                    @if($showCta)
                        <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold" style="color: var(--site-primary, #2563eb);">
                            {{ $buttonText }} <span aria-hidden="true">&rarr;</span>
                        </span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
