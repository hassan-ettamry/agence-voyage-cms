<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    public function __construct(private UserService $service) {}

    public function index()
    {
        $users = User::with('role')->paginate(10);

        // Stats
        $stats = [
            [
                'label' => 'Total Users',
                'value' => $users->total(),
                'note'  => '↑ Active users',
                'color' => 'text-emerald-500',
            ],
            [
                'label' => 'Admins',
                'value' => User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->count(),
                'note'  => 'System admins',
            ],
            [
                'label' => 'Roles',
                'value' => Role::count(),
                'note'  => 'Defined roles',
            ],
            [
                'label' => 'Permissions',
                'value' => '—',
                'note'  => 'System level',
            ],
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->service->create($request->validated());

        return back()->with('success', 'User created');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->service->update($user, $request->validated());

        return back()->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return back()->with('success', 'User deleted');
    }
}