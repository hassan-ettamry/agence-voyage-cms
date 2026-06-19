@include('components.builder.offer-grid', [
    'props' => array_merge($props, ['source' => 'special', 'title' => $props['title'] ?? 'Special Offers']),
    'children' => $children,
    'type' => $type,
    'nodeId' => $nodeId,
    'mode' => $mode,
    'isEditor' => $isEditor,
    'isPreview' => $isPreview,
    'isLive' => $isLive,
])
