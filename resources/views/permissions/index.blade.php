@extends('layouts.admin')

@section('topbar')

<x-layout.topbar>

    <x-slot name="right">
        <button onclick="openModal('createPermissionModal')"
                class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm shadow-sm">
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
            ({{ $permissions->total() }} records)
        </span>
    </x-slot>

    {{-- ACTIONS (obligatoire pour ton component) --}}
    <x-slot name="actions">
        <button class="px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
            Export
        </button>
    </x-slot>

    {{-- TABLE --}}
    <x-slot name="table">

        <x-ui.data-table>

            {{-- HEAD --}}
            <x-slot name="head">
                <tr>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase">ID</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Module</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Action</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase">Created</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </x-slot>

            {{-- BODY --}}
            <x-slot name="body">
                @foreach($permissions as $permission)

                @php
                    // Parse permission (ex: "view pages")
                    $parts = explode(' ', strtolower($permission->name));
                    $action = ucfirst($parts[0] ?? '');
                    $module = ucfirst($parts[1] ?? '');

                    // Action colors
                    $actionColors = [
                        'View' => 'bg-blue-100 text-blue-600',
                        'Create' => 'bg-emerald-100 text-emerald-600',
                        'Update' => 'bg-amber-100 text-amber-600',
                        'Delete' => 'bg-rose-100 text-rose-600',
                    ];

                    $actionColor = $actionColors[$action] ?? 'bg-gray-100 text-gray-600';

                    // Avatar color (module)
                    $avatarColors = ['bg-indigo-500','bg-violet-500','bg-sky-500','bg-teal-500','bg-pink-500'];
                    $avatarColor = $avatarColors[crc32($module) % count($avatarColors)];

                    $initial = strtoupper(substr($module, 0, 1));
                @endphp

                <tr class="hover:bg-gray-50 transition">

                    {{-- ID --}}
                    <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">
                        {{ $permission->id }}
                    </td>

                    {{-- MODULE --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 {{ $avatarColor }} rounded-md flex items-center justify-center text-white text-xs font-bold">
                                {{ $initial }}
                            </div>
                            <span class="font-semibold text-gray-800 text-sm">
                                {{ $module }}
                            </span>
                        </div>
                    </td>

                    {{-- ACTION --}}
                    <td class="px-5 py-3.5">
                        <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-md {{ $actionColor }}">
                            {{ $action }}
                        </span>
                    </td>

                    {{-- CREATED --}}
                    <td class="px-5 py-3.5 text-xs text-gray-400 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($permission->created_at)->format('Y-m-d H:i') }}
                    </td>

                    {{-- ACTIONS --}}
                    <td class="px-5 py-3.5">
                        <x-ui.actions
                            :edit="route('permissions.edit', $permission)"
                            :delete="route('permissions.destroy', $permission)"
                            confirm="Delete this permission?"
                        />
                    </td>

                </tr>
                @endforeach
            </x-slot>

        </x-ui.data-table>

    </x-slot>

    {{-- FOOTER --}}
    <x-slot name="footer">
        <x-ui.pagination :paginator="$permissions" />
    </x-slot>

</x-ui.table-layout>

@endsection