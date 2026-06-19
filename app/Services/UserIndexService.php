<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;

class UserIndexService
{
    private const PER_PAGE_OPTIONS = [8, 16, 32, 64];

    public function build(array $filters, User $user): array
    {
        $baseQuery = User::query();
        $search = $this->stringFilter($filters, 'search');
        $filter = $this->stringFilter($filters, 'filter');

        $query = (clone $baseQuery)
            ->with('role')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'admin', function ($query) {
                $query->whereHas('role', fn ($role) => $role->where('slug', 'admin'));
            })
            ->when($filter === 'users', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereDoesntHave('role')
                        ->orWhereHas('role', fn ($role) => $role->where('slug', '!=', 'admin'));
                });
            });

        $users = $query->latest()
            ->paginate($this->perPage($filters))
            ->withQueryString();

        return [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
            'stats' => [
                [
                    'label' => 'Total Users',
                    'value' => $users->total(),
                    'note' => 'Active users',
                    'tone' => 'violet',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Admins',
                    'value' => User::whereHas('role', fn ($query) => $query->where('slug', 'admin'))->count(),
                    'note' => 'System admins',
                    'tone' => 'emerald',
                    'icon' => 'shield',
                ],
                [
                    'label' => 'Roles',
                    'value' => Role::count(),
                    'note' => 'Defined roles',
                    'tone' => 'orange',
                    'icon' => 'users',
                ],
                [
                    'label' => 'Permissions',
                    'value' => '-',
                    'note' => 'System level',
                    'tone' => 'blue',
                    'icon' => 'shield',
                ],
            ],
        ];
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
