<button {{ $attributes->merge([
    'class' => 'px-4 py-2 rounded-lg font-medium transition bg-indigo-600 text-white hover:bg-indigo-700'
]) }}>
    {{ $slot }}
</button>