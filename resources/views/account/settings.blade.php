@extends('layouts.admin')

@section('topbar')
<x-layout.topbar />
@endsection

@section('content')
<style>
    .account-settings-page {
        width: 100%;
        max-width: 1450px;
        padding-top: clamp(8px, 0.8vw, 12px);
        padding-bottom: clamp(28px, 4vw, 48px);
    }

    .account-settings-form,
    .account-settings-grid,
    .account-settings-main,
    .account-settings-general,
    .account-settings-panel,
    .account-settings-danger {
        min-width: 0;
    }

    .account-settings-header {
        margin-bottom: clamp(20px, 1.5vw, 24px);
        padding-bottom: clamp(16px, 1.2vw, 18px);
    }

    .account-settings-breadcrumb {
        margin-bottom: clamp(12px, 1vw, 14px);
        overflow-x: auto;
        white-space: nowrap;
    }

    .account-settings-header-layout {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .account-settings-header-copy {
        min-width: 0;
    }

    .account-settings-title {
        font-size: clamp(24px, 2vw, 29px);
    }

    .account-settings-subtitle {
        margin-top: 8px;
        font-size: clamp(13px, 1vw, 15px);
        line-height: 1.5;
    }

    .account-settings-header-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        width: 100%;
    }

    .account-settings-header-action {
        width: 100%;
        min-width: 0;
        min-height: 42px;
        padding-inline: clamp(18px, 2vw, 28px);
    }

    .account-settings-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        align-items: start;
        gap: clamp(22px, 1.8vw, 28px);
    }

    .account-settings-nav {
        width: 100%;
        height: auto;
        padding: clamp(14px, 1.3vw, 16px);
    }

    .account-settings-nav-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .account-settings-nav-item {
        min-width: 0;
        gap: 10px;
        padding: 8px 10px;
    }

    .account-settings-nav-icon {
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
    }

    .account-settings-nav-icon svg {
        width: 17px;
        height: 17px;
    }

    .account-settings-nav-label,
    .account-settings-nav-danger-copy {
        min-width: 0;
    }

    .account-settings-nav-divider {
        margin-block: 14px;
    }

    .account-settings-nav-danger {
        display: flex;
        width: 100%;
        min-width: 0;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
    }

    .account-settings-main {
        display: grid;
        gap: 20px;
    }

    .account-settings-general,
    .account-settings-panel {
        height: auto;
        overflow: visible;
        border-radius: 12px;
        padding: clamp(20px, 1.6vw, 24px);
        box-shadow: 0 20px 48px -38px rgba(15, 23, 42, 0.48);
    }

    .account-settings-panel {
        height: 100%;
    }

    .account-settings-card-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: clamp(18px, 1.4vw, 20px);
    }

    .account-settings-card-title {
        font-size: clamp(15px, 1.2vw, 17px);
        line-height: 1.3;
    }

    .account-settings-section-icon {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
    }

    .account-settings-section-icon svg {
        width: 18px;
        height: 18px;
    }

    .account-settings-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: clamp(16px, 1.4vw, 20px);
    }

    .account-settings-field-label {
        margin-bottom: 7px;
    }

    .account-settings-field {
        width: 100%;
        min-width: 0;
        height: 40px;
    }

    .account-settings-preview {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px 12px;
        margin-top: clamp(16px, 1.4vw, 20px);
        padding: 10px 14px;
    }

    .account-settings-secondary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr));
        align-items: stretch;
        gap: clamp(20px, 1.6vw, 24px);
    }

    .account-settings-notification-list {
        display: grid;
        gap: clamp(18px, 1.4vw, 20px);
    }

    .account-settings-notification-row {
        display: flex;
        min-width: 0;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .account-settings-notification-copy,
    .account-settings-security-copy {
        min-width: 0;
    }

    .account-settings-toggle {
        flex: 0 0 auto;
    }

    .account-settings-security-list {
        display: grid;
    }

    .account-settings-security-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        align-items: center;
        gap: 12px;
        padding-block: 10px;
    }

    .account-settings-security-row:first-child {
        padding-top: 0;
    }

    .account-settings-security-row:last-child {
        padding-bottom: 0;
    }

    .account-settings-security-action {
        width: 100%;
        min-width: 0;
        min-height: 36px;
        padding-inline: 16px;
    }

    .account-settings-security-value,
    .account-settings-security-status {
        justify-self: start;
    }

    .account-settings-danger {
        height: auto;
        overflow: visible;
        border-radius: 14px;
        padding: clamp(16px, 1.7vw, 22px);
    }

    .account-settings-danger-content {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        align-items: center;
        gap: 16px;
        width: 100%;
        min-width: 0;
    }

    .account-settings-danger-copy {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 14px;
    }

    .account-settings-danger-text {
        min-width: 0;
    }

    .account-settings-danger-description {
        max-width: 100%;
        overflow: visible;
        line-height: 1.5;
        text-overflow: clip;
        white-space: normal;
    }

    .account-settings-danger-button {
        width: 100%;
        min-width: 0;
        min-height: 38px;
        padding-inline: 20px;
        white-space: nowrap;
    }

    @media (min-width: 640px) {
        .account-settings-header-actions {
            max-width: 360px;
        }

        .account-settings-nav-list {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .account-settings-form-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 900px) {
        .account-settings-security-row {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .account-settings-security-action {
            width: auto;
        }

        .account-settings-security-value,
        .account-settings-security-status {
            justify-self: end;
        }

        .account-settings-danger-content {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .account-settings-danger-copy {
            align-items: center;
        }

        .account-settings-danger-button {
            width: auto;
        }
    }

    @media (min-width: 960px) {
        .account-settings-header-layout {
            flex-direction: row;
            align-items: flex-start;
            justify-content: space-between;
        }

        .account-settings-header-actions {
            width: auto;
            max-width: none;
            flex: 0 0 auto;
        }
    }

    @media (min-width: 1180px) {
        .account-settings-page {
            padding-top: 0;
            padding-bottom: 0;
        }

        .account-settings-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
        }

        .account-settings-breadcrumb {
            margin-bottom: 14px;
        }

        .account-settings-title {
            font-size: 24px;
        }

        .account-settings-subtitle {
            margin-top: 8px;
            font-size: 14px;
        }

        .account-settings-header-action {
            min-height: 40px;
        }

        .account-settings-grid {
            grid-template-columns: clamp(280px, 24vw, 308px) minmax(0, 1fr);
            gap: 24px;
        }

        .account-settings-secondary {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr));
            gap: 20px;
        }

        .account-settings-nav {
            position: sticky;
            top: 0;
            align-self: start;
            padding: 16px;
        }

        .account-settings-nav-list {
            grid-template-columns: minmax(0, 1fr);
        }

        .account-settings-nav-item {
            padding: 9px 12px;
        }

        .account-settings-nav-divider {
            margin-block: 18px;
        }

        .account-settings-nav-danger {
            padding: 9px 12px;
        }

        .account-settings-main {
            gap: 20px;
        }

        .account-settings-general {
            padding: 20px 24px;
        }

        .account-settings-panel {
            padding: 18px 22px;
        }

        .account-settings-card-heading {
            margin-bottom: 14px;
        }

        .account-settings-section-icon {
            width: 32px;
            height: 32px;
            flex-basis: 32px;
        }

        .account-settings-card-title {
            font-size: 16px;
        }

        .account-settings-form-grid {
            gap: 14px 20px;
        }

        .account-settings-preview {
            margin-top: 12px;
            padding-block: 8px;
        }

        .account-settings-notification-list {
            gap: 16px;
        }

        .account-settings-security-row {
            padding-block: 10px;
        }

        .account-settings-danger {
            padding: 10px 16px;
        }

        .account-settings-danger-copy {
            align-items: center;
        }

        .account-settings-danger-description {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-settings-danger-button {
            min-height: 36px;
        }
    }

</style>

@php
    $timezone = old('timezone', $user->timezone ?: 'Africa/Casablanca');
    try {
        $previewDate = now($timezone);
    } catch (\Throwable $e) {
        $previewDate = now();
    }

    $dateFormats = [
        'M d, Y' => $previewDate->format('M d, Y'),
        'd M Y' => $previewDate->format('d M Y'),
        'Y-m-d' => $previewDate->format('Y-m-d'),
    ];

    $timePreview = old('time_format', $user->time_format ?: '24h') === '12h'
        ? $previewDate->format('g:i A')
        : $previewDate->format('H:i');

    $settingsNav = [
        ['label' => 'General', 'icon' => 'cog-6-tooth', 'active' => true],
        ['label' => 'Security', 'icon' => 'shield-check', 'active' => false],
        ['label' => 'Notifications', 'icon' => 'bell', 'active' => false],
        ['label' => 'Appearance', 'icon' => 'swatch', 'active' => false],
        ['label' => 'Sessions', 'icon' => 'computer-desktop', 'active' => false],
    ];
@endphp

<div class="account-page account-settings-page mx-auto text-[#0f172a]">
    <form id="settingsForm" class="account-settings-form" method="POST" action="{{ route('account.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="account-page-header account-settings-header border-b border-[#e6edf5]">
            <div class="account-breadcrumb account-settings-breadcrumb flex items-center gap-3 text-[12px] font-semibold text-[#71809a]">
                <a href="{{ route('dashboard') }}" class="transition hover:text-[#6c4df5]">Dashboard</a>
                <span class="text-[#a8b3c4]">›</span>
                <span>Account</span>
                <span class="text-[#a8b3c4]">›</span>
                <span class="text-[#5b3ff1]">Account Settings</span>
            </div>

            <div class="account-header-layout account-settings-header-layout">
                <div class="account-header-copy account-settings-header-copy">
                    <h1 class="account-title account-settings-title font-black leading-none tracking-[-0.02em] text-[#0f172a]">Account Settings</h1>
                    <p class="account-subtitle account-settings-subtitle font-medium text-[#64748b]">Manage how your account works and your preferences.</p>
                </div>

                <div class="account-header-actions account-settings-header-actions">
                    <a href="{{ route('dashboard') }}" class="account-header-action account-settings-header-action rounded-[9px] border border-[#d8e1ef] bg-white text-[13px] font-bold text-[#111827] shadow-sm transition hover:bg-[#f8fafc]">
                        Cancel
                    </a>
                    <button type="submit" class="account-header-action account-settings-header-action rounded-[9px] bg-[#5b3ff1] text-[13px] font-bold text-white shadow-[0_12px_26px_-14px_rgba(91,63,241,0.8)] transition hover:bg-[#4f35de]">
                        Save changes
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-[10px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-[10px] border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                Please review the highlighted fields.
            </div>
        @endif

        <div class="account-layout account-settings-grid">
            <aside class="account-card account-side-column account-settings-nav">
                <div class="account-settings-nav-list">
                    @foreach($settingsNav as $item)
                        <a href="#"
                           class="account-settings-nav-item flex items-center rounded-[10px] transition {{ $item['active'] ? 'bg-[#f1edff] text-[#5b3ff1]' : 'text-[#111827] hover:bg-[#f8fafc]' }}">
                            <span class="account-settings-nav-icon grid place-items-center rounded-[8px] {{ $item['active'] ? 'bg-[#ede7ff]' : 'bg-[#f8fafc]' }}">
                                <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="h-4 w-4" />
                            </span>
                            <span class="account-settings-nav-label text-[13px] font-bold">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="account-settings-nav-divider h-px bg-[#e6edf5]"></div>

                <button type="button" class="account-settings-nav-danger rounded-[10px] text-left text-red-600 transition hover:bg-red-50">
                    <span class="account-settings-nav-icon grid place-items-center rounded-[8px] bg-red-50">
                        <x-dynamic-component component="heroicon-o-user-minus" class="h-4 w-4" />
                    </span>
                    <span class="account-settings-nav-danger-copy">
                        <span class="block text-[13px] font-bold">Deactivate account</span>
                        <span class="mt-0.5 block text-[12px] font-medium text-[#64748b]">Temporarily disable your account</span>
                    </span>
                </button>
            </aside>

            <div class="account-main-column account-settings-main">
                <section class="account-card account-settings-general">
                    <div class="account-settings-card-heading">
                        <span class="account-settings-section-icon grid place-items-center rounded-[9px] bg-[#f3efff] text-[#5b3ff1]">
                            <x-dynamic-component component="heroicon-o-cog-6-tooth" class="h-4 w-4" />
                        </span>
                        <h2 class="account-settings-card-title font-black text-[#0f172a]">General Settings</h2>
                    </div>

                    <div class="account-settings-form-grid">
                        <label class="block">
                            <span class="account-settings-field-label block text-[11px] font-bold text-[#52627a]">Language</span>
                            <select name="language" class="account-field account-settings-field">
                                @foreach(['en' => 'English (US)', 'fr' => 'French', 'es' => 'Spanish', 'ar' => 'Arabic'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('language', $user->language ?: 'en') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="account-settings-field-label block text-[11px] font-bold text-[#52627a]">Time zone</span>
                            <select name="timezone" class="account-field account-settings-field">
                                @foreach([
                                    'Africa/Casablanca' => '(UTC+01:00) Africa/Casablanca',
                                    'Europe/Paris' => '(UTC+02:00) Europe/Paris',
                                    'America/Los_Angeles' => '(UTC-07:00) Pacific Time (US & Canada)',
                                    'America/New_York' => '(UTC-05:00) Eastern Time (US & Canada)',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected($timezone === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="account-settings-field-label block text-[11px] font-bold text-[#52627a]">Date format</span>
                            <select name="date_format" class="account-field account-settings-field">
                                @foreach($dateFormats as $value => $label)
                                    <option value="{{ $value }}" @selected(old('date_format', $user->date_format ?: 'M d, Y') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="account-settings-field-label block text-[11px] font-bold text-[#52627a]">Time format</span>
                            <select name="time_format" class="account-field account-settings-field">
                                <option value="24h" @selected(old('time_format', $user->time_format ?: '24h') === '24h')>24-hour (14:30)</option>
                                <option value="12h" @selected(old('time_format', $user->time_format ?: '24h') === '12h')>12-hour (2:30 PM)</option>
                            </select>
                        </label>
                    </div>

                    <div class="account-settings-preview rounded-[9px] bg-[#f3f0ff] text-[13px] font-semibold text-[#52627a]">
                        <x-dynamic-component component="heroicon-o-eye" class="h-4 w-4 text-[#52627a]" />
                        <span class="text-[#0f172a]">Preview:</span>
                        <span>{{ $previewDate->format(old('date_format', $user->date_format ?: 'M d, Y')) }} {{ $timePreview }}</span>
                    </div>
                </section>

                <div class="account-settings-secondary">
                    <section class="account-card account-settings-panel">
                        <div class="account-settings-card-heading">
                            <span class="account-settings-section-icon grid place-items-center rounded-[9px] bg-[#f3efff] text-[#5b3ff1]">
                                <x-dynamic-component component="heroicon-o-bell" class="h-4 w-4" />
                            </span>
                            <h2 class="account-settings-card-title font-black text-[#0f172a]">Notification Preferences</h2>
                        </div>

                        <div class="account-settings-notification-list">
                            @foreach([
                                'email_notifications' => ['Email notifications', 'Receive important updates via email'],
                                'product_updates' => ['Product updates', 'Get notified about new features and improvements'],
                                'security_alerts' => ['Security alerts', 'Receive security-related notifications'],
                            ] as $field => [$label, $description])
                                <div class="account-settings-notification-row">
                                    <div class="account-settings-notification-copy">
                                        <div class="text-[13px] font-black text-[#0f172a]">{{ $label }}</div>
                                        <div class="mt-0.5 text-[11px] font-medium text-[#64748b]">{{ $description }}</div>
                                    </div>
                                    <label class="account-settings-toggle relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" name="{{ $field }}" value="1" class="peer sr-only" @checked(old($field, $preferences[$field] ?? true))>
                                        <span class="h-6 w-11 rounded-full bg-[#cbd5e1] transition peer-checked:bg-[#5b3ff1]"></span>
                                        <span class="absolute left-1 h-4 w-4 rounded-full bg-white transition peer-checked:translate-x-5"></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="account-card account-settings-panel account-settings-security">
                        <div class="account-settings-card-heading">
                            <span class="account-settings-section-icon grid place-items-center rounded-[9px] bg-[#f3efff] text-[#5b3ff1]">
                                <x-dynamic-component component="heroicon-o-shield-check" class="h-4 w-4" />
                            </span>
                            <h2 class="account-settings-card-title font-black text-[#0f172a]">Security</h2>
                        </div>

                        <div class="account-settings-security-list divide-y divide-[#e6edf5]">
                            <div class="account-settings-security-row">
                                <div class="account-settings-security-copy">
                                    <div class="text-[13px] font-black text-[#0f172a]">Change password</div>
                                    <div class="mt-0.5 text-[11px] font-medium text-[#64748b]">Update your password regularly</div>
                                </div>
                                <button type="button" class="account-settings-security-action inline-flex items-center justify-center rounded-[8px] border border-[#9b87ff] bg-white text-[12px] font-bold text-[#5b3ff1] transition hover:bg-[#f6f3ff]">
                                    Change password
                                </button>
                            </div>

                            <div class="account-settings-security-row">
                                <div class="text-[13px] font-black text-[#0f172a]">Last password update</div>
                                <div class="account-settings-security-value text-[13px] font-semibold text-[#52627a]">{{ ($user->password_changed_at ?: $user->created_at)?->format('M d, Y') }}</div>
                            </div>

                            <div class="account-settings-security-row">
                                <div class="account-settings-security-copy">
                                    <div class="text-[13px] font-black text-[#0f172a]">Active sessions</div>
                                    <div class="mt-0.5 text-[11px] font-medium text-[#64748b]">1 active session</div>
                                </div>
                                <button type="button" class="account-settings-security-action inline-flex items-center justify-center rounded-[8px] border border-[#9b87ff] bg-white text-[12px] font-bold text-[#5b3ff1] transition hover:bg-[#f6f3ff]">
                                    View sessions
                                </button>
                            </div>

                            <div class="account-settings-security-row">
                                <div class="account-settings-security-copy">
                                    <div class="text-[13px] font-black text-[#0f172a]">Two-factor authentication</div>
                                    <div class="mt-0.5 text-[11px] font-medium text-[#64748b]">Add an extra layer of security</div>
                                </div>
                                <span class="account-settings-security-status inline-flex items-center gap-2 rounded-full border border-orange-300 bg-orange-50 px-3 py-1.5 text-[11px] font-bold text-orange-600">
                                    <x-dynamic-component component="heroicon-o-exclamation-triangle" class="h-3.5 w-3.5" />
                                    2FA not enabled
                                </span>
                            </div>
                        </div>
                    </section>
                </div>

                <section class="account-settings-danger border border-red-300 bg-red-50/30">
                    <div class="account-settings-danger-content">
                        <div class="account-settings-danger-copy">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[10px] text-red-600">
                                <x-dynamic-component component="heroicon-o-exclamation-triangle" class="h-7 w-7" />
                            </span>
                            <div class="account-settings-danger-text">
                                <h2 class="text-[15px] font-black text-[#0f172a]">Deactivate Account</h2>
                                <p class="account-settings-danger-description mt-0.5 text-[12px] font-medium text-[#64748b]">Temporarily disable your account and restrict access to the CMS. You can reactivate your account anytime by signing in again.</p>
                            </div>
                        </div>
                        <button type="button" class="account-settings-danger-button inline-flex items-center justify-center rounded-[8px] border border-red-500 bg-white text-[12px] font-bold text-red-600 transition hover:bg-red-50">
                            Deactivate account
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
@endsection
