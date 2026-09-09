<?php

namespace App\Services;

use App\Models\Component;
use App\Services\ComponentSchemaValidator;

class ComponentService
{
    private ComponentSchemaValidator $validator;

    public function __construct(ComponentSchemaValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Tous les composants actifs
     */
    public function getActive()
    {
        return Component::where('is_active', true)->get();
    }

    /**
     * Composants groupés par catégorie
     */
    public function getGrouped()
    {
        return $this->getActive()->groupBy('category');
    }

    /**
     * Composants pour le builder (format optimisé)
     */
    public function getForBuilder()
    {
        return $this->getActive()->map(function ($component) {
            return [
                'type' => $component->type,
                'name' => $component->name,
                'category' => $component->category,
                'schema' => $component->schema_json
            ];
        });
    }

    /**
     * Créer un composant avec validation du schema
     */
    public function create(array $data)
    {
        $this->validator->validate($data['schema_json'] ?? null);

        return Component::create($data);
    }

    /**
     * Mettre à jour avec validation
     */
    public function update(Component $component, array $data)
    {
        if (isset($data['schema_json'])) {
            $this->validator->validate($data['schema_json']);
        }

        $component->update($data);

        return $component;
    }

    /**
     * Supprimer
     */
    public function delete(Component $component)
    {
        return $component->delete();
    }

    /**
     * Toggle active
     */
    public function toggle(Component $component)
    {
        $component->update([
            'is_active' => !$component->is_active
        ]);

        return $component;
    }

    /**
     * Vérifier si un type existe
     */
    public function exists(string $type): bool
    {
        return Component::where('type', $type)
            ->where('is_active', true)
            ->exists();
    }
}