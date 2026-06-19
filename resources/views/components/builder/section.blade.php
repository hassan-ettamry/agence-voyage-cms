@php
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

    $backgroundMode = $props['backgroundMode'] ?? 'color';
    $backgroundColor = $backgroundMode === 'none'
        ? 'transparent'
        : ($props['backgroundColor'] ?? 'var(--site-background, #ffffff)');
    $backgroundImage = $backgroundMode === 'image' && !empty($props['backgroundImage'])
        ? "url('".e($props['backgroundImage'])."')"
        : null;
    $customClass = trim((string) ($props['customClass'] ?? ''));
@endphp

<div
    @if($isEditor)
        data-type="section"
        data-node-id="{{ $nodeId }}"
        data-dropzone="true"
        draggable="true"
        data-drag-action="reorder"
    @endif

    class="
        w-full
        mx-auto
        relative
        {{ $isEditor ? 'min-h-[220px] builder-node' : '' }}
        transition-all
        duration-150
        {{ $customClass }}
    "

    style="
        @if($css('display'))
            display: {{ $css('display') }};
        @endif

        @if($pixel('width'))
            width: {{ $pixel('width') }};
        @endif

        @if($pixel('height'))
            height: {{ $pixel('height') }};
        @endif

        min-height:
            {{ $pixel('minHeight', 220) }};

        margin-top:
            {{ $pixel('marginTop', 0) }};

        margin-bottom:
            {{ $pixel('marginBottom', 0) }};

        background-color:
            {{ $backgroundColor }};

        @if($backgroundImage)
            background-image: {{ $backgroundImage }};
            background-size: cover;
            background-position: center;
        @endif

        padding-top:
            {{ ($props['paddingTop'] ?? 60) . 'px' }};

        padding-bottom:
            {{ ($props['paddingBottom'] ?? 60) . 'px' }};

        padding-left:
            {{ ($props['paddingLeft'] ?? 20) . 'px' }};

        padding-right:
            {{ ($props['paddingRight'] ?? 20) . 'px' }};

        border-style:
            {{ $props['borderStyle'] ?? 'solid' }};

        border-width:
            {{ $pixel('borderWidth', 0) }};

        border-color:
            {{ $props['borderColor'] ?? 'var(--site-border, #e2e8f0)' }};

        border-radius:
            {{ $pixel('borderRadius', 0) }};

        @if($css('boxShadow'))
            box-shadow: {{ $css('boxShadow') }};
        @endif

        @if($css('transform'))
            transform: {{ $css('transform') }};
        @endif

        color:
            {{ $props['textColor'] ?? 'var(--site-text, #0f172a)' }};

        @if($pixel('fontSize'))
            font-size: {{ $pixel('fontSize') }};
        @endif

        @if($css('fontWeight'))
            font-weight: {{ $css('fontWeight') }};
        @endif

        overflow:
            {{ $props['overflow'] ?? 'hidden' }};

        visibility:
            {{ $props['visibility'] ?? 'visible' }};

        opacity:
            {{ (($props['opacity'] ?? 100) / 100) }};

        position:
            {{ $props['position'] ?? 'relative' }};

        @if($pixel('top'))
            top: {{ $pixel('top') }};
        @endif

        @if($pixel('left'))
            left: {{ $pixel('left') }};
        @endif

        transition-duration:
            {{ ($props['transitionDuration'] ?? 150) . 'ms' }};
    "
>

    @if($backgroundMode === 'video' && !empty($props['backgroundVideo']))
        <video
            class="absolute inset-0 h-full w-full object-cover"
            src="{{ $props['backgroundVideo'] }}"
            autoplay
            muted
            loop
            playsinline
        ></video>
    @endif

    <div
        class="relative z-10 mx-auto"
        style="
            max-width:
                {{ $pixel('maxWidth', 1200) }};
        "
    >

        @if(!empty($props['title']))

            <h2
                @if($isEditor)
                    contenteditable="true"
                    data-field="title"
                @endif

                class="
                    text-3xl
                    font-bold
                    mb-6
                    {{ $isEditor ? 'outline-none' : '' }}
                "
            >
                {{ $props['title'] }}
            </h2>

        @endif

        @if(!empty($children))

            {!! $children !!}

        @elseif($isEditor)

            <div
                class="
                    mx-auto
                    flex
                    min-h-[104px]
                    max-w-5xl
                    items-center
                    justify-center
                    border-2
                    border-dashed
                    border-slate-700
                    text-sm
                    font-medium
                    text-slate-400
                "
            >
                Drop Widget
            </div>

        @endif

    </div>

</div>
