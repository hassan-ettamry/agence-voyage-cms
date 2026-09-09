@foreach($permissionModules as $module)
    @php
        $modalId = 'editPermissionModule-' . $module['key'];
    @endphp

    <x-ui.modal id="{{ $modalId }}" title="Edit {{ $module['label'] }} permissions" width="620px">
        <form method="POST" action="{{ route('permissions.modules.update', $module['key']) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="module_key" value="{{ $module['key'] }}">

            <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-900">{{ $module['label'] }}</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        Checked actions exist in the permissions table. Uncheck an action to remove it.
                    </p>
                </div>

                @error('actions')
                    <p class="mt-3 text-xs text-rose-600">{{ $message }}</p>
                @enderror

                <div class="mt-4 grid gap-2">
                    @foreach($module['actions'] as $action)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50/40">
                            <input
                                type="checkbox"
                                name="actions[]"
                                value="{{ $action['action'] }}"
                                @checked(in_array($action['action'], old('module_key') === $module['key'] ? old('actions', []) : $module['existingActions'], true))
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span class="flex-1">
                                <span class="block font-medium text-slate-900">
                                    {{ $action['name'] }}
                                </span>
                                <span class="block text-xs text-slate-500 font-mono">
                                    {{ $action['slug'] }}
                                </span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button
                    type="button"
                    onclick="closeModal('{{ $modalId }}')"
                    class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Cancel
                </button>

                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                    Save permissions
                </button>
            </div>
        </form>
    </x-ui.modal>
@endforeach
