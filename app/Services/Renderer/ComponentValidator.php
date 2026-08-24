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
        'iconColor',
        'titleColor',
        'linkColor',
        'buttonColor',
        'accentColor',
    ];

    private const IMAGE_URL_PROPS = [
        'src',
    ];

    private const URL_PROPS = [
        'url',
        'embedUrl',
        'actionUrl',
    ];

    private const CSS_LENGTH_PROPS = [
        'paddingTop',
        'paddingBottom',
        'paddingLeft',
        'paddingRight',
        'padding',
        'maxWidth',
        'minHeight',
        'width',
        'height',
        'gap',
        'fontSize',
        'borderWidth',
        'borderRadius',
        'marginTop',
        'marginBottom',
        'top',
        'left',
        'size',
    ];

    private const ENUM_PROPS = [
        'display' => [
            'block',
            'flex',
            'grid',
            'none',
        ],
        'flexDirection' => [
            'row',
            'row-reverse',
            'column',
            'column-reverse',
        ],
        'justifyContent' => [
            'flex-start',
            'center',
            'flex-end',
            'space-between',
            'space-around',
            'space-evenly',
            'start',
            'end',
        ],
        'alignItems' => [
            'stretch',
            'flex-start',
            'center',
            'flex-end',
            'baseline',
            'start',
            'end',
        ],
        'align' => [
            'left',
            'center',
            'right',
            'justify',
        ],
        'layout' => [
            'horizontal',
            'vertical',
        ],
        'target' => [
            'same-tab',
            'new-tab',
        ],
        'underline' => [
            'yes',
            'no',
        ],
        'controls' => [
            'yes',
            'no',
        ],
        'autoplay' => [
            'yes',
            'no',
        ],
        'allowFullscreen' => [
            'yes',
            'no',
        ],
        'allowMultiple' => [
            'yes',
            'no',
        ],
        'method' => [
            'get',
            'post',
        ],
        'borderStyle' => [
            'none',
            'solid',
            'dashed',
            'dotted',
            'double',
        ],
        'containerRole' => [
            'layout',
            'grid',
            'grid-item',
            'card',
            'content',
            'group',
        ],
        'overflow' => [
            'visible',
            'hidden',
            'auto',
            'scroll',
        ],
        'visibility' => [
            'visible',
            'hidden',
        ],
        'position' => [
            'static',
            'relative',
            'absolute',
            'sticky',
        ],
    ];

    private const INTEGER_RANGE_PROPS = [
        'gridColumns' => [1, 12],
        'gridSpan' => [1, 12],
        'fontWeight' => [1, 1000],
        'columns' => [1, 6],
        'zoom' => [1, 20],
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
        $this->validateDesign($props['design'] ?? null);
    }

    private function validateDesign(mixed $design): void
    {
        if ($design === null) {
            return;
        }

        $this->assertValid(is_array($design) && ! array_is_list($design), 'design');
        $allowedDevices = ['desktop', 'tablet', 'mobile'];
        $lengths = ['marginTop', 'marginRight', 'marginBottom', 'marginLeft', 'paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft', 'width', 'maxWidth', 'minHeight', 'height', 'gap', 'fontSize', 'borderWidth', 'borderRadius'];
        $colors = ['backgroundColor', 'textColor', 'borderColor'];
        $allowedKeys = array_merge($lengths, $colors, ['backgroundImage', 'fontWeight', 'lineHeight', 'alignment', 'columns', 'shadow', 'visibility']);

        foreach ($design as $device => $values) {
            $this->assertValid(in_array($device, $allowedDevices, true), "design.{$device}");
            $this->assertValid(is_array($values) && ! array_is_list($values), "design.{$device}");

            foreach ($values as $key => $value) {
                $path = "design.{$device}.{$key}";
                $this->assertValid(in_array($key, $allowedKeys, true), $path);

                if (in_array($key, $lengths, true)) {
                    $this->assertValid($this->isSafeCssLength($value, $key), $path);
                } elseif (in_array($key, $colors, true)) {
                    $this->assertValid($this->isSafeColor($value), $path);
                } elseif ($key === 'backgroundImage') {
                    $this->assertValid($this->isSafeImageUrl($value), $path);
                } elseif ($key === 'fontWeight') {
                    $this->assertValid($this->isIntegerInRange($value, 100, 900), $path);
                } elseif ($key === 'lineHeight') {
                    $this->assertValid($this->isNumberInRange($value, .8, 3), $path);
                } elseif ($key === 'columns') {
                    $this->assertValid($this->isIntegerInRange($value, 1, 6), $path);
                } elseif ($key === 'alignment') {
                    $this->assertValid(is_string($value) && in_array($value, ['left', 'center', 'right', 'justify'], true), $path);
                } elseif ($key === 'shadow') {
                    $this->assertValid(is_string($value) && in_array($value, ['none', 'soft', 'medium', 'strong'], true), $path);
                } elseif ($key === 'visibility') {
                    $this->assertValid(is_string($value) && in_array($value, ['visible', 'hidden'], true), $path);
                }
            }
        }
    }

    private function validateType($value, string $type, string $key): void
    {
        $valid = match ($type) {
            'text' => is_string($value),
            'number' => is_numeric($value),
            'boolean' => is_bool($value),
            'array' => is_array($value) && array_is_list($value),
            'object' => is_array($value) && ! array_is_list($value),
            'text_or_array' => is_string($value) || (is_array($value) && array_is_list($value)),
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

            if (in_array($key, self::URL_PROPS, true)) {
                $this->assertValid(
                    $this->isSafeUrl($value),
                    $key
                );
            }

            if (in_array($key, self::CSS_LENGTH_PROPS, true)) {
                $this->assertValid(
                    $this->isSafeCssLength($value, $key),
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

    private function isSafeUrl($value): bool
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

        if ($value === '#') {
            return true;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if ($scheme !== null) {
            $scheme = strtolower($scheme);

            if (in_array($scheme, ['http', 'https'], true)) {
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            }

            return in_array($scheme, ['mailto', 'tel'], true);
        }

        return str_starts_with($value, '/');
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

    private function isSafeCssLength($value, string $key): bool
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

        if ($key === 'maxWidth' && strtolower($value) === 'none') {
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
