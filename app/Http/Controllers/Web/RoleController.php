<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display roles list
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->get();
    
        $stats = [
            [
                'label' => 'Total Roles',
                'value' => $roles->count(),
            ],
            [
                'label' => 'Total Users',
                'value' => \App\Models\User::count(),
            ],
            [
                'label' => 'Permissions',
                'value' => \App\Models\Permission::count(),
            ],
        ];
    
        return view('roles.index', compact('roles', 'stats'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store role
     */
    public function store(Request $request)
    {
        Role::create($request->all());

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update role
     */
    public function update(Request $request, Role $role)
    {
        $role->update($request->all());

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Delete role
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}