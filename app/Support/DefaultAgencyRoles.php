<?php

namespace App\Support;

use App\Models\Agency;
use App\Models\Permission;
use App\Models\Role;

class DefaultAgencyRoles
{
    public static function ensureForAgency(Agency $agency): Role
    {
        $permissions = Permission::pluck('id', 'slug');

        $admin = self::ensureRole(
            $agency,
            'Admin',
            'admin',
            $permissions->values()->all()
        );

        self::ensureRole(
            $agency,
            'Editor',
            'editor',
            $permissions->only([
                'page.view',
                'page.create',
                'page.update',
                'destination.view',
                'destination.create',
                'destination.update',
                'offer.view',
                'offer.create',
                'offer.update',
                'media.view',
                'media.upload',
                'media.update',
                'theme.view',
            ])->values()->all()
        );

        self::ensureRole(
            $agency,
            'Viewer',
            'viewer',
            $permissions->only([
                'page.view',
                'destination.view',
                'offer.view',
                'media.view',
            ])->values()->all()
        );

        return $admin;
    }

    private static function ensureRole(Agency $agency, string $name, string $slug, array $permissionIds): Role
    {
        $role = Role::withoutGlobalScopes()->updateOrCreate(
            [
                'agency_id' => $agency->id,
                'slug' => $slug,
            ],
            [
                'name' => $name,
            ]
        );

        $role->permissions()->sync($permissionIds);

        return $role;
    }
}
