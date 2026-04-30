@props([
    'id' => 'modal',
    'title' => null,
    'width' => '500px'
])

<div id="{{ $id }}"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-xl shadow-xl p-6 relative"
         style="width: {{ $width }}">

        {{-- CLOSE --}}
        <button onclick="closeModal('{{ $id }}')"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
            ✕
        </button>

        {{-- TITLE (optional) --}}
        @if($title)
            <h2 class="text-lg font-semibold mb-4">
                {{ $title }}
            </h2>
        @endif

        {{-- CONTENT --}}
        <div>
            {{ $slot }}
        </div>

    </div>
</div>