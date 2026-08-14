@php
    $columns = is_numeric($props['columns'] ?? null) ? max(1, min(6, (int) $props['columns'])) : 3;
    $gap = is_numeric($props['gap'] ?? null) ? max(0, min(64, (int) $props['gap'])) : 16;
    $height = is_numeric($props['height'] ?? null) ? max(80, min(720, (int) $props['height'])) : 220;
    $radius = is_numeric($props['borderRadius'] ?? null) ? max(0, min(80, (int) $props['borderRadius'])) : 12;

    $safeImage = function (string $value): bool {
        $value = trim($value);

        if ($value === '' || preg_match('/[\x00-\x1F\x7F\s\\\\<>"\']/', $value)) {
            return false;
        }

        if (preg_match('/^(?:javascript|vbscript|data):/i', $value) || str_starts_with($value, '//')) {
            return false;
        }

        return preg_match('/^https?:\/\//i', $value) === 1
            || str_starts_with($value, '/')
            || str_starts_with(strtolower($value), 'storage/');
    };

    $images = collect(preg_split('/\r\n|\r|\n/', (string) ($props['images'] ?? '')))
        ->map(function ($line) use ($safeImage) {
            $parts = array_map('trim', explode('|', $line, 2));
            $url = $parts[0] ?? '';

            if (! $safeImage($url)) {
                return null;
            }

            return [
                'url' => $url,
                'alt' => $parts[1] ?? 'Gallery image',
            ];
        })
        ->filter()
        ->values();
@endphp

<div
    @if($isEditor)
        data-type="gallery"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} p-4 transition-all duration-150"
>
    @if($images->isNotEmpty())
        <div
            class="site-gallery-grid grid"
            style="--site-gallery-columns: {{ $columns }}; gap: {{ $gap }}px;"
        >
            @foreach($images as $image)
                <img
                    src="{{ $image['url'] }}"
                    alt="{{ $image['alt'] }}"
                    loading="lazy"
                    decoding="async"
                    class="w-full object-cover transition duration-300 hover:scale-[1.015]"
                    style="height: {{ $height }}px; border-radius: {{ $radius }}px;"
                >
            @endforeach
        </div>
    @elseif($isEditor)
        <div
            class="site-gallery-grid grid"
            style="--site-gallery-columns: {{ $columns }}; gap: {{ $gap }}px;"
        >
            @for($i = 0; $i < min($columns, 3); $i++)
                <div
                    class="flex items-center justify-center border border-dashed border-slate-300 bg-slate-50 text-sm text-slate-400"
                    style="height: {{ $height }}px; border-radius: {{ $radius }}px;"
                >
                    Gallery image
                </div>
            @endfor
        </div>
    @endif
</div>
