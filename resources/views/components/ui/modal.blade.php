@props([
    'id' => 'modal',
    'title' => null,
    'width' => '500px'
])

<div id="{{ $id }}"
     class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto bg-black/40 p-4">

    <div class="relative max-h-[calc(100vh-2rem)] w-full overflow-y-auto rounded-xl bg-white p-5 shadow-xl sm:p-6"
         style="max-width: {{ $width }}">

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
