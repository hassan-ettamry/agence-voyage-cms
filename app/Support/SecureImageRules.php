<?php

namespace App\Support;

class SecureImageRules
{
    public const MAX_KILOBYTES = 5120;

    public const MAX_DIMENSION = 6000;

    public static function rules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'image',
            'mimetypes:image/jpeg,image/png,image/webp',
            'max:'.self::MAX_KILOBYTES,
            'dimensions:max_width='.self::MAX_DIMENSION.',max_height='.self::MAX_DIMENSION,
        ];
    }
}
