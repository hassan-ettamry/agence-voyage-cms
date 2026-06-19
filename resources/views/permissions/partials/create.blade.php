<x-ui.modal id="createPermissionModal" title="Create permission module" width="760px">
    <form method="POST" action="{{ route('permissions.store') }}" class="space-y-5">
        @csrf

        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Missing modules</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Add complete permission sets only for the v1 business modules that do not exist yet.
                </p>
            </div>

            @error('modules')
                <p class="mt-3 text-xs text-rose-600">{{ $message }}</p>
            @enderror

            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @forelse($newPermissionModules as $module)
                    <label class="cursor-pointer rounded-xl border border-slate-200 bg-white p-4 transition hover:border-indigo-200 hover:bg-indigo-50/40">
                        <div class="flex items-start gap-3">
                            <input
                                type="checkbox"
                                name="modules[]"
                                value="{{ $module['key'] }}"
                                @checked(in_array($module['key'], old('modules', []), true))
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span class="flex-1">
                                <span class="block text-sm font-semibold text-slate-900">{{ $module['label'] }}</span>
                                <span class="mt-2 flex flex-wrap gap-1.5">
                                    @foreach($module['permissions'] as $permission)
                                        <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                            {{ $permission['action_label'] }}
                                        </span>
                                    @endforeach
                                </span>
                            </span>
                        </div>
                    </label>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-200 bg-white p-8 text-center">
                        <div class="text-sm font-medium text-slate-900">All v1 modules already exist.</div>
                        <div class="mt-1 text-xs text-slate-500">Use edit on a module row to adjust its permissions.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <button
                type="button"
                onclick="closeModal('createPermissionModal')"
                class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                Cancel
            </button>

            <button
                type="submit"
                @disabled(empty($newPermissionModules))
                class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                Create selected modules
            </button>
        </div>
    </form>
</x-ui.modal>
