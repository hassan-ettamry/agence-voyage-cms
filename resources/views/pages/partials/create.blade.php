<x-ui.modal id="createPageModal" title="Create new page" width="720px">
    <form method="POST" action="{{ route('pages.store') }}" class="space-y-6">
        @csrf

        <div class="grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="create-page-title" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Page title
                </label>
                <input
                    id="create-page-title"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Home, About us, Destination details..."
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                    required
                >
                @error('title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="create-page-slug" class="mb-1.5 block text-sm font-medium text-slate-700">
                    URL slug
                </label>
                <input
                    id="create-page-slug"
                    name="slug"
                    value="{{ old('slug') }}"
                    placeholder="auto-generated if empty"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >
                @error('slug')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="create-page-status" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Status
                </label>
                <select
                    id="create-page-status"
                    name="status"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >
                    <option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option>
                    <option value="published" @selected(old('status') === 'published')>Published</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="create-page-menu" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Menu
                </label>
                <select
                    id="create-page-menu"
                    name="menu_selection"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >
                    <option value="none" @selected(old('menu_selection', 'none') === 'none')>No menu</option>
                    <option value="default" @selected(old('menu_selection') === 'default')>Default menu</option>
                    @foreach($menus ?? [] as $menu)
                        <option value="{{ $menu['id'] }}" @selected(old('menu_selection') === $menu['id'])>
                            {{ $menu['name'] }}{{ $menu['is_default'] ? ' (default)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('menu_selection')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="create-page-meta-title" class="mb-1.5 block text-sm font-medium text-slate-700">
                    SEO title
                </label>
                <input
                    id="create-page-meta-title"
                    name="meta_title"
                    value="{{ old('meta_title') }}"
                    maxlength="60"
                    placeholder="Used for search results"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >
                @error('meta_title')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="create-page-meta-description" class="mb-1.5 block text-sm font-medium text-slate-700">
                    SEO description
                </label>
                <input
                    id="create-page-meta-description"
                    name="meta_description"
                    value="{{ old('meta_description') }}"
                    maxlength="160"
                    placeholder="Short page summary"
                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >
                @error('meta_description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-600">
            A blank page will be created in draft mode. You can open the builder right after creation and start adding sections.
        </div>

        <div class="flex items-center justify-end gap-2 pt-1">
            <button
                type="button"
                onclick="closeModal('createPageModal')"
                class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
            >
                Create page
            </button>
        </div>
    </form>
</x-ui.modal>
