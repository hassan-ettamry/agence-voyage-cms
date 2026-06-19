<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PermissionCatalogService
{
    private const DEFAULT_ACTIONS = ['view', 'create', 'update', 'delete'];

    private const ACTION_LABELS = [
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'apply' => 'Apply',
    ];

    private const MODULES = [
        'page' => [
            'label' => 'Pages',
            'subject' => 'Page',
            'plural' => 'Pages',
        ],
        'user' => [
            'label' => 'Users',
            'subject' => 'User',
            'plural' => 'Users',
        ],
        'role' => [
            'label' => 'Roles',
            'subject' => 'Role',
            'plural' => 'Roles',
        ],
        'permission' => [
            'label' => 'Permissions',
            'subject' => 'Permission',
            'plural' => 'Permissions',
        ],
        'theme' => [
            'label' => 'Themes',
            'subject' => 'Theme',
            'plural' => 'Themes',
            'actions' => ['view', 'apply', 'update'],
        ],
    ];

    public function modules(): array
    {
        return collect(self::MODULES)
            ->map(fn (array $module, string $key) => [
                'key' => $key,
                'label' => $module['label'],
                'subject' => $module['subject'],
                'plural' => $module['plural'],
                'actions' => $module['actions'] ?? self::DEFAULT_ACTIONS,
            ])
            ->values()
            ->all();
    }

    public function module(string $key): ?array
    {
        $module = collect($this->modules())
            ->firstWhere('key', $key);

        if ($module !== null) {
            return $module;
        }

        return null;
    }

    public function definitionsForModule(string $moduleKey): array
    {
        $module = $this->module($moduleKey);

        if ($module === null) {
            throw ValidationException::withMessages([
                'module' => 'Select a valid module.',
            ]);
        }

        return collect($module['actions'])
            ->map(fn (string $action) => $this->definition($module, $action))
            ->values()
            ->all();
    }

    public function modulesWithoutPermissions(): array
    {
        $existingModules = Permission::query()
            ->select('slug')
            ->whereIn('slug', $this->canonicalSlugs())
            ->get()
            ->pluck('module_key')
            ->unique()
            ->flip();

        return collect($this->modules())
            ->reject(fn (array $module) => $existingModules->has($module['key']))
            ->map(function (array $module) {
                return [
                    'key' => $module['key'],
                    'label' => $module['label'],
                    'permissions' => $this->definitionsForModule($module['key']),
                ];
            })
            ->values()
            ->all();
    }

    public function createModules(array $moduleKeys): array
    {
        $moduleKeys = array_values(array_unique($moduleKeys));
        $catalogModules = collect($this->modules())->keyBy('key');
        $created = [];

        foreach ($moduleKeys as $moduleKey) {
            if (! $catalogModules->has($moduleKey)) {
                throw ValidationException::withMessages([
                    'modules' => 'Select a valid missing module.',
                ]);
            }

            $moduleSlugs = collect($this->definitionsForModule($moduleKey))->pluck('slug')->all();

            if (Permission::whereIn('slug', $moduleSlugs)->exists()) {
                continue;
            }

            foreach ($this->definitionsForModule($moduleKey) as $definition) {
                Permission::create([
                    'name' => $definition['name'],
                    'slug' => $definition['slug'],
                ]);
            }

            $created[] = $moduleKey;
        }

        return $created;
    }

    public function syncModule(string $moduleKey, array $actions): void
    {
        $definitions = collect($this->definitionsForModule($moduleKey))->keyBy('action');

        $actions = collect($actions)
            ->filter(fn ($action) => is_string($action))
            ->map(fn (string $action) => trim($action))
            ->unique()
            ->values();

        $invalidAction = $actions->first(fn (string $action) => ! $definitions->has($action));

        if ($invalidAction !== null) {
            throw ValidationException::withMessages([
                'actions' => 'Select valid permissions for this module.',
            ]);
        }

        Permission::where('slug', 'like', $moduleKey.'.%')
            ->get()
            ->each(function (Permission $permission) use ($actions, $definitions) {
                if (
                    $definitions->has($permission->action_key) &&
                    ! $actions->contains($permission->action_key)
                ) {
                    $permission->delete();
                }
            });

        foreach ($actions as $action) {
            $definition = $definitions->get($action);

            Permission::firstOrCreate(
                ['slug' => $definition['slug']],
                ['name' => $definition['name']]
            );
        }
    }

    public function canonicalSlugs(): array
    {
        return collect($this->modules())
            ->flatMap(function (array $module) {
                return collect($module['actions'])
                    ->map(fn (string $action) => "{$module['key']}.{$action}");
            })
            ->values()
            ->all();
    }

    private function definition(array $module, string $action): array
    {
        $actionLabel = Arr::get(self::ACTION_LABELS, $action, Str::headline($action));
        $subject = $action === 'view' ? $module['plural'] : $module['subject'];

        return [
            'module' => $module['key'],
            'module_label' => $module['label'],
            'action' => $action,
            'action_label' => $actionLabel,
            'name' => "{$actionLabel} {$subject}",
            'slug' => "{$module['key']}.{$action}",
        ];
    }
}
