<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    /**
     * Autorisation de la requête
     * Ici on autorise tout, mais idéalement à gérer avec une Policy
     */
    public function authorize(): bool
    {
        return true; // à remplacer plus tard par une logique de Policy
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'], // titre obligatoire
            'slug' => ['nullable', 'string', 'max:255'], // slug optionnel
            'content' => ['nullable', 'string'], // contenu texte/HTML
            'structure' => ['nullable', 'array'], // structure JSON (page builder)
            'status' => ['nullable', 'in:draft,published'], // statut
        ];
    }
}