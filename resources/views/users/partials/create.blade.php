{{-- ================= MODAL ================= --}}
<x-ui.modal id="createUserModal" title="Create new user">

<form method="POST" action="{{ route('users.store') }}">
    @csrf

    <div class="mb-4">
        <label class="text-sm text-gray-600">Name</label>
        <input name="name" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">Email</label>
        <input name="email" type="email" class="w-full border rounded-lg px-3 py-2 mt-1">
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">Password</label>
        <input name="password" type="password" class="w-full border rounded-lg px-3 py-2 mt-1">
        <p class="mt-1 text-xs text-gray-500">At least 8 characters with uppercase, lowercase, number and symbol.</p>
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">Role</label>
        <select name="role_id" class="w-full border rounded-lg px-3 py-2 mt-1">
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex justify-end gap-2">
        <button type="button" onclick="closeModal('createUserModal')" class="px-4 py-2 border rounded-lg">
            Cancel
        </button>

        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
            Create
        </button>
    </div>

</form>

</x-ui.modal>
