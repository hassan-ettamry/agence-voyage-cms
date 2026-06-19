@props([
    'builder' => null,
    'edit' => null,
    'delete' => null,
    'preview' => null,
    'view' => null,
    'align' => 'center',
    'confirm' => 'Are you sure?'
])

@php
    $justify = [
        'left' => 'justify-start',
        'right' => 'justify-end',
        'center' => 'justify-center',
    ][$align] ?? 'justify-center';
@endphp

<div class="flex items-center {{ $justify }} gap-1">

    {{-- BUILDER --}}
    @if($builder)
    <a href="{{ $builder }}" title="Builder"
       class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-md text-gray-400 hover:text-indigo-600 transition-colors">
        <x-layout.icon name="builder-action"/>
    </a>
    @endif

    {{-- EDIT --}}
    @if($edit)
    <a href="{{ $edit }}" title="Edit"
       class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-md text-gray-400 hover:text-indigo-600 transition-colors">
        <x-layout.icon name="edit"/>
    </a>
    @endif

    {{-- PREVIEW --}}
    @if($preview)
    <a href="{{ $preview }}" title="Preview"
       class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-md text-gray-400 hover:text-indigo-600 transition-colors">
        <x-layout.icon name="preview"/>
    </a>
    @endif

    {{-- VIEW --}}
    @if($view)
    <a href="{{ $view }}" title="View"
       class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-md text-gray-400 hover:text-indigo-600 transition-colors">
        <x-layout.icon name="view"/>
    </a>
    @endif

    {{-- DELETE --}}
    @if($delete)
    <form method="POST" action="{{ $delete }}" class="inline" onsubmit="return confirm('{{ $confirm }}')">
        @csrf
        @method('DELETE')
        <button type="submit" title="Delete"
                class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-red-300 hover:bg-red-50 rounded-md text-gray-400 hover:text-red-500 transition-colors">
            <x-layout.icon name="delete"/>
        </button>
    </form>
    @endif

</div>
