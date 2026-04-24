<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class PageStructureValidator
{
    /**
     * Valider la structure complète de la page
     */
    public function validate(?array $structure): void
    {
        // Si aucune structure, on ne valide pas
        if (!$structure) {
            return;
        }

        // Lancer la validation récursive
        $this->validateNode($structure);
    }

    /**
     * Valider un noeud de la structure (récursif)
     */
    private function validateNode(array $node): void
    {
        // Vérifier que le type est défini et valide
        if (!isset($node['type']) || !is_string($node['type'])) {
            throw ValidationException::withMessages([
                'structure' => 'Chaque composant doit avoir un type valide.'
            ]);
        }

        // Vérifier que les props sont un tableau si présentes
        if (isset($node['props']) && !is_array($node['props'])) {
            throw ValidationException::withMessages([
                'structure' => 'Les props doivent être un tableau.'
            ]);
        }

        // Vérifier les enfants (validation récursive)
        if (isset($node['children'])) {

            if (!is_array($node['children'])) {
                throw ValidationException::withMessages([
                    'structure' => 'Children doit être un tableau.'
                ]);
            }

            foreach ($node['children'] as $child) {
                $this->validateNode($child);
            }
        }
    }
}