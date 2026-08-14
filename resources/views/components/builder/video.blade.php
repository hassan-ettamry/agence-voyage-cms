@php
    $rawUrl = trim((string) ($props['url'] ?? ''));
    $title = $props['title'] ?? 'Video';
    $height = is_numeric($props['height'] ?? null) ? max(160, min(900, (int) $props['height'])) : 360;
    $radius = is_numeric($props['borderRadius'] ?? null) ? max(0, min(80, (int) $props['borderRadius'])) : 12;
    $controls = ($props['controls'] ?? 'yes') === 'yes';
    $autoplay = ($props['autoplay'] ?? 'no') === 'yes';

    $isRemote = preg_match('/^https?:\/\//i', $rawUrl) === 1;
    $embedUrl = null;
    $directUrl = null;

    if ($isRemote && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{6,})/', $rawUrl, $matches)) {
        $embedUrl = 'https://www.youtube.com/embed/'.$matches[1].($autoplay ? '?autoplay=1&mute=1' : '');
    } elseif ($isRemote && preg_match('/vimeo\.com\/(?:video\/)?(\d+)/', $rawUrl, $matches)) {
        $embedUrl = 'https://player.vimeo.com/video/'.$matches[1].($autoplay ? '?autoplay=1&muted=1' : '');
    } elseif ($isRemote && preg_match('/\.(mp4|webm|ogg)(?:\?.*)?$/i', $rawUrl)) {
        $directUrl = $rawUrl;
    }
@endphp

<div
    @if($isEditor)
        data-type="video"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} p-4 transition-all duration-150"
>
    @if($embedUrl)
        <iframe
            src="{{ $embedUrl }}"
            title="{{ $title }}"
            loading="lazy"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            class="block aspect-video w-full border-0 bg-slate-100"
            style="max-height: {{ $height }}px; border-radius: {{ $radius }}px;"
        ></iframe>
    @elseif($directUrl)
        <video
            src="{{ $directUrl }}"
            class="block aspect-video w-full bg-slate-100 object-cover"
            style="max-height: {{ $height }}px; border-radius: {{ $radius }}px;"
            @if($controls) controls @endif
            @if($autoplay) autoplay muted loop playsinline @endif
        ></video>
    @elseif($isEditor)
        <div
            class="flex w-full items-center justify-center border border-dashed border-slate-300 bg-slate-50 text-sm text-slate-400"
            style="height: {{ $height }}px; border-radius: {{ $radius }}px;"
        >
            Add a YouTube, Vimeo, MP4, WebM, or OGG URL
        </div>
    @endif
</div>
