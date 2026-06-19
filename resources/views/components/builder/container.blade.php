@php
    $displayOptions = ['block', 'flex', 'grid', 'none'];
    $flexDirections = ['row', 'row-reverse', 'column', 'column-reverse'];
    $contentOptions = ['flex-start', 'center', 'flex-end', 'space-between', 'space-around', 'space-evenly', 'start', 'end'];
    $alignOptions = ['stretch', 'flex-start', 'center', 'flex-end', 'baseline', 'start', 'end'];

    $rawDisplay = $props['display'] ?? 'block';
    $rawFlexDirection = $props['flexDirection'] ?? 'row';
    $rawJustifyContent = $props['justifyContent'] ?? 'flex-start';
    $rawAlignItems = $props['alignItems'] ?? 'stretch';

    $display = in_array($rawDisplay, $displayOptions, true)
        ? $rawDisplay
        : 'block';

    $flexDirection = in_array($rawFlexDirection, $flexDirections, true)
        ? $rawFlexDirection
        : 'row';

    $justifyContent = in_array($rawJustifyContent, $contentOptions, true)
        ? $rawJustifyContent
        : 'flex-start';

    $alignItems = in_array($rawAlignItems, $alignOptions, true)
        ? $rawAlignItems
        : 'stretch';

    $gridColumns = is_numeric($props['gridColumns'] ?? null)
        ? max(1, min(12, (int) $props['gridColumns']))
        : 12;

    $gridSpan = is_numeric($props['gridSpan'] ?? null)
        ? max(1, min(12, (int) $props['gridSpan']))
        : 12;

    $parentDisplay = $parentProps['display'] ?? 'block';
    $isGridItem = ($parentType ?? null) === 'container' && $parentDisplay === 'grid';
    $containerRole = $props['containerRole'] ?? 'group';
    $isCard = $containerRole === 'card';
    $customClass = trim((string) ($props['customClass'] ?? ''));

    $pixel = function (string $key, mixed $default = null) use ($props) {
        $value = $props[$key] ?? $default;

        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? "{$value}px" : $value;
    };

    $css = function (string $key, mixed $default = null) use ($props) {
        $value = $props[$key] ?? $default;

        return $value === null || $value === '' ? null : $value;
    };
@endphp

<div

    @if($isEditor)

    data-type="container"

    data-container-role="{{ $containerRole }}"

    data-node-id="{{ $nodeId }}"

    data-dropzone="true"

    draggable="true"

    data-drag-action="reorder"

    @endif

    class="
        w-full
        mx-auto
        relative
        {{ $isEditor ? 'min-h-[120px] builder-node' : '' }}
        transition-all
        duration-150
        {{ $customClass }}
    "

    style="
        @if($isGridItem)
            grid-column: span {{ $gridSpan }} / span {{ $gridSpan }};
        @endif

        display:
            {{ $display }};

        @if($pixel('width'))
            width:
                {{ $pixel('width') }};
        @endif

        @if($pixel('height'))
            height:
                {{ $pixel('height') }};
        @endif

        @if($pixel('minHeight'))
            min-height:
                {{ $pixel('minHeight') }};
        @endif

        margin-top:
            {{ $pixel('marginTop', 0) }};

        margin-bottom:
            {{ $pixel('marginBottom', 0) }};

        @if($display === 'grid')
            grid-template-columns:
                repeat({{ $gridColumns }}, minmax(0, 1fr));
        @endif

        @if($display === 'flex')
            flex-direction:
                {{ $flexDirection }};
        @endif

        @if(in_array($display, ['flex', 'grid'], true))
            gap:
                {{ $pixel('gap', 20) }};

            justify-content:
                {{ $justifyContent }};

            align-items:
                {{ $alignItems }};
        @endif

        max-width:
            {{ $pixel('maxWidth', 1200) }};

        padding-top:
            {{ $pixel('paddingTop', 0) }};

        padding-bottom:
            {{ $pixel('paddingBottom', 0) }};

        padding-left:
            {{ $pixel('paddingLeft', 0) }};

        padding-right:
            {{ $pixel('paddingRight', 0) }};

        background-color:
            {{ $props['backgroundColor'] ?? ($isCard ? 'var(--site-surface, #f8fafc)' : 'transparent') }};

        border-style:
            {{ $props['borderStyle'] ?? 'solid' }};

        border-width:
            {{ $pixel('borderWidth', $isCard ? 1 : 0) }};

        border-color:
            {{ $props['borderColor'] ?? 'var(--site-border, #e2e8f0)' }};

        border-radius:
            {{ $props['borderRadius'] ?? ($isCard ? 'var(--site-radius, 14px)' : 0) }};

        @if($css('boxShadow') || $isCard)
            box-shadow:
                {{ $css('boxShadow', 'var(--site-shadow, none)') }};
        @endif

        @if($css('transform'))
            transform:
                {{ $css('transform') }};
        @endif

        color:
            {{ $props['textColor'] ?? 'var(--site-text, #0f172a)' }};

        @if($pixel('fontSize'))
            font-size:
                {{ $pixel('fontSize') }};
        @endif

        @if(!empty($props['fontWeight']))
            font-weight:
                {{ $props['fontWeight'] }};
        @endif

        overflow:
            {{ $props['overflow'] ?? 'visible' }};

        visibility:
            {{ $props['visibility'] ?? 'visible' }};

        opacity:
            {{ (($props['opacity'] ?? 100) / 100) }};

        position:
            {{ $props['position'] ?? 'relative' }};

        @if($pixel('top'))
            top:
                {{ $pixel('top') }};
        @endif

        @if($pixel('left'))
            left:
                {{ $pixel('left') }};
        @endif

        transition-duration:
            {{ ($props['transitionDuration'] ?? 150) . 'ms' }};
    "
>

    @if(!empty($children))

        {!! $children !!}

    @elseif($isEditor)

        <div
            class="
                min-h-[120px]
                border
                border-dashed
                border-gray-300
                rounded-lg
                flex
                items-center
                justify-center
                text-sm
                text-gray-400
            "
        >

            Container

        </div>

    @endif

</div>
