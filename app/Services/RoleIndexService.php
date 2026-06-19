<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class RoleIndexService
{
    private const PER_PAGE_OPTIONS = [8, 16, 32, 64];

    public function build(array $filters, User $user): array
    {
        $allRoles = Role::with(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        $modules = Permission::query()
            ->select('slug')
            ->orderBy('slug')
            ->get()
            ->pluck('module_key')
            ->unique()
            ->sort()
            ->values();

        $permissionsByModule = Permission::query()
            ->orderBy('slug')
            ->get()
            ->groupBy('module_key');

        $selectedModule = $this->stringFilter($filters, 'module');

        if (! $modules->contains($selectedModule)) {
            $selectedModule = $modules->first();
        }

        $filteredRoles = $allRoles
            ->map(function (Role $role) use ($selectedModule) {
                $permissions = $role->permissions
                    ->filter(function (Permission $permission) use ($selectedModule) {
                        return $selectedModule === null || $permission->module_key === $selectedModule;
                    })
                    ->values();

                $role->setRelation('visible_permissions', $permissions);

                return $role;
            })
            ->filter(function (Role $role) use ($selectedModule) {
                return $selectedModule === null || $role->visible_permissions->isNotEmpty();
            })
            ->values();

        $roles = $this->paginate($filteredRoles, $filters);

        return [
            'roles' => $roles,
            'stats' => [
                [
                    'label' => 'Total Roles',
                    'value' => $allRoles->count(),
                    'note' => 'Manage user roles across the system',
                    'tone' => 'violet',
                    'icon' => 'shield',
                ],
                [
                    'label' => 'Total Users',
                    'value' => User::count(),
                    'note' => 'Active users with assigned roles',
                    'tone' => 'emerald',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Permissions',
                    'value' => Permission::count(),
                    'note' => 'Total permissions available',
                    'tone' => 'orange',
                    'icon' => 'lock',
                ],
            ],
            'modules' => $modules,
            'permissionsByModule' => $permissionsByModule,
            'moduleFilters' => $modules
                ->mapWithKeys(fn (string $module) => [$module => Str::headline($module)])
                ->all(),
            'selectedModule' => $selectedModule,
            'selectedModuleLabel' => $selectedModule ? Str::headline($selectedModule) : null,
        ];
    }

    private function paginate($roles, array $filters): LengthAwarePaginator
    {
        $perPage = $this->perPage($filters);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $roles->forPage($currentPage, $perPage)->values(),
            $roles->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $filters,
            ]
        );
    }

    private function perPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 8);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 8;
    }

    private function stringFilter(array $filters, string $key): string
    {
        $value = $filters[$key] ?? '';

        return is_scalar($value) ? trim((string) $value) : '';
    }
}
