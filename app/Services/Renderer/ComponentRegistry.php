<?php

namespace App\Services\Renderer;

use App\Models\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ComponentRegistry
{
    private array $components = [];
    private array $views = [];

    public function __construct()
    {
        $this->components = Cache::remember('components.registry', 3600, function () {
            return Component::where('is_active', true)
                ->get()
                ->keyBy('type')
                ->toArray();
        });
    }

    // vérifier si composant existe
    public function exists(string $type): bool
    {
        return isset($this->components[$type]);
    }

    // récupérer schema
    public function getSchema(string $type): ?array
    {
        return $this->components[$type]['schema_json'] ?? null;
    }

    // récupérer vue avec cache
    public function resolveView(string $type): ?string
    {
        if (isset($this->views[$type])) {
            return $this->views[$type];
        }

        $view = "components.builder.".$type;
        return $this->views[$type] = View::exists($view) ? $view : null;
    }
}