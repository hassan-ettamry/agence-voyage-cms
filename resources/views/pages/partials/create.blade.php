<x-ui.modal id="createPageModal" title="Create new page">

<form method="POST" action="{{ route('pages.store') }}">
    @csrf

    <div class="mb-4">
        <label class="text-sm text-gray-600">Page name</label>
        <input name="title" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">Menu</label>
        <select name="menu" class="w-full border rounded-lg px-3 py-2 mt-1">
            <option>Primary Menu</option>
        </select>
    </div>

    <div class="flex justify-end gap-2">
        <button type="button"
                onclick="closeModal('createPageModal')"
                class="px-4 py-2 border rounded-lg">
            Cancel
        </button>

        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
            Create
        </button>
    </div>

</form>

</x-ui.modal>