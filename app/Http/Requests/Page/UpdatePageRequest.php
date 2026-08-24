<?php

namespace App\Http\Requests\Page;

use App\Models\Menu;
use App\Rules\ValidPageStructure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    /**
     * Autorisation
     */
    public function authorize(): bool
    {
        $page = $this->route('page');

        return auth()->check() && auth()->user()->agency_id === $page->agency_id;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        $page = $this->route('page');

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')
                    ->where('agency_id', auth()->user()->agency_id)
                    ->ignore($page->id),
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
            if (! $this->has('menu_selection')) {
                return;
            }

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
     * Préparation des données
     */
    protected function prepareForValidation(): void
    {

        if ($this->filled('title') && ! $this->filled('slug')) {

            $this->merge([
                'slug' => Str::slug($this->title),
            ]);

        }

        if ($this->filled('structure') && is_string($this->structure)) {

            $this->merge([
                'structure' => json_decode($this->structure, true),
            ]);

        }
    }
}
