@props([
    'label',
    'value',
    'note' => null,
    'color' => 'text-gray-400',
    'tone' => null,
    'icon' => null,
])

@php
    $labelText = (string) $label;
    $labelKey = \Illuminate\Support\Str::of($labelText)->lower()->toString();
    $tone = $tone
        ?: (\Illuminate\Support\Str::contains($color, 'emerald') ? 'emerald' : null)
        ?: (match (true) {
            \Illuminate\Support\Str::contains($labelKey, ['published', 'admin', 'used']) => 'emerald',
            \Illuminate\Support\Str::contains($labelKey, ['draft', 'featured', 'special', 'role']) => 'orange',
            \Illuminate\Support\Str::contains($labelKey, ['permission', 'recent', 'storage']) => 'blue',
            default => 'violet',
        });

    $palette = [
        'violet' => [
            'accent' => '#6c4df5',
            'text' => 'text-[#6c4df5]',
            'iconBg' => 'bg-[#eee9ff]',
            'iconText' => 'text-[#6c4df5]',
            'bottom' => 'border-b-[#6c4df5]',
            'spark' => '#6c4df5',
            'fill' => 'rgba(108, 77, 245, 0.12)',
        ],
        'emerald' => [
            'accent' => '#10b981',
            'text' => 'text-[#08a66a]',
            'iconBg' => 'bg-[#def8eb]',
            'iconText' => 'text-[#08a66a]',
            'bottom' => 'border-b-[#10b981]',
            'spark' => '#10b981',
            'fill' => 'rgba(16, 185, 129, 0.13)',
        ],
        'orange' => [
            'accent' => '#f97316',
            'text' => 'text-[#f97316]',
            'iconBg' => 'bg-[#fff0e5]',
            'iconText' => 'text-[#f97316]',
            'bottom' => 'border-b-[#f97316]',
            'spark' => '#f97316',
            'fill' => 'rgba(249, 115, 22, 0.13)',
        ],
        'blue' => [
            'accent' => '#2f80ed',
            'text' => 'text-[#2f80ed]',
            'iconBg' => 'bg-[#e8f2ff]',
            'iconText' => 'text-[#2f80ed]',
            'bottom' => 'border-b-[#2f80ed]',
            'spark' => '#2f80ed',
            'fill' => 'rgba(47, 128, 237, 0.13)',
        ],
    ];

    $style = $palette[$tone] ?? $palette['violet'];
    $iconName = $icon ?: match (true) {
        \Illuminate\Support\Str::contains($labelKey, ['page', 'draft', 'recent']) => \Illuminate\Support\Str::contains($labelKey, 'recent') ? 'calendar' : 'page',
        \Illuminate\Support\Str::contains($labelKey, ['published', 'used']) => 'check',
        \Illuminate\Support\Str::contains($labelKey, ['destination']) => 'pin',
        \Illuminate\Support\Str::contains($labelKey, ['featured', 'special']) => 'star',
        \Illuminate\Support\Str::contains($labelKey, ['offer']) => 'tag',
        \Illuminate\Support\Str::contains($labelKey, ['admin', 'user']) => 'users',
        \Illuminate\Support\Str::contains($labelKey, ['role']) => 'shield',
        \Illuminate\Support\Str::contains($labelKey, ['permission']) => 'lock',
        default => 'shield',
    };

    $displayLabel = match ($labelText) {
        'Destinations' => 'Total Destinations',
        default => $labelText,
    };
@endphp

