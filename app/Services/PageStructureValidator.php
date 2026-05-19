<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class PageStructureValidator
{
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

        foreach ($structure as $node) {
            if (! is_array($node)) {
                throw ValidationException::withMessages([
                    'structure' => 'Each component must be an array.',
                ]);
            }

            $this->validateNode($node);
        }
    }

    /**
     * Validate a single node recursively.
     */
    private function validateNode(array $node): void
    {
        if (! isset($node['type']) || ! is_string($node['type'])) {
            throw ValidationException::withMessages([
                'structure' => 'Each component must have a valid type.',
            ]);
        }

        if (isset($node['props']) && ! is_array($node['props'])) {
            throw ValidationException::withMessages([
                'structure' => 'Props must be an array.',
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

        foreach ($node['children'] as $child) {
            if (! is_array($child)) {
                throw ValidationException::withMessages([
                    'structure' => 'Each child must be an array.',
                ]);
            }

            $this->validateNode($child);
        }
    }
}
