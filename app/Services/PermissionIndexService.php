<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class PermissionIndexService
{
    private const PER_PAGE_OPTIONS = [8, 16, 32, 64];

    public function __construct(private PermissionCatalogService $permissionCatalog) {}

    public function build(array $filters, User $user): array
    {
        $allPermissions = Permission::query()
            ->whereIn('slug', $this->permissionCatalog->canonicalSlugs())
            ->orderBy('slug')
            ->get();

        $moduleRows = $this->moduleRows($allPermissions);
        $permissionModules = $this->paginate($moduleRows, $filters);

        return [
            'permissionModules' => $permissionModules,
            'stats' => [
                [
                    'label' => 'Total Permissions',
                    'value' => $allPermissions->count(),
                    'icon' => 'shield',
                ],
            ],
            'newPermissionModules' => $this->permissionCatalog->modulesWithoutPermissions(),
        ];
    }

    private function moduleRows($permissions)
    {
        return $permissions
            ->groupBy('module_key')
            ->map(function ($permissions, string $moduleKey) {
                $module = $this->permissionCatalog->module($moduleKey) ?? [
                    'key' => $moduleKey,
                    'label' => Str::headline($moduleKey),
                ];

                $definitions = collect($this->permissionCatalog->definitionsForModule($moduleKey));
                $existingByAction = $permissions->keyBy('action_key');

                $actions = $definitions
                    ->map(function (array $definition) use ($existingByAction) {
                        $permission = $existingByAction->get($definition['action']);

                        return array_merge($definition, [
                            'exists' => $permission !== null,
                            'permission' => $permission,
                        ]);
                    })
                    ->values();

                return [
                    'key' => $moduleKey,
                    'label' => $module['label'],
                    'permissions' => $permissions
                        ->filter(fn (Permission $permission) => $definitions->contains('action', $permission->action_key))
                        ->sortBy('slug')
                        ->values(),
                    'actions' => $actions->all(),
                    'existingActions' => $existingByAction->keys()->values()->all(),
                    'updatedAt' => $permissions->sortByDesc('updated_at')->first()?->updated_at,
                    'isEditable' => true,
                ];
            })
            ->sortBy('label')
            ->values();
    }

    private function paginate($rows, array $filters): LengthAwarePaginator
    {
        $perPage = $this->perPage($filters);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $query = [];

        if (isset($filters['per_page'])) {
            $query['per_page'] = $perPage;
        }

        return new LengthAwarePaginator(
            $rows->forPage($currentPage, $perPage)->values(),
            $rows->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $query,
            ]
        );
    }

    private function perPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 8);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : 8;
    }
}