<article class="h-[142px] overflow-hidden rounded-[14px] border border-[#e2e8f0] border-b-[4px] {{ $style['bottom'] }} bg-white px-[20px] py-[22px] shadow-[0_14px_34px_-24px_rgba(15,23,42,0.45)]">
    <div class="grid h-full grid-cols-[58px_minmax(0,1fr)_70px] items-center gap-[14px]">
        <div class="grid h-[58px] w-[58px] shrink-0 place-items-center rounded-[15px] {{ $style['iconBg'] }} {{ $style['iconText'] }}">
            @switch($iconName)
                @case('users')
                    <svg class="h-[30px] w-[30px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <circle cx="9" cy="8" r="3.2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.8 19c.8-3.2 2.9-4.8 6.2-4.8s5.4 1.6 6.2 4.8"/>
                        <circle cx="17" cy="10" r="2.4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 15.2c2.1.2 3.5 1.4 4 3.8"/>
                    </svg>
                    @break
                @case('check')
                    <svg class="h-[31px] w-[31px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8 12.5 2.7 2.7L16.5 8.8"/>
                    </svg>
                    @break
                @case('page')
                    <svg class="h-[30px] w-[30px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5M9 13h6M9 17h5"/>
                    </svg>
                    @break
                @case('calendar')
                    <svg class="h-[30px] w-[30px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <rect x="4" y="5" width="16" height="15" rx="2"/>
                        <path stroke-linecap="round" d="M8 3v4M16 3v4M4 10h16"/>
                        <path stroke-linecap="round" d="M8 14h2M12 14h2M16 14h.01M8 17h2M12 17h2"/>
                    </svg>
                    @break
                @case('pin')
                    <svg class="h-[31px] w-[31px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.8 7-11a7 7 0 1 0-14 0c0 5.2 7 11 7 11Z"/>
                        <circle cx="12" cy="10" r="2.6"/>
                    </svg>
                    @break
                @case('star')
                    <svg class="h-[32px] w-[32px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m12 3 2.8 5.6 6.2.9-4.5 4.4 1.1 6.1-5.6-2.9L6.4 20l1.1-6.1L3 9.5l6.2-.9L12 3Z"/>
                    </svg>
                    @break
                @case('tag')
                    <svg class="h-[31px] w-[31px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12 12 20 3 11V4h7l10 8Z"/>
                        <circle cx="7.8" cy="8" r="1"/>
                    </svg>
                    @break
                @case('pencil')
                    <svg class="h-[31px] w-[31px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4l10.5-10.5a2.1 2.1 0 0 0-3-3L5 17v3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.5 7.5 2 2"/>
                    </svg>
                    @break
                @case('lock')
                    <svg class="h-[31px] w-[31px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <rect x="5" y="10" width="14" height="10" rx="2"/>
                        <path stroke-linecap="round" d="M8 10V7a4 4 0 0 1 8 0v3"/>
                    </svg>
                    @break
                @default
                    <svg class="h-[31px] w-[31px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 20 7v5c0 5-3.4 8.4-8 9-4.6-.6-8-4-8-9V7l8-4Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-5"/>
                    </svg>
            @endswitch
        </div>

        <div class="min-w-0">
            <div class="truncate whitespace-nowrap text-[12px] font-black uppercase leading-tight tracking-[0.06em] {{ $style['text'] }}">{{ $displayLabel }}</div>
            <div class="mt-[10px] text-[30px] font-black leading-none tracking-[0] text-[#111827]">{{ $value }}</div>
            @if($note)
                <div class="mt-[12px] truncate whitespace-nowrap text-[13px] font-medium leading-tight text-[#5c667a]">{{ $note }}</div>
            @endif
        </div>

        <svg class="mb-[8px] h-[40px] w-[70px] self-end" viewBox="0 0 92 46" fill="none" aria-hidden="true">
            <path d="M4 42 C14 23 21 15 30 24 C39 33 45 31 52 18 C60 4 68 24 74 19 C82 12 84 7 88 4 L88 46 L4 46 Z" fill="{{ $style['fill'] }}"/>
            <path d="M4 42 C14 23 21 15 30 24 C39 33 45 31 52 18 C60 4 68 24 74 19 C82 12 84 7 88 4" stroke="{{ $style['spark'] }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="88" cy="4" r="3.2" fill="{{ $style['spark'] }}"/>
        </svg>
    </div>
</article>
