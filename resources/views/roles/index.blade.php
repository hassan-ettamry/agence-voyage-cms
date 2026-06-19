@extends('layouts.admin')

@section('topbar')

<x-layout.topbar>

    <x-slot name="right">
        <button type="button" onclick="openModal('createRoleModal')">
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
            ({{ $roles->total() }} records)
        </span>
    </x-slot>

    {{-- ACTIONS --}}
    <x-slot name="actions">
        @if(! empty($moduleFilters))
            <x-ui.filter-tabs
                :filters="$moduleFilters"
                :current="$selectedModule"
                query="module"
            />
        @endif

        <button class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
            Export
        </button>

        <button type="button"
                onclick="openModal('createRoleModal')"
                class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
            + New Role
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

                @forelse($roles as $role)

                    <tr class="hover:bg-gray-50/60 transition-colors duration-150 border-b border-gray-100 last:border-0">

                        {{-- ROLE --}}
                        <td class="px-6 py-4 align-middle">
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

                        {{-- USERS --}}
                        <td class="px-6 py-4 align-middle text-sm text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                <span class="font-medium text-gray-700">{{ $role->users->count() }}</span>
                            </div>
                        </td>

                        {{-- MODULE --}}
                        <td class="px-6 py-4 align-middle text-sm text-gray-700">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                {{ $selectedModuleLabel ?? 'Module' }}
                            </span>
                        </td>

                        {{-- PERMISSIONS --}}
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-1.5 flex-wrap">

                                @forelse($role->visible_permissions as $perm)

                                    @php
                                        $action = $perm->action_label;

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

                                @empty
                                    <span class="text-xs text-gray-400">No permission</span>
                                @endforelse

                            </div>
                        </td>

                        {{-- CREATED --}}
                        <td class="px-6 py-4 align-middle text-xs text-gray-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($role->created_at)->format('Y-m-d') }}
                        </td>

                        {{-- ACTIONS --}}
                        <td class="px-6 py-4 align-middle">
                            <x-ui.actions
                                :edit="route('roles.edit', $role)"
                                :delete="route('roles.destroy', $role)"
                                align="right"
                                confirm="Delete this role?"
                            />
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">
                            No roles have permissions for this module.
                        </td>
                    </tr>
                @endforelse

            </x-slot>

        </x-ui.data-table>

    </x-slot>

    {{-- FOOTER --}}
    <x-slot name="footer">
        <x-ui.pagination :paginator="$roles" />
    </x-slot>

</x-ui.table-layout>

{{-- ================= MODAL ================= --}}
@include('roles.partials.create')

@endsection

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal('createRoleModal');
    });
</script>
@endif
