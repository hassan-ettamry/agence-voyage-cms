<?php

namespace Tests\Unit;

use App\Services\PageStructureValidator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PageStructureValidatorTest extends TestCase
{
    private PageStructureValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new PageStructureValidator;
    }

    public function test_it_accepts_empty_array_root(): void
    {
        $this->validator->validate([]);

        $this->assertTrue(true);
    }

    public function test_it_accepts_nested_array_root_structure(): void
    {
        $this->validator->validate([
            [
                'type' => 'section',
                'props' => [],
                'children' => [
                    [
                        'type' => 'text',
                        'props' => [
                            'text' => 'Hello',
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertTrue(true);
    }

    public function test_it_rejects_legacy_object_root_structure(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate([
            'type' => 'page',
            'children' => [],
        ]);
    }

    public function test_it_rejects_non_list_children(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate([
            [
                'type' => 'section',
                'children' => [
                    'first' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ]);
    }

    public function test_it_rejects_more_than_the_component_limit(): void
    {
        $structure = array_fill(0, PageStructureValidator::MAX_NODES + 1, [
            'type' => 'text',
            'props' => [],
        ]);

        $this->expectException(ValidationException::class);

        app(PageStructureValidator::class)->validate($structure);
    }

    public function test_it_rejects_structures_nested_too_deeply(): void
    {
        $node = ['type' => 'text', 'props' => []];

        for ($depth = 0; $depth < PageStructureValidator::MAX_DEPTH; $depth++) {
            $node = [
                'type' => 'container',
                'props' => [],
                'children' => [$node],
            ];
        }

        $this->expectException(ValidationException::class);

        app(PageStructureValidator::class)->validate([$node]);
    }

    public function test_it_rejects_structures_larger_than_one_megabyte(): void
    {
        $structure = [[
            'type' => 'text',
            'props' => [
                'text' => str_repeat('x', PageStructureValidator::MAX_BYTES),
            ],
        ]];

        $this->expectException(ValidationException::class);

        app(PageStructureValidator::class)->validate($structure);
    }
}
