<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    /**
     * Autorisation
     * À connecter plus tard avec une Policy
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour update
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'], // facultatif mais validé si présent
            'slug' => ['nullable', 'string', 'max:255'],   // slug optionnel
            'content' => ['nullable', 'string'],           // contenu
            'structure' => ['nullable', 'array'],          // JSON structure
            'status' => ['nullable', 'in:draft,published'], // statut
        ];
    }
}