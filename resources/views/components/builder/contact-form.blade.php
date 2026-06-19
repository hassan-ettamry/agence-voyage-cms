@php
    $rawAction = trim((string) ($props['actionUrl'] ?? ''));
    $isSafeAction = $rawAction !== ''
        && (
            preg_match('/^https?:\/\//i', $rawAction) === 1
            || str_starts_with($rawAction, '/')
        );
    $action = $isSafeAction ? $rawAction : '#';
@endphp

<div
    @if($isEditor)
        data-type="contact-form"
        data-node-id="{{ $nodeId }}"
        draggable="true"
        data-drag-action="reorder"
    @endif
    class="{{ $isEditor ? 'builder-node' : '' }} p-4 transition-all duration-150"
    style="color: {{ $props['textColor'] ?? 'var(--site-text, #111827)' }};"
>
    <form
        action="{{ $action }}"
        method="post"
        class="mx-auto max-w-2xl border p-6"
        style="
            background-color: var(--site-surface, #ffffff);
            border-color: var(--site-border, #e2e8f0);
            border-radius: var(--site-radius, 14px);
            box-shadow: var(--site-shadow, none);
        "
        @if($isEditor || ! $isSafeAction)
            onsubmit="return false"
        @endif
    >
        @if(! $isEditor && str_starts_with($action, '/'))
            @csrf
        @endif

        <div class="mb-6">
            <h3
                @if($isEditor)
                    contenteditable="true"
                    data-field="title"
                @endif
                class="{{ $isEditor ? 'outline-none' : '' }} text-2xl font-bold"
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
                    class="mt-1 w-full border px-3 py-2 text-sm focus:outline-none"
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
                    class="mt-1 w-full border px-3 py-2 text-sm focus:outline-none"
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
                class="mt-1 w-full border px-3 py-2 text-sm focus:outline-none"
                style="border-color: var(--site-border, #cbd5e1); border-radius: var(--site-radius, 14px);"
                placeholder="{{ $props['messageLabel'] ?? 'Message' }}"
                @if($isEditor) tabindex="-1" @endif
            ></textarea>
        </label>

        <button
            type="submit"
            class="mt-5 inline-flex px-5 py-2.5 text-sm font-semibold text-white"
            style="background-color: {{ $props['buttonColor'] ?? 'var(--site-primary, #2563eb)' }}; border-radius: var(--site-radius, 14px);"
            @if($isEditor) tabindex="-1" @endif
        >
            {{ $props['buttonText'] ?? 'Send message' }}
        </button>
    </form>
</div>
