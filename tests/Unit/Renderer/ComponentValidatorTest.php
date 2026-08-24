<?php

namespace Tests\Unit\Renderer;

use App\Services\Renderer\ComponentValidator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ComponentValidatorTest extends TestCase
{
    private ComponentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new ComponentValidator;
    }

    public function test_it_accepts_guided_sparse_responsive_design_values(): void
    {
        $this->validator->validate('heading', [
            'text' => 'Responsive heading',
            'design' => [
                'desktop' => ['fontSize' => 56, 'alignment' => 'left', 'textColor' => '#12372f'],
                'tablet' => ['fontSize' => 42],
                'mobile' => ['fontSize' => 30, 'visibility' => 'visible'],
            ],
        ], ['props' => ['text' => ['type' => 'text']]]);

        $this->assertTrue(true);
    }

    public function test_it_rejects_free_css_inside_guided_design_values(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('heading', [
            'design' => ['desktop' => ['customCss' => 'position:fixed']],
        ], null);
    }

    #[DataProvider('safeImageUrls')]
    public function test_it_accepts_safe_image_urls(string $url): void
    {
        $this->validator->validate('image', [
            'src' => $url,
        ], null);

        $this->assertTrue(true);
    }

    public static function safeImageUrls(): array
    {
        return [
            [''],
            ['/images/photo.jpg'],
            ['/storage/pages/photo.jpg'],
            ['storage/pages/photo.jpg'],
            ['https://example.com/photo.jpg'],
            ['http://example.com/photo.jpg'],
        ];
    }

    #[DataProvider('unsafeImageUrls')]
    public function test_it_rejects_unsafe_image_urls(string $url): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('image', [
            'src' => $url,
        ], null);
    }

    public static function unsafeImageUrls(): array
    {
        return [
            ['javascript:alert(1)'],
            ['vbscript:alert(1)'],
            ['data:text/html,<svg onload=alert(1)>'],
            ['//example.com/photo.jpg'],
            ['https://example.com/photo.jpg" onerror="alert(1)'],
        ];
    }

    #[DataProvider('safeWidgetUrls')]
    public function test_it_accepts_safe_widget_urls(string $key, string $url): void
    {
        $this->validator->validate('link', [
            $key => $url,
        ], null);

        $this->assertTrue(true);
    }

    public static function safeWidgetUrls(): array
    {
        return [
            ['url', '#'],
            ['url', '/contact'],
            ['url', 'https://example.com/page'],
            ['url', 'mailto:hello@example.com'],
            ['url', 'tel:+212600000000'],
            ['embedUrl', 'https://www.google.com/maps?q=Rabat&output=embed'],
            ['actionUrl', '/contact'],
        ];
    }

    #[DataProvider('unsafeWidgetUrls')]
    public function test_it_rejects_unsafe_widget_urls(string $key, string $url): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('link', [
            $key => $url,
        ], null);
    }

    public static function unsafeWidgetUrls(): array
    {
        return [
            ['url', 'javascript:alert(1)'],
            ['url', 'data:text/html,<svg onload=alert(1)>'],
            ['embedUrl', '//evil.example/map'],
            ['actionUrl', 'https://example.com/form" onsubmit="alert(1)'],
        ];
    }

    #[DataProvider('safeColors')]
    public function test_it_accepts_safe_colors(string $color): void
    {
        $this->validator->validate('section', [
            'backgroundColor' => $color,
        ], null);

        $this->assertTrue(true);
    }

    public static function safeColors(): array
    {
        return [
            [''],
            ['transparent'],
            ['#fff'],
            ['#ffff'],
            ['#ffffff'],
            ['#ffffffff'],
        ];
    }

    #[DataProvider('unsafeColors')]
    public function test_it_rejects_unsafe_colors(string $color): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('section', [
            'backgroundColor' => $color,
        ], null);
    }

    public static function unsafeColors(): array
    {
        return [
            ['red'],
            ['var(--brand)'],
            ['url(javascript:alert(1))'],
            ['expression(alert(1))'],
            ['#fff; background:url(javascript:alert(1))'],
        ];
    }

    #[DataProvider('safeCssLengths')]
    public function test_it_accepts_safe_css_lengths(int|float|string $length): void
    {
        $this->validator->validate('section', [
            'paddingTop' => $length,
        ], null);

        $this->assertTrue(true);
    }

    public static function safeCssLengths(): array
    {
        return [
            [0],
            [24],
            [1.5],
            ['24'],
            ['24px'],
            ['2rem'],
            ['1.5em'],
            ['50%'],
            ['100vh'],
            ['100vw'],
        ];
    }

    #[DataProvider('unsafeCssLengths')]
    public function test_it_rejects_unsafe_css_lengths(string $length): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('section', [
            'paddingTop' => $length,
        ], null);
    }

    public static function unsafeCssLengths(): array
    {
        return [
            ['-1px'],
            ['calc(100% - 1rem)'],
            ['var(--space)'],
            ['expression(alert(1))'],
            ['1px; color:red'],
        ];
    }

    public function test_it_accepts_known_enum_values(): void
    {
        $this->validator->validate('text', [
            'align' => 'center',
        ], null);

        $this->validator->validate('image', [
            'borderStyle' => 'dashed',
        ], null);

        $this->validator->validate('container', [
            'containerRole' => 'grid-item',
            'overflow' => 'visible',
            'visibility' => 'visible',
            'position' => 'relative',
        ], null);

        $this->assertTrue(true);
    }

    public function test_it_rejects_unknown_enum_values(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate('text', [
            'align' => 'evil',
        ], null);
    }

    public function test_schema_image_and_color_types_validate_semantics(): void
    {
        $schema = [
            'props' => [
                'src' => ['type' => 'image'],
                'backgroundColor' => ['type' => 'color'],
            ],
        ];

        $this->validator->validate('image', [
            'src' => '/storage/photo.jpg',
            'backgroundColor' => '#ffffff',
        ], $schema);

        $this->assertTrue(true);
    }

    public function test_max_width_accepts_none_for_layout_defaults(): void
    {
        $this->validator->validate('container', [
            'maxWidth' => 'none',
        ], null);

        $this->assertTrue(true);
    }

    public function test_container_role_preset_metadata_is_allowed(): void
    {
        $this->validator->validate('container', [
            'containerRole' => 'grid',
            'rolePreset' => [
                'role' => 'grid',
                'version' => 1,
                'applied' => [
                    'display' => 'grid',
                    'gridColumns' => 12,
                    'gap' => 24,
                ],
            ],
        ], null);

        $this->assertTrue(true);
    }
}
