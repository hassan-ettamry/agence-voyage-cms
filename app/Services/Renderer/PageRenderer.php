<?php

namespace App\Services\Renderer;

use Illuminate\Support\Facades\View;

class PageRenderer
{
    private const MAX_DEPTH = 50;

    public function __construct(
        private ComponentRegistry $registry,
        private ComponentValidator $validator
    ) {}

    public function render(array $structure): string
    {
        return $this->renderNode($structure);
    }

    private function renderNode(array $node, int $depth = 0): string
    {
        if ($depth > self::MAX_DEPTH) return '';

        $type = $node['type'] ?? null;
        $props = $node['props'] ?? [];
        $children = $node['children'] ?? [];

        // vérifier composant
        if (!$type || !$this->registry->exists($type)) {
            return $this->debug("Invalid component ".$type);
        }

        // validation props
        try {
            $this->validator->validate(
                $type,
                $props,
                $this->registry->getSchema($type)
            );
        } catch (\Throwable $e) {
            return $this->debug($e->getMessage());
        }

        $props = $this->sanitizeProps($props);

        // récupérer vue
        $view = $this->registry->resolveView($type);

        if (!$view) {
            return $this->debug("Missing view ".$type);
        }

        // rendu enfants
        $childrenHtml = '';
        foreach ($children as $child) {
            $childrenHtml .= $this->renderNode($child, $depth + 1);
        }

        try {
            return View::make($view, [
                'props' => $props,
                'children' => $childrenHtml,
                'type' => $type
            ])->render();
        } catch (\Throwable $e) {
            return $this->debug($e->getMessage());
        }
    }

    //  nettoyage anti XSS
    private function sanitizeProps(array $props): array
    {
        foreach ($props as $key => $value) {

            if (is_string($value)) {
                // supprime toutes les balises HTML dangereuses
                $props[$key] = strip_tags($value);
            }

            // si tableau (nested props)
            if (is_array($value)) {
                $props[$key] = $this->sanitizeProps($value);
            }
        }

        return $props;
    }

    private function debug(string $msg): string
    {
        return app()->environment('local') ? "<!-- ".$msg." -->" : '';
    }
}