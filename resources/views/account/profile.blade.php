@extends('layouts.admin')

@section('topbar')
<x-layout.topbar />
@endsection

@section('content')
<style>
    @media (min-width: 1180px) {
        .account-profile-page {
            margin-top: 0;
            padding-bottom: 0;
        }

        .account-profile-header {
            margin-bottom: 20px !important;
            padding-bottom: 16px !important;
        }

        .account-profile-breadcrumb {
            margin-bottom: 14px !important;
        }

        .account-profile-grid {
            display: grid;
            grid-template-columns: clamp(280px, 24vw, 308px) minmax(0, 1fr) !important;
            align-items: start;
            gap: 24px !important;
        }

        .account-profile-left {
            gap: 18px !important;
        }

        .account-avatar-card {
            min-height: 310px;
            padding: 22px 24px !important;
        }

        .account-profile-avatar {
            height: 98px !important;
            width: 98px !important;
            font-size: 36px !important;
        }

        .account-profile-name {
            margin-top: 14px !important;
            font-size: 19px !important;
        }

        .account-profile-role {
            margin-top: 8px !important;
        }

        .account-profile-email {
            margin-top: 12px !important;
        }

        .account-profile-photo-actions {
            margin-top: 18px !important;
            gap: 10px !important;
        }

        .account-profile-photo-actions label,
        .account-profile-photo-actions button {
            height: 38px !important;
            min-width: 0;
            padding-left: 10px !important;
            padding-right: 10px !important;
            white-space: nowrap;
        }

        .account-profile-photo-actions .account-photo-change {
            background-color: #f7f5ff !important;
        }

        .account-profile-photo-actions .account-photo-remove {
            background-color: #fff7f7 !important;
        }

        .account-status-card {
            min-height: 310px;
            padding: 20px 22px !important;
        }

        .account-status-card > div:first-child {
            margin-bottom: 12px !important;
        }

        .account-status-card .account-status-row {
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }

        .account-personal-card {
            min-height: 370px;
            padding: 24px 28px !important;
        }

        .account-context-card {
            min-height: 258px;
            padding: 26px 28px !important;
        }

        .account-personal-card > div:first-child,
        .account-context-card > div:first-child {
            margin-bottom: 18px !important;
        }

        .account-profile-input {
            height: 40px !important;
        }

        .account-profile-bio {
            height: 92px !important;
        }

        .account-context-icon {
            height: 36px !important;
            width: 36px !important;
        }

        .account-context-icon svg {
            height: 20px !important;
            width: 20px !important;
            stroke-width: 2;
        }

        .account-context-note {
            margin-top: 18px !important;
        }
    }

    @media (min-width: 1180px) and (max-width: 1450px) {
        .account-profile-grid {
            grid-template-columns: minmax(276px, 290px) minmax(0, 1fr) !important;
            gap: 20px !important;
        }

        .account-avatar-card,
        .account-status-card {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }

        .account-profile-photo-actions label,
        .account-profile-photo-actions button {
            font-size: 11px !important;
        }
    }

    @media (max-width: 1179px) {
        .account-profile-page {
            max-width: 960px;
        }

        .account-profile-grid {
            grid-template-columns: 1fr !important;
        }

        .account-profile-left {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            align-items: stretch;
        }

        .account-avatar-card,
        .account-status-card {
            height: 100%;
        }
    }

    @media (max-width: 760px) {
        .account-profile-left {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    $displayName = old('display_name', $user->display_name ?: $user->name);
    $initial = strtoupper(substr($displayName ?: $user->name, 0, 1));
    $roleName = $user->role?->name ?? 'User';
    $agencyName = $user->agency?->name ?? 'Agency';
    $memberSince = $user->created_at?->format('M d, Y') ?? 'Not available';
    $lastLogin = $user->last_login_at?->format('M d, Y H:i') ?? 'Current session';
@endphp

<div class="account-page account-profile-page mx-auto max-w-[1450px] pb-5 text-[#0f172a]">
    <form id="profileForm" method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="account-page-header account-profile-header mb-5 border-b border-[#e6edf5] pb-4">
            <div class="account-breadcrumb account-profile-breadcrumb mb-4 flex items-center gap-3 text-[12px] font-semibold text-[#71809a]">
                <a href="{{ route('dashboard') }}" class="transition hover:text-[#6c4df5]">Dashboard</a>
                <span class="text-[#a8b3c4]">›</span>
                <span>Account</span>
                <span class="text-[#a8b3c4]">›</span>
                <span class="text-[#5b3ff1]">Edit Profile</span>
            </div>

            <div class="account-header-layout">
                <div class="account-header-copy">
                    <h1 class="account-title text-[24px] font-black leading-none tracking-[-0.02em] text-[#0f172a]">Edit Profile</h1>
                    <p class="account-subtitle mt-2 text-[14px] font-medium text-[#64748b]">Manage your personal identity in the CMS.</p>
                </div>

                <div class="account-header-actions">
                    <a href="{{ route('dashboard') }}" class="account-header-action rounded-[8px] border border-[#d8e1ef] bg-white text-[13px] font-bold text-[#111827] shadow-sm transition hover:bg-[#f8fafc]">
                        Cancel
                    </a>
                    <button type="submit" class="account-header-action rounded-[8px] bg-[#5b3ff1] text-[13px] font-bold text-white shadow-[0_12px_26px_-14px_rgba(91,63,241,0.8)] transition hover:bg-[#4f35de]">
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

        <div class="account-layout account-profile-grid">
            <div class="account-side-column account-profile-left flex flex-col gap-4">
                <section class="account-card account-avatar-card p-5">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative">
                            <div class="account-profile-avatar grid h-[92px] w-[92px] place-items-center overflow-hidden rounded-full bg-gradient-to-br from-[#8b7cf6] to-[#5b3ff1] text-[34px] font-black text-white shadow-[0_18px_36px_-18px_rgba(91,63,241,0.75)]">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $displayName }}" class="h-full w-full object-cover">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            <label for="avatar" class="absolute bottom-0 right-0 grid h-8 w-8 cursor-pointer place-items-center rounded-full border border-[#d8e1ef] bg-white text-[#4b5563] shadow-[0_8px_18px_-10px_rgba(15,23,42,0.55)]">
                                <svg class="h-[16px] w-[16px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h3l2-3h6l2 3h3v11H4z"/>
                                    <circle cx="12" cy="14" r="3.5"/>
                                </svg>
                            </label>
                        </div>

                        <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">

                        <h2 class="account-profile-name mt-4 text-[18px] font-black leading-none text-[#0f172a]">{{ $displayName }}</h2>
                        <span class="account-profile-role mt-2 rounded-full bg-[#eee9ff] px-3 py-1 text-[11px] font-bold text-[#6c4df5]">{{ $roleName }}</span>
                        <p class="account-profile-email mt-3 text-[13px] font-medium text-[#64748b]">{{ $user->email }}</p>

                        <div class="account-profile-photo-actions mt-5 grid w-full grid-cols-2 gap-3">
                            <label for="avatar" class="account-photo-change inline-flex h-9 cursor-pointer items-center justify-center gap-2 rounded-[8px] border border-[#9b87ff] bg-[#f7f5ff] px-3 text-[12px] font-bold text-[#5b3ff1] transition hover:bg-[#f0ecff]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 18v2h16v-2"/>
                                </svg>
                                Change photo
                            </label>
                            <button type="submit" name="remove_avatar" value="1" class="account-photo-remove inline-flex h-9 items-center justify-center gap-2 rounded-[8px] border border-red-300 bg-[#fff7f7] px-3 text-[12px] font-bold text-red-600 transition hover:bg-red-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12M9 7V4h6v3m-8 0 .7 13h8.6L17 7"/>
                                </svg>
                                Remove photo
                            </button>
                        </div>
                    </div>
                </section>

                <section class="account-card account-status-card p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-[15px] font-black text-[#0f172a]">Account status</h2>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    </div>

                    <div class="divide-y divide-[#edf2f7]">
                        @foreach([
                            ['icon' => 'calendar-days', 'label' => 'Member since', 'value' => $memberSince],
                            ['icon' => 'clock', 'label' => 'Last login', 'value' => $lastLogin],
                            ['icon' => 'shield-check', 'label' => 'Role', 'value' => $roleName],
                            ['icon' => 'building-office-2', 'label' => 'Agency', 'value' => $agencyName],
                        ] as $item)
                            <div class="account-status-row flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-[8px] text-[#48617f]">
                                    <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="h-5 w-5" />
                                </span>
                                <div>
                                    <div class="text-[11px] font-bold text-[#71809a]">{{ $item['label'] }}</div>
                                    <div class="mt-0.5 text-[13px] font-semibold text-[#40516f]">{{ $item['value'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="account-main-column account-profile-main">
                <section class="account-card account-personal-card p-5">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="grid h-8 w-8 place-items-center rounded-[8px] bg-[#f3efff] text-[#5b3ff1]">
                            <x-dynamic-component component="heroicon-o-user" class="h-4 w-4" />
                        </span>
                        <h2 class="text-[16px] font-black text-[#0f172a]">Personal Information</h2>
                    </div>

                    <div class="grid gap-x-5 gap-y-4 lg:grid-cols-2">
                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-bold text-[#52627a]">Full name</span>
                            <input name="name" value="{{ old('name', $user->name) }}" required class="account-field account-profile-input">
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-bold text-[#52627a]">Display name</span>
                            <input name="display_name" value="{{ old('display_name', $displayName) }}" class="account-field account-profile-input">
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-bold text-[#52627a]">Email address</span>
                            <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="account-field account-profile-input">
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-bold text-[#52627a]">Phone number</span>
                            <input name="phone" value="{{ old('phone', $user->phone ?: '+1 (800) 123-4567') }}" class="account-field account-profile-input">
                        </label>
                    </div>

                    <label class="mt-4 block">
                        <span class="mb-1.5 block text-[11px] font-bold text-[#52627a]">Bio</span>
                        <div class="relative">
                            <textarea name="bio" maxlength="160" rows="3" class="account-field account-profile-bio h-[92px] resize-none">{{ old('bio', $user->bio ?: 'Travel enthusiast and content manager.') }}</textarea>
                            <span class="absolute bottom-3 right-4 text-[12px] font-semibold text-[#8a98ad]">{{ strlen(old('bio', $user->bio ?: 'Travel enthusiast and content manager.')) }} / 160</span>
                        </div>
                    </label>
                </section>

                <section class="account-card account-context-card p-5">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="account-context-icon grid h-8 w-8 place-items-center rounded-[8px] bg-[#f3efff] text-[#5b3ff1]">
                            <x-dynamic-component component="heroicon-o-shield-check" class="h-5 w-5" />
                        </span>
                        <h2 class="text-[16px] font-black text-[#0f172a]">Account Context</h2>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-2">
                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-bold text-[#52627a]">Role</span>
                            <div class="relative">
                                <input value="{{ $roleName }}" readonly class="account-field account-profile-input bg-[#f8fafc] pr-11 text-[#9aa6b8]">
                                <x-dynamic-component component="heroicon-o-lock-closed" class="absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9aa6b8]" />
                            </div>
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-bold text-[#52627a]">Agency</span>
                            <div class="relative">
                                <input value="{{ $agencyName }}" readonly class="account-field account-profile-input bg-[#f8fafc] pr-11 text-[#9aa6b8]">
                                <x-dynamic-component component="heroicon-o-lock-closed" class="absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-[#9aa6b8]" />
                            </div>
                        </label>
                    </div>

                    <div class="account-context-note mt-4 flex items-center gap-2 text-[12px] font-semibold text-[#64748b]">
                        <x-dynamic-component component="heroicon-o-information-circle" class="h-4 w-4 text-[#64748b]" />
                        Role and agency are managed by administrators and cannot be changed here.
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
@endsection
