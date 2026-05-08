<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;
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
                Rule::unique('pages', 'slug')
                    ->where('agency_id', auth()->user()->agency_id)
                    ->ignore($page->id)
            ],
            'content' => ['nullable', 'string'],
            'structure' => ['nullable', 'array'],
            'status' => ['nullable', 'in:draft,published'],
            'meta_title' => ['nullable', 'string', 'max:60'],
            'meta_description' => ['nullable', 'string', 'max:160'],
        ];
    }

    /**
     * Préparation des données
     */
    protected function prepareForValidation(): void
    {

        if ($this->filled('title') && !$this->filled('slug')) {
    
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title)
            ]);
    
        }
    
        if ($this->filled('structure') && is_string($this->structure)) {
    
            $this->merge([
                'structure' => json_decode($this->structure, true)
            ]);
    
        }
    }
}