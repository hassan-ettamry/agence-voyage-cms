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
}
