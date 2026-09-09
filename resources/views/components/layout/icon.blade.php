@props(['name'])

@switch($name)

@case('dashboard')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <rect x="3" y="3" width="7" height="7" rx="1"/>
    <rect x="14" y="3" width="7" height="4" rx="1"/>
    <rect x="14" y="11" width="7" height="9" rx="1"/>
    <rect x="3" y="14" width="7" height="6" rx="1"/>
</svg>
@break

@case('builder')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <rect x="2" y="4" width="20" height="16" rx="2"/>
    <path d="M7 8h10M7 12h6"/>
</svg>
@break

@case('content')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
    <polyline points="14 2 14 8 20 8"/>
    <line x1="16" y1="13" x2="8" y2="13"/>
    <line x1="16" y1="17" x2="8" y2="17"/>
    <polyline points="10 9 9 9 8 9"/>
</svg>
@break

@case('media')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <rect x="2" y="4" width="20" height="16" rx="2"/>
    <path d="M9 10a2 2 0 100-4 2 2 0 000 4z"/>
    <path d="M21 15l-5-4-3 3-4-4-5 5"/>
</svg>
@break

@case('design')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M12 19l7-7 3 3-7 7-3-3z"/>
    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/>
    <path d="M2 2l7.586 7.586"/>
    <circle cx="11" cy="11" r="2"/>
</svg>
@break

@case('users')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <circle cx="8" cy="8" r="3"/>
    <circle cx="16" cy="8" r="3"/>
    <path d="M2 20c0-3 2.5-5 6-5s6 2 6 5"/>
    <path d="M16 15c2 0 4 1.5 4 4"/>
</svg>
@break

@case('page')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M7 3h7l5 5v13H7z"/>
    <path d="M14 3v5h5"/>
    <path d="M9 12h6"/>
    <path d="M9 16h6"/>
</svg>
@break

@case('navigation')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M12 3l7 18-7-4-7 4 7-18z"/>
</svg>
@break

@case('menu')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
    <rect x="4" y="5" width="16" height="14" rx="2"/>
    <path stroke-linecap="round" d="M8 9h8M8 13h8M8 17h5"/>
</svg>
@break

@case('site-template')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <rect x="3" y="4" width="18" height="16" rx="2"/>
    <path d="M3 9h18"/>
    <path d="M8 13h3"/>
    <path d="M13 13h3"/>
    <path d="M8 16h8"/>
</svg>
@break

@case('template')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <rect x="3" y="4" width="18" height="16" rx="2"/>
    <path stroke-linecap="round" d="M3 9h18"/>
    <rect x="7" y="12" width="4" height="4" rx="0.8"/>
    <rect x="13" y="12" width="4" height="4" rx="0.8"/>
</svg>
@break

@case('destination')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M12 21s6-5.2 6-10a6 6 0 10-12 0c0 4.8 6 10 6 10z"/>
    <circle cx="12" cy="11" r="2.2"/>
</svg>
@break

@case('offer')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M20 12l-8 8-10-10V4h6z"/>
    <path d="M7.5 7.5h.01"/>
</svg>
@break

@case('form')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <rect x="4" y="4" width="16" height="16" rx="2"/>
    <path d="M8 8h8M8 12h8M8 16h5"/>
</svg>
@break

@case('theme')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M4 20h16"/>
    <path d="M7 16l5-10 5 10"/>
</svg>
@break

@case('style')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M4 7h16"/>
    <path d="M4 12h16"/>
    <path d="M4 17h10"/>
</svg>
@break

@case('user')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <circle cx="12" cy="8" r="3"/>
    <path d="M5 20a7 7 0 0114 0"/>
</svg>
@break

@case('role')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <rect x="4" y="4" width="16" height="16" rx="3"/>
    <path d="M8 9h8M8 13h5"/>
</svg>
@break

@case('permission')
<svg class="w-[16px] h-[16px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M12 3l8 4v5c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4z"/>
    <path d="M9 12l2 2 4-4"/>
</svg>
@break

@case('marketing')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <path d="M22 12c0 5.5-4.5 9-10 9a11 11 0 01-6.4-2L2 21l2.1-3.5A9 9 0 012 12C2 6.5 6.5 3 12 3s10 3.5 10 9z"/>
    <circle cx="8" cy="12" r="1"/>
    <circle cx="12" cy="12" r="1"/>
    <circle cx="16" cy="12" r="1"/>
</svg>
@break

@case('settings')
<svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="3"/>
    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
</svg>
@break

{{-- ACTION BUTTON ICONS --}}
@case('builder-action')
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v4H4V5z"/>
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 9h6v12H6a2 2 0 01-2-2V9z"/>
    <path stroke-linecap="round" stroke-linejoin="round" d="M10 9h10v10a2 2 0 01-2 2h-8V9z"/>
</svg>
@break

@case('edit')
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
</svg>
@break

@case('preview')
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
</svg>
@break

@case('view')
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
</svg>
@break

@case('delete')
<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
</svg>
@break

@endswitch
