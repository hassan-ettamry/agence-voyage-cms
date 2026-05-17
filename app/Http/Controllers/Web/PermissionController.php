<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display permissions list
     */
    public function index()
    {
        $this->authorize('viewAny', Permission::class);

        $permissions = Permission::paginate(10);
    
        $stats = [
            [
                'label' => 'Total Permissions',
                'value' => $permissions->total(),
                'icon' => 'shield'
            ]
        ];
    
        return view('permissions.index', compact('permissions', 'stats'));
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

        Permission::create($request->all());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully');
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
