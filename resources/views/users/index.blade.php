@extends('layouts.admin')

@section('topbar')

<x-layout.topbar searchPlaceholder="Search users...">

    {{-- LEFT SIDE (garde même style que pages) --}}
    <x-slot name="left">
        <a href="{{ url('/') }}" target="_blank" rel="noopener">
            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="11" cy="12" r="7"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h14M11 5c2 2 3 4.3 3 7s-1 5-3 7M11 5c-2 2-3 4.3-3 7s1 5 3 7M16 5h3v3M19 5l-5 5"/>
            </svg>
            Visit site
        </a>
    </x-slot>

    {{-- RIGHT SIDE --}}
    <x-slot name="right">
        <button type="button" onclick="openModal('createUserModal')">
            + New User
        </button>
    </x-slot>

</x-layout.topbar>

@endsection


@section('content')

{{-- ================= STATS ================= --}}
<x-ui.stats-cards :stats="$stats" />


{{-- ================= TABLE ================= --}}
<x-ui.table-layout>

    {{-- TITLE (IDENTIQUE) --}}
    <x-slot name="title">
        <span class="text-sm font-semibold text-gray-800">All Users</span>
        <span class="text-xs text-gray-400">
            ({{ $users->total() }} records)
        </span>
    </x-slot>

    {{-- ACTIONS --}}
    <x-slot name="actions">

        <x-ui.filter-tabs :filters="[
            'all' => 'All',
            'admin' => 'Admins',
            'users' => 'Users'
        ]" />

        <button class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition flex items-center gap-1.5">
            Export
        </button>

        <button type="button"
                onclick="openModal('createUserModal')"
                class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition flex items-center gap-1.5">
            + New User
        </button>

    </x-slot>

    {{-- TABLE --}}
    <x-slot name="table">

        <x-ui.data-table>

            <x-slot name="head">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">User</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Role</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Created</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </x-slot>

            <x-slot name="body">
                @foreach($users as $user)

                @php
                    $initial = strtoupper(substr($user->name, 0, 1));
                    $colors = ['bg-indigo-500','bg-violet-500','bg-emerald-500','bg-sky-500','bg-rose-500'];
                    $color = $colors[crc32($user->name) % count($colors)];
                @endphp

                <tr class="hover:bg-gray-50 transition user-row"
                    data-filter-value="{{ $user->role?->slug === 'admin' ? 'admin' : 'users' }}">

                    {{-- ID --}}
                    <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">
                        {{ $user->id }}
                    </td>

                    {{-- USER --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 {{ $color }} rounded-md flex items-center justify-center text-white text-xs font-bold">
                                {{ $initial }}
                            </div>
                            <span class="font-semibold text-gray-800 text-sm">
                                {{ $user->name }}
                            </span>
                        </div>
                    </td>

                    {{-- EMAIL --}}
                    <td class="px-5 py-3.5 text-xs text-gray-500">
                        {{ $user->email }}
                    </td>

                    {{-- ROLE --}}
                    <td class="px-5 py-3.5">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-md bg-indigo-100 text-indigo-600 uppercase">
                            {{ $user->role?->name ?? '—' }}
                        </span>
                    </td>

                    {{-- CREATED --}}
                    <td class="px-5 py-3.5 text-xs text-gray-400">
                        {{ $user->created_at->format('Y-m-d H:i') }}
                    </td>

                    {{-- ACTIONS --}}
                    <td class="px-5 py-3.5">
                        <x-ui.actions
                            :edit="route('users.edit', $user)"
                            :delete="route('users.destroy', $user)"
                            :view="'#'"
                            confirm="Delete this user?"
                        />
                    </td>

                </tr>

                @endforeach
            </x-slot>

        </x-ui.data-table>

    </x-slot>

    {{-- FOOTER --}}
    <x-slot name="footer">
        <x-ui.pagination :paginator="$users" />
    </x-slot>

</x-ui.table-layout>


{{-- ================= MODAL ================= --}}
@include('users.partials.create')

@endsection
