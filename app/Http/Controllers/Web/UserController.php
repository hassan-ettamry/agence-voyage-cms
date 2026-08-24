<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\SecurityEventLogger;
use App\Services\UserIndexService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private UserService $service,
        private UserIndexService $indexService,
        private SecurityEventLogger $securityLogger
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        return view('users.index', $this->indexService->build($request->query(), $request->user()));
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        $createdUser = $this->service->create($request->validated(), $request->user());
        $this->securityLogger->record('admin.user_created', $request->user(), $request, [
            'target_user_id' => $createdUser->id,
        ]);

        return back()->with('success', 'User created');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $this->service->update($user, $request->validated());
        $this->securityLogger->record('admin.user_updated', $request->user(), $request, [
            'target_user_id' => $user->id,
        ]);

        return back()->with('success', 'User updated');
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        $targetUserId = $user->id;
        $this->service->delete($user);
        $this->securityLogger->record('admin.user_deleted', $request->user(), $request, [
            'target_user_id' => $targetUserId,
        ]);

        return back()->with('success', 'User deleted');
    }
}
