@include('components.builder.destination-grid', [
    'props' => array_merge($props, ['source' => 'featured', 'title' => $props['title'] ?? 'Featured Destinations']),
    'children' => $children,
    'type' => $type,
    'nodeId' => $nodeId,
    'mode' => $mode,
    'isEditor' => $isEditor,
    'isPreview' => $isPreview,
    'isLive' => $isLive,
])
