<?php

namespace App\Http\Requests\Page;

use App\Models\Menu;
use App\Rules\ValidPageStructure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
                }),
            ],
            'content' => ['nullable', 'string'],
            'structure' => ['nullable', 'array', new ValidPageStructure],
            'status' => ['nullable', 'in:draft,published'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'menu_selection' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $selection = $this->input('menu_selection', 'none');

            if (in_array($selection, ['none', 'default', null, ''], true)) {
                return;
            }

            $exists = Menu::withoutGlobalScopes()
                ->where('agency_id', $this->user()->agency_id)
                ->whereKey($selection)
                ->exists();

            if (! $exists) {
                $validator->errors()->add('menu_selection', 'Le menu selectionne est invalide.');
            }
        });
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
        if (! $this->filled('slug') && $this->filled('title')) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }

        if (! $this->filled('menu_selection')) {
            $this->merge(['menu_selection' => 'none']);
        }
    }
}
