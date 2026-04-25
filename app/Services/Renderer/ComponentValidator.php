<?php

namespace App\Services\Renderer;

use Illuminate\Validation\ValidationException;

class ComponentValidator
{
    // valider props selon schema
    public function validate(string $type, array $props, ?array $schema): void
    {
        if (!$schema || !isset($schema['props'])) return;

        foreach ($schema['props'] as $key => $config) {

            // required
            if (($config['required'] ?? false) && !array_key_exists($key, $props)) {
                throw ValidationException::withMessages([
                    'structure' => "Missing prop ".$key
                ]);
            }

            // type
            if (isset($props[$key])) {
                $this->validateType($props[$key], $config['type'], $key);
            }
        }
    }

    private function validateType($value, string $type, string $key): void
    {
        $valid = match ($type) {
            'text' => is_string($value),
            'number' => is_numeric($value),
            'boolean' => is_bool($value),
            default => true
        };

        if (!$valid) {
            throw ValidationException::withMessages([
                'structure' => "Invalid type for ".$key
            ]);
        }
    }
}