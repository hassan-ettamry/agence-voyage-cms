<?php

namespace App\Services\Renderer;

class ComponentPresentation
{
    private const LENGTHS = [
        'marginTop' => 'margin-top',
        'marginRight' => 'margin-right',
        'marginBottom' => 'margin-bottom',
        'marginLeft' => 'margin-left',
        'paddingTop' => 'padding-top',
        'paddingRight' => 'padding-right',
        'paddingBottom' => 'padding-bottom',
        'paddingLeft' => 'padding-left',
        'width' => 'width',
        'maxWidth' => 'max-width',
        'minHeight' => 'min-height',
        'height' => 'height',
        'gap' => 'gap',
        'fontSize' => 'font-size',
        'borderWidth' => 'border-width',
        'borderRadius' => 'border-radius',
    ];

    private const COLORS = [
        'backgroundColor' => 'background-color',
        'textColor' => 'color',
        'borderColor' => 'border-color',
    ];

    private const SHADOWS = [
        'none' => 'none',
        'soft' => '0 10px 30px rgba(15, 23, 42, .08)',
        'medium' => '0 16px 45px rgba(15, 23, 42, .16)',
        'strong' => '0 24px 60px rgba(15, 23, 42, .24)',
    ];

    public function apply(string $html, array $props): string
    {
        $design = is_array($props['design'] ?? null) ? $props['design'] : [];
        $classes = [];
        $styles = [];

        foreach (['desktop', 'tablet', 'mobile'] as $device) {
            $values = is_array($design[$device] ?? null) ? $design[$device] : [];
            if ($values === []) {
                continue;
            }

            $classes[] = 'builder-design';
            if (array_key_exists('gap', $values) && $values['gap'] !== '') {
                $classes[] = 'builder-design-has-gap';
            }
            if (array_key_exists('columns', $values) && $values['columns'] !== '') {
                $classes[] = 'builder-design-has-columns';
            }
            foreach ($this->deviceVariables($device, $values) as $name => $value) {
                $styles[] = "{$name}:{$value}";
            }

            if (($values['visibility'] ?? null) === 'hidden') {
                $classes[] = "builder-design-hide-{$device}";
            }
        }

        if (($props['hideOnMobile'] ?? 'no') === 'yes') {
            $classes[] = 'site-hide-mobile';
        }
        if (($props['hideOnTablet'] ?? 'no') === 'yes') {
            $classes[] = 'site-hide-tablet';
        }

        if ($classes === [] && $styles === []) {
            return $html;
        }

        $html = $this->appendClass($html, implode(' ', array_unique($classes)));

        return $styles === []
            ? $html
            : $this->appendStyle($html, implode(';', $styles).';');
    }

    private function deviceVariables(string $device, array $values): array
    {
        $variables = [];

        foreach (self::LENGTHS as $key => $property) {
            if (! array_key_exists($key, $values) || $values[$key] === '') {
                continue;
            }
            $value = $this->cssLength($values[$key], $key);
            $variables["--bd-{$device}-{$this->kebab($key)}"] = $value;
            if ($device === 'desktop') {
                $variables[$property] = $value;
            }
        }

        foreach (self::COLORS as $key => $property) {
            if (! empty($values[$key])) {
                $variables["--bd-{$device}-{$this->kebab($key)}"] = $values[$key];
                if ($device === 'desktop') {
                    $variables[$property] = $values[$key];
                }
            }
        }

        if (! empty($values['backgroundImage'])) {
            $image = str_replace(["'", '(', ')'], ['%27', '%28', '%29'], $values['backgroundImage']);
            $variables["--bd-{$device}-background-image"] = "url('{$image}')";
            if ($device === 'desktop') {
                $variables['background-image'] = "url('{$image}')";
                $variables['background-size'] = 'cover';
                $variables['background-position'] = 'center';
            }
        }

        foreach (['fontWeight' => 'font-weight', 'lineHeight' => 'line-height', 'alignment' => 'text-align'] as $key => $property) {
            if (($values[$key] ?? '') !== '') {
                $variables["--bd-{$device}-{$this->kebab($key)}"] = (string) $values[$key];
                if ($device === 'desktop') {
                    $variables[$property] = (string) $values[$key];
                }
            }
        }

        if (($values['columns'] ?? '') !== '') {
            $variables["--bd-{$device}-columns"] = (string) $values['columns'];
            if ($device === 'desktop') {
                $variables['--site-card-columns'] = (string) $values['columns'];
            }
        }

        if (isset(self::SHADOWS[$values['shadow'] ?? ''])) {
            $shadow = self::SHADOWS[$values['shadow']];
            $variables["--bd-{$device}-shadow"] = $shadow;
            if ($device === 'desktop') {
                $variables['box-shadow'] = $shadow;
            }
        }

        if (($values['borderWidth'] ?? '') !== '' && ! isset($values['borderStyle'])) {
            $variables['border-style'] = 'solid';
        }

        return $variables;
    }

    private function cssLength(mixed $value, string $key): string
    {
        if (is_int($value) || is_float($value) || (is_string($value) && is_numeric(trim($value)))) {
            return in_array($key, ['lineHeight'], true) ? (string) $value : ((float) $value).'px';
        }

        return trim((string) $value);
    }

    private function kebab(string $value): string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '-$0', $value));
    }

    private function appendClass(string $html, string $classes): string
    {
        $safe = htmlspecialchars(trim($classes), ENT_QUOTES, 'UTF-8');
        $updated = preg_replace('/^(\s*<[^>]*\bclass=")/', '$1'.$safe.' ', $html, 1, $count);

        return $count === 1
            ? $updated
            : (preg_replace('/^(\s*<[a-zA-Z0-9:-]+)/', '$1 class="'.$safe.'"', $html, 1) ?? $html);
    }

    private function appendStyle(string $html, string $style): string
    {
        $safe = htmlspecialchars($style, ENT_QUOTES, 'UTF-8');
        $updated = preg_replace('/^(\s*<[^>]*\bstyle=")([^\"]*)"/', '$1$2'.$safe.'"', $html, 1, $count);

        return $count === 1
            ? $updated
            : (preg_replace('/^(\s*<[a-zA-Z0-9:-]+(?:\s+class="[^"]*")?)/', '$1 style="'.$safe.'"', $html, 1) ?? $html);
    }
}
