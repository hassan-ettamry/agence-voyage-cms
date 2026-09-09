<?php

namespace App\Services\Renderer;

use Illuminate\Support\Facades\View;

class PageRenderer
{
    private const MAX_DEPTH = 50;

    private const MODES = [
        'editor',
        'preview',
        'live',
    ];

    public function __construct(
        private ComponentRegistry $registry,
        private ComponentValidator $validator,
        private ?ComponentPresentation $presentation = null
    ) {}

    /**
     * Entry point
     */
    public function render(array $structure, string $mode = 'live'): string
    {
        $mode = $this->normalizeMode($mode);

        if (! array_is_list($structure)) {
            return $this->debug('Invalid root structure');
        }

        $html = '';

        foreach ($structure as $node) {
            if (! is_array($node)) {
                continue;
            }

            $html .= $this->renderNode(
                $node,
                $mode
            );
        }

        return $html;
    }

    /**
     * Recursive render
     */
    private function renderNode(
        array $node,
        string $mode,
        int $depth = 0,
        ?array $parentNode = null,
        int $siblingIndex = 0,
        int $siblingCount = 1
    ): string {
        if ($depth > self::MAX_DEPTH) {
            return '';
        }

        $type = $node['type'] ?? null;

        $props = $this->normalizeProps(
            $node['props'] ?? []
        );

        $children = $node['children'] ?? [];

        $nodeId = $node['id'] ?? null;

        // vérifier composant
        if (! $type || ! $this->registry->exists($type)) {
            return $this->debug('Invalid component '.$type);
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

        if (! $view) {
            return $this->debug('Missing view '.$type);
        }

        // render children
        $childrenHtml = '';

        foreach ($children as $index => $child) {

            $childrenHtml .= $this->renderNode(
                $child,
                $mode,
                $depth + 1,
                $node,
                $index,
                count($children)
            );

        }

        try {

            $html = View::make($view, [

                'props' => $props,

                'children' => $childrenHtml,

                'type' => $type,

                'nodeId' => $nodeId,

                'mode' => $mode,

                'isEditor' => $mode === 'editor',

                'isPreview' => $mode === 'preview',

                'isLive' => $mode === 'live',

                'parentNode' => $parentNode,

                'parentType' => $parentNode['type'] ?? null,

                'parentProps' => $this->normalizeProps(
                    $parentNode['props'] ?? []
                ),

                'siblingIndex' => $siblingIndex,

                'siblingCount' => $siblingCount,

            ])->render();

            return ($this->presentation ?? app(ComponentPresentation::class))->apply($html, $props);

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

    private function normalizeProps(mixed $props): array
    {
        if (! is_array($props)) {
            return [];
        }

        return array_is_list($props)
            ? []
            : $props;
    }

    /**
     * Normalize render mode
     */
    private function normalizeMode(string $mode): string
    {
        return in_array($mode, self::MODES, true)
            ? $mode
            : 'live';
    }

    /**
     * debug (dev only)
     */
    private function debug(string $msg): string
    {
        return app()->environment('local')
            ? '<!-- '.$msg.' -->'
            : '';
    }
}
