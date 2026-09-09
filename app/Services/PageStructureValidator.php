<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use JsonException;

class PageStructureValidator
{
    public const MAX_BYTES = 1_048_576;

    public const MAX_DEPTH = 30;

    public const MAX_NODES = 500;

    private const MAX_CHILDREN_PER_NODE = 100;

    private const MAX_PROPS_PER_NODE = 100;

    private const MAX_PROP_DEPTH = 10;

    /**
     * Validate the complete page structure.
     */
    public function validate(?array $structure): void
    {
        if ($structure === null) {
            return;
        }

        if (! array_is_list($structure)) {
            throw ValidationException::withMessages([
                'structure' => 'Structure must be a list of root nodes.',
            ]);
        }

        try {
            $encoded = json_encode($structure, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw ValidationException::withMessages([
                'structure' => 'Structure contains invalid data.',
            ]);
        }

        if (strlen($encoded) > self::MAX_BYTES) {
            throw ValidationException::withMessages([
                'structure' => 'Structure is larger than the 1 MB limit.',
            ]);
        }

        $nodeCount = 0;

        foreach ($structure as $node) {
            if (! is_array($node)) {
                throw ValidationException::withMessages([
                    'structure' => 'Each component must be an array.',
                ]);
            }

            $this->validateNode($node, 1, $nodeCount);
        }
    }

    /**
     * Validate a single node recursively.
     */
    private function validateNode(array $node, int $depth, int &$nodeCount): void
    {
        $nodeCount++;

        if ($nodeCount > self::MAX_NODES) {
            throw ValidationException::withMessages([
                'structure' => 'Structure may not contain more than 500 components.',
            ]);
        }

        if ($depth > self::MAX_DEPTH) {
            throw ValidationException::withMessages([
                'structure' => 'Structure nesting is too deep.',
            ]);
        }

        if (
            ! isset($node['type'])
            || ! is_string($node['type'])
            || $node['type'] === ''
            || strlen($node['type']) > 100
        ) {
            throw ValidationException::withMessages([
                'structure' => 'Each component must have a valid type.',
            ]);
        }

        if (isset($node['props']) && ! is_array($node['props'])) {
            throw ValidationException::withMessages([
                'structure' => 'Props must be an array.',
            ]);
        }

        if (isset($node['props'])) {
            if (count($node['props']) > self::MAX_PROPS_PER_NODE) {
                throw ValidationException::withMessages([
                    'structure' => 'A component contains too many properties.',
                ]);
            }

            $this->validatePropDepth($node['props']);
        }

        if (isset($node['id']) && (! is_string($node['id']) || strlen($node['id']) > 100)) {
            throw ValidationException::withMessages([
                'structure' => 'Each component ID must be a short string.',
            ]);
        }

        if (! isset($node['children'])) {
            return;
        }

        if (! is_array($node['children']) || ! array_is_list($node['children'])) {
            throw ValidationException::withMessages([
                'structure' => 'Children must be a list.',
            ]);
        }

        if (count($node['children']) > self::MAX_CHILDREN_PER_NODE) {
            throw ValidationException::withMessages([
                'structure' => 'A component may not contain more than 100 direct children.',
            ]);
        }

        foreach ($node['children'] as $child) {
            if (! is_array($child)) {
                throw ValidationException::withMessages([
                    'structure' => 'Each child must be an array.',
                ]);
            }

            $this->validateNode($child, $depth + 1, $nodeCount);
        }
    }

    private function validatePropDepth(mixed $value, int $depth = 0): void
    {
        if ($depth > self::MAX_PROP_DEPTH) {
            throw ValidationException::withMessages([
                'structure' => 'Component property nesting is too deep.',
            ]);
        }

        if (! is_array($value)) {
            return;
        }

        foreach ($value as $child) {
            $this->validatePropDepth($child, $depth + 1);
        }
    }
}
