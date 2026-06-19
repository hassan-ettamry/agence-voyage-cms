<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Pages', 'slug' => 'page.view'],
            ['name' => 'Create Page', 'slug' => 'page.create'],
            ['name' => 'Update Page', 'slug' => 'page.update'],
            ['name' => 'Delete Page', 'slug' => 'page.delete'],
            ['name' => 'View Users', 'slug' => 'user.view'],
            ['name' => 'Create User', 'slug' => 'user.create'],
            ['name' => 'Update User', 'slug' => 'user.update'],
            ['name' => 'Delete User', 'slug' => 'user.delete'],
            ['name' => 'View Roles', 'slug' => 'role.view'],
            ['name' => 'Create Role', 'slug' => 'role.create'],
            ['name' => 'Update Role', 'slug' => 'role.update'],
            ['name' => 'Delete Role', 'slug' => 'role.delete'],
            ['name' => 'View Permissions', 'slug' => 'permission.view'],
            ['name' => 'Create Permission', 'slug' => 'permission.create'],
            ['name' => 'Update Permission', 'slug' => 'permission.update'],
            ['name' => 'Delete Permission', 'slug' => 'permission.delete'],
            ['name' => 'View Destinations', 'slug' => 'destination.view'],
            ['name' => 'Create Destination', 'slug' => 'destination.create'],
            ['name' => 'Update Destination', 'slug' => 'destination.update'],
            ['name' => 'Delete Destination', 'slug' => 'destination.delete'],
            ['name' => 'View Offers', 'slug' => 'offer.view'],
            ['name' => 'Create Offer', 'slug' => 'offer.create'],
            ['name' => 'Update Offer', 'slug' => 'offer.update'],
            ['name' => 'Delete Offer', 'slug' => 'offer.delete'],
            ['name' => 'View Media', 'slug' => 'media.view'],
            ['name' => 'Upload Media', 'slug' => 'media.upload'],
            ['name' => 'Update Media', 'slug' => 'media.update'],
            ['name' => 'Delete Media', 'slug' => 'media.delete'],
            ['name' => 'View Themes', 'slug' => 'theme.view'],
            ['name' => 'Apply Theme', 'slug' => 'theme.apply'],
            ['name' => 'Update Theme', 'slug' => 'theme.update'],
        ];

        foreach ($permissions as $perm) {

            Permission::updateOrCreate(
                ['slug' => $perm['slug']], // unique
                [
                    'name' => $perm['name'],
                ]
            );
        }
    }
}
