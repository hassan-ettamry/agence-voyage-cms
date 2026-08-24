<?php

namespace App\Http\Requests\Builder;

use App\Rules\ValidPageStructure;
use Illuminate\Foundation\Http\FormRequest;

class RenderBuilderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'structure' => ['required', 'array', new ValidPageStructure],
            'mode' => ['sometimes', 'string', 'in:editor,preview,live'],
        ];
    }
}
