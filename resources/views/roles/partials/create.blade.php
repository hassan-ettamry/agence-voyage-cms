<x-ui.modal id="createRoleModal" title="Create new role" width="760px">
    <form method="POST" action="{{ route('roles.store', ['module' => $selectedModule]) }}" class="space-y-6">
        @csrf

        <div>
            <label for="create-role-name" class="mb-1.5 block text-sm font-medium text-slate-700">
                Role name
            </label>
            <input
                id="create-role-name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Manager, Content editor..."
                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                required
            >
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            @error('slug')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="mb-3 flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Permissions</h3>
                    <p class="text-xs text-slate-400">Choose what this role can access in each module.</p>
                </div>
            </div>

            @error('permissions')
                <p class="mb-2 text-xs text-red-600">{{ $message }}</p>
            @enderror

            <div class="grid gap-3 md:grid-cols-2">
                @foreach($permissionsByModule as $module => $permissions)
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-4">
                        <div class="mb-3 flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-xs font-bold text-indigo-600">
                                {{ strtoupper(substr($module, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-slate-800">
                                {{ \Illuminate\Support\Str::headline($module) }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach($permissions as $permission)
                                <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600">
                                    <input
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->id }}"
                                        @checked(in_array($permission->id, old('permissions', []), true))
                                        class="h-3.5 w-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    >
                                    {{ $permission->action_label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-1">
            <button
                type="button"
                onclick="closeModal('createRoleModal')"
                class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
            >
                Create role
            </button>
        </div>
    </form>
</x-ui.modal>
