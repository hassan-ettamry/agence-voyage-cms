<div {{ $attributes->merge([
    'class' => 'bg-white rounded-xl shadow-sm border p-6'
]) }}>
    {{ $slot }}
</div>