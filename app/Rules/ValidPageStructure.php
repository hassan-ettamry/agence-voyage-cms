<?php

namespace App\Rules;

use App\Services\PageStructureValidator;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\ValidationException;

class ValidPageStructure implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            return;
        }

        try {
            app(PageStructureValidator::class)->validate($value);
        } catch (ValidationException $exception) {
            $fail($exception->errors()['structure'][0] ?? 'The page structure is invalid.');
        }
    }
}
