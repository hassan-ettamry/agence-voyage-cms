<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    /**
     * Autorisation
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->where(function ($query) {
                    return $query->where('agency_id', auth()->user()->agency_id);
                })
            ],
            'content' => ['nullable', 'string'],
            'structure' => ['nullable', 'array'],
            'status' => ['nullable', 'in:draft,published'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    /**
     * Messages personnalisés
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de la page est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'slug.unique' => 'Ce slug est déjà utilisé pour une autre page.',
            'status.in' => 'Le statut doit être "draft" ou "published".',
        ];
    }

    /**
     * Préparation des données
     */
    protected function prepareForValidation(): void
    {
        if (!$this->filled('slug') && $this->filled('title')) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title)
            ]);
        }
    }
}