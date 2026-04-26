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
        ];

        foreach ($permissions as $perm) {

            Permission::updateOrCreate(
                ['slug' => $perm['slug']], // unique
                [
                    'id' => Str::uuid(),
                    'name' => $perm['name'],
                ]
            );
        }
    }
}