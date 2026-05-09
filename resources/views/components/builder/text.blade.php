<p

    data-type="text"

    data-index="{{ $index ?? 0 }}"

    contenteditable="true"

    data-field="text"

    class="outline-none"

    style="
        color:
            {{ $props['color'] ?? '#374151' }};

        font-size:
            {{ ($props['fontSize'] ?? 16) . 'px' }};

        font-weight:
            {{ $props['fontWeight'] ?? 400 }};

        line-height:
            {{ $props['lineHeight'] ?? 1.6 }};

        text-align:
            {{ $props['align'] ?? 'left' }};

        padding:
            {{ ($props['padding'] ?? 16) . 'px' }};
    "

>

    {{ $props['text'] ?? 'Text here' }}

</p>