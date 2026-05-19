<?php

namespace App\Services\Renderer;

use Illuminate\Validation\ValidationException;

class ComponentValidator
{
    private const COLOR_PROPS = [
        'backgroundColor',
        'textColor',
        'borderColor',
        'color',
    ];

    private const IMAGE_URL_PROPS = [
        'src',
    ];

    private const CSS_LENGTH_PROPS = [
        'paddingTop',
        'paddingBottom',
        'paddingLeft',
        'paddingRight',
        'padding',
        'maxWidth',
        'height',
        'gap',
        'fontSize',
    ];

    private const ENUM_PROPS = [
        'align' => [
            'left',
            'center',
            'right',
            'justify',
        ],
        'borderStyle' => [
            'none',
            'solid',
            'dashed',
            'dotted',
            'double',
        ],
    ];

    private const INTEGER_RANGE_PROPS = [
        'columns' => [1, 12],
        'fontWeight' => [1, 1000],
    ];

    private const NUMBER_RANGE_PROPS = [
        'lineHeight' => [0, 10],
    ];

    // valider props selon schema
    public function validate(string $type, array $props, ?array $schema): void
    {
        if ($schema && isset($schema['props'])) {

            foreach ($schema['props'] as $key => $config) {

                // required
                if (($config['required'] ?? false) && ! array_key_exists($key, $props)) {
                    throw ValidationException::withMessages([
                        'structure' => 'Missing prop '.$key,
                    ]);
                }

                // type
                if (isset($props[$key])) {
                    $this->validateType($props[$key], $config['type'], $key);
                }
            }
        }

        $this->validateKnownProps($props);
    }

    private function validateType($value, string $type, string $key): void
    {
        $valid = match ($type) {
            'text' => is_string($value),
            'number' => is_numeric($value),
            'boolean' => is_bool($value),
            'image' => $this->isSafeImageUrl($value),
            'color' => $this->isSafeColor($value),
            default => true
        };

        if (! $valid) {
            throw ValidationException::withMessages([
                'structure' => 'Invalid type for '.$key,
            ]);
        }
    }

    private function validateKnownProps(array $props): void
    {
        foreach ($props as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (in_array($key, self::COLOR_PROPS, true)) {
                $this->assertValid(
                    $this->isSafeColor($value),
                    $key
                );
            }

            if (in_array($key, self::IMAGE_URL_PROPS, true)) {
                $this->assertValid(
                    $this->isSafeImageUrl($value),
                    $key
                );
            }

            if (in_array($key, self::CSS_LENGTH_PROPS, true)) {
                $this->assertValid(
                    $this->isSafeCssLength($value),
                    $key
                );
            }

            if (isset(self::ENUM_PROPS[$key])) {
                $this->assertValid(
                    is_string($value) && in_array($value, self::ENUM_PROPS[$key], true),
                    $key
                );
            }

            if (isset(self::INTEGER_RANGE_PROPS[$key])) {
                [$min, $max] = self::INTEGER_RANGE_PROPS[$key];

                $this->assertValid(
                    $this->isIntegerInRange($value, $min, $max),
                    $key
                );
            }

            if (isset(self::NUMBER_RANGE_PROPS[$key])) {
                [$min, $max] = self::NUMBER_RANGE_PROPS[$key];

                $this->assertValid(
                    $this->isNumberInRange($value, $min, $max),
                    $key
                );
            }
        }
    }

    private function assertValid(bool $valid, string $key): void
    {
        if ($valid) {
            return;
        }

        throw ValidationException::withMessages([
            'structure' => 'Invalid value for '.$key,
        ]);
    }

    private function isSafeImageUrl($value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $value = trim($value);

        if ($value === '') {
            return true;
        }

        if (preg_match('/[\x00-\x1F\x7F\s\\\\<>"\']/', $value)) {
            return false;
        }

        $lowerValue = strtolower($value);

        if (str_starts_with($lowerValue, '//')) {
            return false;
        }

        if (preg_match('/^(?:javascript|vbscript|data):/i', $value)) {
            return false;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if ($scheme !== null) {
            return in_array(strtolower($scheme), ['http', 'https'], true)
                && filter_var($value, FILTER_VALIDATE_URL) !== false;
        }

        return str_starts_with($value, '/')
            || str_starts_with($lowerValue, 'storage/');
    }

    private function isSafeColor($value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $value = trim($value);

        if ($value === '') {
            return true;
        }

        return strtolower($value) === 'transparent'
            || preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value) === 1;
    }

    private function isSafeCssLength($value): bool
    {
        if (is_int($value) || is_float($value)) {
            return is_finite((float) $value) && $value >= 0;
        }

        if (! is_string($value)) {
            return false;
        }

        $value = trim($value);

        if ($value === '') {
            return true;
        }

        return preg_match(
            '/^(?:\d+(?:\.\d+)?|\.\d+)(?:px|%|rem|em|vh|vw)?$/i',
            $value
        ) === 1;
    }

    private function isIntegerInRange($value, int $min, int $max): bool
    {
        if (is_int($value)) {
            $number = $value;
        } elseif (is_float($value) && is_finite($value) && floor($value) === $value) {
            $number = (int) $value;
        } elseif (is_string($value) && preg_match('/^\d+$/', trim($value)) === 1) {
            $number = (int) trim($value);
        } else {
            return false;
        }

        return $number >= $min && $number <= $max;
    }

    private function isNumberInRange($value, int $min, int $max): bool
    {
        if (is_int($value) || is_float($value)) {
            $number = (float) $value;
        } elseif (is_string($value) && preg_match('/^(?:\d+(?:\.\d+)?|\.\d+)$/', trim($value)) === 1) {
            $number = (float) trim($value);
        } else {
            return false;
        }

        return is_finite($number) && $number >= $min && $number <= $max;
    }
}
