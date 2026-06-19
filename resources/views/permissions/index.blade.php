@extends('layouts.admin')

@section('topbar')

<x-layout.topbar>

    <x-slot name="right">
        <button type="button" onclick="openModal('createPermissionModal')">
            + New Permission
        </button>
    </x-slot>

</x-layout.topbar>

@endsection

@section('content')

{{-- ================= TABLE ================= --}}
<x-ui.table-layout>

    {{-- TITLE --}}
    <x-slot name="title">
        <span class="text-sm font-semibold text-gray-800">Permissions</span>
        <span class="text-xs text-gray-400">
            ({{ $permissionModules->total() }} modules)
        </span>
    </x-slot>

    {{-- ACTIONS --}}
    <x-slot name="actions">
        <button class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
            Export
        </button>

        <button type="button"
                onclick="openModal('createPermissionModal')"
                class="px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
            + New Permission
        </button>
    </x-slot>

    {{-- TABLE --}}
    <x-slot name="table">

        <x-ui.data-table>

            {{-- HEAD --}}
            <x-slot name="head">
                <tr>
                    <th class="w-[28%] px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Module</th>
                    <th class="w-[42%] px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Permissions</th>
                    <th class="w-[12%] px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Enabled</th>
                    <th class="w-[10%] px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase">Updated</th>
                    <th class="w-[8%] px-6 py-3 text-right text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </x-slot>

            {{-- BODY --}}
            <x-slot name="body">
                @forelse($permissionModules as $module)
                    @php
                        $initial = strtoupper(substr($module['label'], 0, 1));
                        $avatarColors = ['bg-indigo-500','bg-violet-500','bg-emerald-500','bg-sky-500','bg-rose-500','bg-amber-500','bg-teal-500','bg-pink-500'];
                        $avatarColor = $avatarColors[crc32($module['key']) % count($avatarColors)];
                        $enabledActions = collect($module['actions'])->where('exists', true)->values();
                        $totalActions = count($module['actions']);
                        $modalId = 'editPermissionModule-' . $module['key'];
                        $actionColors = [
                            'View' => 'bg-blue-50 text-blue-600 ring-1 ring-blue-200',
                            'Create' => 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200',
                            'Update' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-200',
                            'Delete' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-200',
                        ];
                    @endphp

                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 {{ $avatarColor }} rounded-lg flex items-center justify-center text-white text-sm font-bold">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm">{{ $module['label'] }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ $module['key'] }}</div>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 align-middle">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($enabledActions as $action)
                                    <span class="px-2 py-0.5 text-xs rounded-md font-medium {{ $actionColors[$action['action_label']] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $action['action_label'] }}
                                    </span>
                                @endforeach
                            </div>
                        </td>

                        <td class="px-6 py-4 align-middle">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                {{ $enabledActions->count() }} / {{ $totalActions }}
                            </span>
                        </td>

                        <td class="px-6 py-4 align-middle text-xs text-gray-400 whitespace-nowrap">
                            {{ $module['updatedAt'] ? $module['updatedAt']->format('Y-m-d') : '-' }}
                        </td>

                        <td class="px-6 py-4 align-middle">
                            <div class="flex justify-end">
                                <button
                                    type="button"
                                    onclick="openModal('{{ $modalId }}')"
                                    title="Edit module permissions"
                                    class="w-7 h-7 flex items-center justify-center border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-md text-gray-400 hover:text-indigo-600 transition-colors">
                                    <x-layout.icon name="edit"/>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                            No permission modules exist yet.
                        </td>
                    </tr>
                @endforelse
            </x-slot>

        </x-ui.data-table>

    </x-slot>

    {{-- FOOTER --}}
    <x-slot name="footer">
        <x-ui.pagination :paginator="$permissionModules" />
    </x-slot>

</x-ui.table-layout>

@include('permissions.partials.create')
@include('permissions.partials.edit-modules')

@endsection

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(old('module_key'))
            openModal('editPermissionModule-{{ old('module_key') }}');
        @else
            openModal('createPermissionModal');
        @endif
    });
</script>
@endif
