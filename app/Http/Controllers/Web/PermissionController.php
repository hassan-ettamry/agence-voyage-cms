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
        return view('permissions.create');
    }

    /**
     * Store permission
     */
    public function store(Request $request)
    {
        Permission::create($request->all());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update permission
     */
    public function update(Request $request, Permission $permission)
    {
        $permission->update($request->all());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    /**
     * Delete permission
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
}