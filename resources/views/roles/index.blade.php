@extends('layouts.admin')

@section('topbar')

<x-layout.topbar>

    <x-slot name="right">
        <button onclick="openModal('createRoleModal')"
                class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm shadow-sm">
            + New Role
        </button>
    </x-slot>

</x-layout.topbar>

@endsection

@section('content')

{{-- ================= STATS ================= --}}
<x-ui.stats-cards :stats="$stats" />

{{-- ================= TABLE ================= --}}
<x-ui.table-layout>

    {{-- TITLE --}}
    <x-slot name="title">
        <span class="text-sm font-semibold text-gray-800">Roles</span>
        <span class="text-xs text-gray-400">
            ({{ $roles->count() }} records)
        </span>
    </x-slot>

    {{-- ACTIONS --}}
    <x-slot name="actions">
        <button class="px-3 py-1.5 text-xs bg-gray-100 rounded-lg hover:bg-gray-200">
            Export
        </button>
    </x-slot>

    {{-- TABLE --}}
    <x-slot name="table">

        <x-ui.data-table>

            {{-- HEAD --}}
            <x-slot name="head">
                <tr class="bg-gray-50/80 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Users</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Module</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Permissions</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </x-slot>

            {{-- BODY --}}
            <x-slot name="body">

                @foreach($roles as $role)

                    @php
                        $grouped = collect($role->permissions)->groupBy(function($perm) {
                            return ucfirst(explode(' ', strtolower($perm->name))[1] ?? 'Other');
                        });
                        $first = true;
                    @endphp

                    @foreach($grouped as $module => $perms)

                        <tr class="hover:bg-gray-50/60 transition-colors duration-150 border-b border-gray-100 last:border-0">

                            @if($first)

                                {{-- ROLE (rowspan) --}}
                                <td class="px-6 py-4 align-top text-center" rowspan="{{ $grouped->count() }}">
                                    <div class="flex items-center gap-3">

                                        <div class="w-9 h-9 bg-indigo-500 rounded-lg flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                            {{ strtoupper(substr($role->name, 0, 1)) }}
                                        </div>

                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-gray-800 text-sm leading-tight">
                                                    {{ $role->name }}
                                                </span>

                                                @if($role->slug === 'admin')
                                                    <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-600 rounded font-medium">
                                                        System
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ $role->slug }}
                                            </div>
                                        </div>

                                    </div>
                                </td>

                                {{-- USERS (rowspan) --}}
                                <td class="px-6 py-4 align-top text-sm text-gray-600" rowspan="{{ $grouped->count() }}">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                        </svg>
                                        <span class="font-medium text-gray-700">{{ $role->users->count() }}</span>
                                    </div>
                                </td>

                            @endif

                            {{-- MODULE --}}
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="font-medium">{{ $module }}</span>
                            </td>

                            {{-- PERMISSIONS --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 flex-wrap">

                                    @foreach($perms as $perm)

                                        @php
                                            $action = ucfirst(explode(' ', strtolower($perm->name))[0]);

                                            $colors = [
                                                'View'   => 'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
                                                'Create' => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200',
                                                'Update' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-200',
                                                'Delete' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-200',
                                            ];
                                        @endphp

                                        <span class="px-2 py-0.5 text-xs rounded-md font-medium {{ $colors[$action] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $action }}
                                        </span>

                                    @endforeach

                                </div>
                            </td>

                            @if($first)

                                {{-- CREATED --}}
                                <td class="px-6 py-4 align-top text-xs text-gray-400 whitespace-nowrap" rowspan="{{ $grouped->count() }}">
                                    {{ \Carbon\Carbon::parse($role->created_at)->format('Y-m-d') }}
                                </td>

                                {{-- ACTIONS --}}
                                <td class="px-6 py-4 align-top" rowspan="{{ $grouped->count() }}">
                                    <x-ui.actions
                                        :edit="route('roles.edit', $role)"
                                        :delete="route('roles.destroy', $role)"
                                        confirm="Delete this role?"
                                    />
                                </td>

                                @php $first = false; @endphp

                            @endif

                        </tr>

                    @endforeach

                @endforeach

            </x-slot>

        </x-ui.data-table>

    </x-slot>

    {{-- FOOTER --}}
    <x-slot name="footer">
        <div class="text-xs text-gray-400">
            Total roles: {{ $roles->count() }}
        </div>
    </x-slot>

</x-ui.table-layout>

@endsection