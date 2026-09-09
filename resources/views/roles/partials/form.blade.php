@php
    $editing = isset($role);
    $selectedPermissions = old(
        'permissions',
        $editing ? $role->permissions->pluck('id')->all() : []
    );
@endphp

<form method="POST"
      action="{{ $editing ? route('roles.update', $role) : route('roles.store') }}"
      class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">
                {{ $editing ? 'Role information' : 'Create a role' }}
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Set the role name and choose the actions its users can perform.
            </p>
        </div>

        <div class="space-y-6 px-6 py-6">
            <div>
                <label for="role-name" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Role name
                </label>
                <input
                    id="role-name"
                    name="name"
                    value="{{ old('name', $role->name ?? '') }}"
                    placeholder="Manager, Content editor..."
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-950 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                    required
                    autofocus
                >
                @if($editing && $role->slug === 'admin')
                    <p class="mt-1.5 text-xs text-amber-700">
                        This is the system administrator role. Its protected slug will not change.
                    </p>
                @endif
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-3">
                    <h3 class="text-sm font-semibold text-slate-800">Permissions</h3>
                    <p class="mt-1 text-xs text-slate-500">Permissions are grouped by platform module.</p>
                </div>

                @error('permissions')
                    <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($permissionsByModule as $module => $permissions)
                        <fieldset class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                            <legend class="px-1 text-sm font-semibold text-slate-800">
                                {{ \Illuminate\Support\Str::headline($module) }}
                            </legend>

                            <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                @foreach($permissions as $permission)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 transition hover:border-indigo-300">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            @checked(in_array($permission->id, $selectedPermissions, true))
                                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                        >
                                        <span>{{ $permission->action_label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 md:col-span-2">
                            No permissions are available yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('roles.index') }}"
           class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            Cancel
        </a>
        <button type="submit"
                class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
            {{ $editing ? 'Save changes' : 'Create role' }}
        </button>
    </div>
</form>
