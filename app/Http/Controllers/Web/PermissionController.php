<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\PermissionCatalogService;
use App\Services\PermissionIndexService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionIndexService $indexService,
        private PermissionCatalogService $permissionCatalog
    ) {}

    /**
     * Display permissions list
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);

        return view('permissions.index', $this->indexService->build($request->query(), $request->user()));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $this->authorize('create', Permission::class);

        return view('permissions.create');
    }

    /**
     * Store permission
     */
    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);

        $validated = $request->validate([
            'modules' => ['required', 'array', 'min:1'],
            'modules.*' => ['required', 'string', 'distinct'],
        ]);

        $createdModules = $this->permissionCatalog->createModules($validated['modules']);

        return redirect()->route('permissions.index')
            ->with('success', count($createdModules).' module(s) created successfully');
    }

    public function updateModule(Request $request, string $module)
    {
        $this->authorize('updateAny', Permission::class);

        $validated = $request->validate([
            'actions' => ['nullable', 'array'],
            'actions.*' => ['required', 'string', 'distinct'],
        ]);

        $actions = $validated['actions'] ?? [];

        $this->permissionCatalog->syncModule($module, $actions);

        return redirect()->route('permissions.index')
            ->with('success', 'Module permissions updated successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Permission $permission)
    {
        $this->authorize('update', $permission);

        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update permission
     */
    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $permission->update($request->all());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    /**
     * Delete permission
     */
    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}
