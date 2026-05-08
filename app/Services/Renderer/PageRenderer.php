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

    /**
     * Entry point
     */
    public function render(array $structure): string
    {
        $index = 0;

        if (isset($structure[0])) {
            $html = '';

            foreach ($structure as $node) {
                $html .= $this->renderNode($node, 0, $index);
            }

            return $html;
        }

        return $this->renderNode($structure, 0, $index);
    }

    /**
     * Recursive render
     */
    private function renderNode(array $node, int $depth = 0, int &$index = 0): string
    {
        if ($depth > self::MAX_DEPTH) return '';

        // generate unique index
        $currentIndex = $index++;

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

        // sanitize
        $props = $this->sanitizeProps($props);

        // récupérer vue
        $view = $this->registry->resolveView($type);

        if (!$view) {
            return $this->debug("Missing view ".$type);
        }

        // render children (IMPORTANT: pass index)
        $childrenHtml = '';
        foreach ($children as $child) {
            $childrenHtml .= $this->renderNode($child, $depth + 1, $index);
        }

        try {
            return View::make($view, [
                'props' => $props,
                'children' => $childrenHtml,
                'type' => $type,
                'index' => $currentIndex
            ])->render();
        } catch (\Throwable $e) {
            return $this->debug($e->getMessage());
        }
    }

    /**
     * sanitize props (anti XSS)
     */
    private function sanitizeProps(array $props): array
    {
        foreach ($props as $key => $value) {

            if (is_string($value)) {
                $props[$key] = strip_tags($value);
            }

            if (is_array($value)) {
                $props[$key] = $this->sanitizeProps($value);
            }
        }

        return $props;
    }

    /**
     * debug (dev only)
     */
    private function debug(string $msg): string
    {
        return app()->environment('local') ? "<!-- ".$msg." -->" : '';
    }
}