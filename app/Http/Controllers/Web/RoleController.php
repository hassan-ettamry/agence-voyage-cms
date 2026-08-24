<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\RoleIndexService;
use App\Services\SecurityEventLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct(
        private RoleIndexService $indexService,
        private SecurityEventLogger $securityLogger
    ) {}

    /**
     * Display roles list
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        return view('roles.index', $this->indexService->build($request->query(), $request->user()));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Role::class);

        return view('roles.create');
    }

    /**
     * Store role
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'id')],
        ]);

        $slug = Str::slug($validated['name']);

        if (Role::withoutGlobalScopes()
            ->where('agency_id', $request->user()->agency_id)
            ->where('slug', $slug)
            ->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' => 'A role with this name already exists.',
                ]);
        }

        $role = Role::create([
            'agency_id' => $request->user()->agency_id,
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);
        $this->securityLogger->record('admin.role_created', $request->user(), $request, [
            'target_role_id' => $role->id,
        ]);

        return redirect()->route('roles.index', array_filter([
            'module' => $request->query('module'),
        ]))
            ->with('success', 'Role created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        return view('roles.edit', compact('role'));
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'id')],
        ]);

        $slug = $role->slug === 'admin'
            ? 'admin'
            : Str::slug($validated['name']);

        if (Role::withoutGlobalScopes()
            ->where('agency_id', $request->user()->agency_id)
            ->where('slug', $slug)
            ->whereKeyNot($role->getKey())
            ->exists()) {
            return back()
                ->withInput()
                ->withErrors(['name' => 'A role with this name already exists.']);
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        if (array_key_exists('permissions', $validated)) {
            $role->syncPermissions($validated['permissions'] ?? []);
        }
        $this->securityLogger->record('admin.role_updated', $request->user(), $request, [
            'target_role_id' => $role->id,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Delete role
     */
    public function destroy(Request $request, Role $role)
    {
        $this->authorize('delete', $role);

        $targetRoleId = $role->id;
        $role->delete();
        $this->securityLogger->record('admin.role_deleted', $request->user(), $request, [
            'target_role_id' => $targetRoleId,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
