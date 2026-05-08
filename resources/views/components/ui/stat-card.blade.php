@props(['label', 'value', 'note' => null, 'color' => 'text-gray-400'])

<div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">
        {{ $label }}
    </p>

    <p class="text-3xl font-bold text-gray-900">
        {{ $value }}
    </p>

    @if($note)
        <p class="mt-1.5 text-xs {{ $color }}">
            {{ $note }}
        </p>
    @endif
</div>