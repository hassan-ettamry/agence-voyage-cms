<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class ComponentSchemaValidator
{
    public function validate(?array $schema): void
    {
        if (!$schema) return;

        // props obligatoire
        if (!isset($schema['props'])) {
            throw ValidationException::withMessages([
                'schema_json' => 'Schema doit contenir "props".'
            ]);
        }

        if (!is_array($schema['props'])) {
            throw ValidationException::withMessages([
                'schema_json' => '"props" doit être un tableau.'
            ]);
        }

        // validation de chaque prop
        foreach ($schema['props'] as $key => $prop) {

            if (!isset($prop['type'])) {
                throw ValidationException::withMessages([
                    'schema_json' => "La propriété {$key} doit avoir un type."
                ]);
            }

            if (!in_array($prop['type'], ['text', 'image', 'color', 'number', 'boolean', 'array', 'object', 'text_or_array'])) {
                throw ValidationException::withMessages([
                    'schema_json' => "Type invalide pour {$key}."
                ]);
            }
        }
    }
}
