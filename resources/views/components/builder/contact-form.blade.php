@php
    $configuredAction = trim((string) ($props['actionUrl'] ?? ''));
    $embedded = ($props['presentation'] ?? 'standalone') === 'embedded';
@endphp

<div
    @if($isEditor)
        data-type="contact-form"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} {{ $embedded ? 'h-full' : 'p-4' }} transition-all duration-150"
    style="color: {{ $props['textColor'] ?? 'var(--site-text, #111827)' }};"
>
    <form
        action="#"
        method="post"
        class="site-card {{ $embedded ? 'h-full p-7 sm:p-9' : 'mx-auto max-w-2xl p-6 sm:p-8' }}"
        style="
            background-color: var(--site-surface, #ffffff);
            border-color: var(--site-border, #e2e8f0);
            border-radius: var(--site-radius, 14px);
            box-shadow: var(--site-shadow, none);
        "
        data-configured-action="{{ $configuredAction }}"
        onsubmit="return false"
    >
        <div class="mb-6">
            <h3
                @if($isEditor)
                    contenteditable="true"
                    data-field="title"
                @endif
                class="{{ $isEditor ? 'outline-none' : '' }} site-heading text-3xl font-bold"
            >
                {{ $props['title'] ?? 'Contact us' }}
            </h3>

            <p
                @if($isEditor)
                    contenteditable="true"
                    data-field="subtitle"
                @endif
                class="{{ $isEditor ? 'outline-none' : '' }} mt-2 text-sm leading-6"
                style="color: var(--site-muted, #64748b);"
            >
                {{ $props['subtitle'] ?? 'Send us a message and we will reply soon.' }}
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm font-medium">
                <span>{{ $props['nameLabel'] ?? 'Name' }}</span>
                <input
                    type="text"
                    name="name"
                    class="mt-2 min-h-12 w-full border bg-transparent px-4 py-3 text-sm focus:outline-none"
                    style="border-color: var(--site-border, #cbd5e1); border-radius: var(--site-radius, 14px);"
                    placeholder="{{ $props['nameLabel'] ?? 'Name' }}"
                    @if($isEditor) tabindex="-1" @endif
                >
            </label>

            <label class="block text-sm font-medium">
                <span>{{ $props['emailLabel'] ?? 'Email' }}</span>
                <input
                    type="email"
                    name="email"
                    class="mt-2 min-h-12 w-full border bg-transparent px-4 py-3 text-sm focus:outline-none"
                    style="border-color: var(--site-border, #cbd5e1); border-radius: var(--site-radius, 14px);"
                    placeholder="{{ $props['emailLabel'] ?? 'Email' }}"
                    @if($isEditor) tabindex="-1" @endif
                >
            </label>
        </div>

        <label class="mt-4 block text-sm font-medium">
            <span>{{ $props['messageLabel'] ?? 'Message' }}</span>
            <textarea
                name="message"
                rows="5"
                class="mt-2 w-full border bg-transparent px-4 py-3 text-sm focus:outline-none"
                style="border-color: var(--site-border, #cbd5e1); border-radius: var(--site-radius, 14px);"
                placeholder="{{ $props['messageLabel'] ?? 'Message' }}"
                @if($isEditor) tabindex="-1" @endif
            ></textarea>
        </label>

        <button
            type="submit"
            class="site-button mt-6"
            style="background-color: {{ $props['buttonColor'] ?? 'var(--site-primary, #2563eb)' }}; border-radius: var(--site-radius, 14px);"
            @if($isEditor) tabindex="-1" @endif
        >
            {{ $props['buttonText'] ?? 'Send message' }}
        </button>
    </form>
</div>
