<?php

namespace App\Http\Requests\Builder;

use App\Models\BuilderSavedBlock;
use App\Rules\ValidPageStructure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSavedBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', BuilderSavedBlock::class) === true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('builder_saved_blocks')->where('agency_id', $this->user()->agency_id)],
            'category' => ['nullable', 'string', 'max:80'],
            'structure' => ['required', 'array', 'size:1', new ValidPageStructure],
            'structure.0.type' => ['required', 'in:section'],
        ];
    }
}
