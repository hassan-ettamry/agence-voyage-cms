<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserIndexService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $service,
        private UserIndexService $indexService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        return view('users.index', $this->indexService->build($request->query(), $request->user()));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $this->service->create($request->validated(), $request->user());

        return back()->with('success', 'User created');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $this->service->update($user, $request->validated());

        return back()->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->service->delete($user);

        return back()->with('success', 'User deleted');
    }
}
