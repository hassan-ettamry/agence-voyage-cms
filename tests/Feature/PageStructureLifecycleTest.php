<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Component;
use App\Models\Page;
use App\Models\PageVersion;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PageService;
use App\Services\Renderer\ComponentRegistry;
use App\Services\Renderer\ComponentValidator;
use App\Services\Renderer\PageRenderer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PageStructureLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_page_creation_defaults_to_array_root_structure(): void
    {
        $agency = $this->agency();
        $user = $this->userWithPermissions($agency, ['page.update']);
        $service = app(PageService::class);

        $page = $service->create([
            'title' => 'Array Root Page',
        ], $user);

        $this->assertSame([], $page->structure);
    }

    public function test_legacy_object_root_is_canonicalized_and_persisted_on_edit_load(): void
    {
        $agency = $this->agency();
        $user = $this->userWithPermissions($agency, ['page.update']);
        $page = $this->page($agency, [
            'type' => 'page',
            'children' => [
                $this->node('section'),
            ],
        ]);

        $this->actingAs($user)
            ->get(route('pages.edit', $page))
            ->assertOk();

        $this->assertSame([
            $this->node('section'),
        ], $page->fresh()->structure);
    }

    public function test_restore_canonicalizes_legacy_version_structure(): void
    {
        $agency = $this->agency();
        $user = $this->user($agency);
        $page = $this->page($agency, [
            $this->node('text'),
        ]);
        $version = PageVersion::create([
            'page_id' => $page->id,
            'structure' => [
                'type' => 'page',
                'children' => [
                    $this->node('section'),
                ],
            ],
            'version' => 1,
            'created_by' => $user->id,
        ]);

        app(PageService::class)->restore($page, $version, $user);

        $this->assertSame([
            $this->node('section'),
        ], $page->fresh()->structure);

        $this->assertSame([
            $this->node('text'),
        ], $page->versions()->where('version', 2)->first()->structure);
    }

    public function test_duplicate_preserves_array_root_shape(): void
    {
        $agency = $this->agency();
        $user = $this->user($agency);
        $page = $this->page($agency, [
            'type' => 'page',
            'children' => [
                $this->node('section'),
            ],
        ]);

        $duplicate = app(PageService::class)->duplicate($page, $user);

        $this->assertSame([
            $this->node('section'),
        ], $duplicate->structure);
    }

    public function test_publish_canonicalizes_legacy_page_structure(): void
    {
        $agency = $this->agency();
        $page = $this->page($agency, [
            'type' => 'page',
            'children' => [
                $this->node('section'),
            ],
        ]);

        app(PageService::class)->publish($page);

        $page = $page->fresh();

        $this->assertSame(Page::STATUS_PUBLISHED, $page->status);
        $this->assertSame([
            $this->node('section'),
        ], $page->structure);
    }

    public function test_structure_canonicalization_removes_list_props_and_preserves_layout_props(): void
    {
        $service = app(PageService::class);

        $structure = $service->canonicalizeStructure([
            [
                'id' => 'section',
                'type' => 'section',
                'props' => [],
                'children' => [
                    [
                        'id' => 'parent-container',
                        'type' => 'container',
                        'props' => [
                            'display' => 'grid',
                            'gridColumns' => 12,
                        ],
                        'children' => [
                            [
                                'id' => 'child-container',
                                'type' => 'container',
                                'props' => [
                                    'legacy-list-value',
                                ],
                                'children' => [],
                            ],
                            [
                                'id' => 'span-container',
                                'type' => 'container',
                                'props' => [
                                    'gridSpan' => 7,
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $parentProps = $structure[0]['children'][0]['props'];
        $invalidChildProps = $structure[0]['children'][0]['children'][0]['props'];
        $spanChildProps = $structure[0]['children'][0]['children'][1]['props'];

        $this->assertSame('grid', $parentProps['display']);
        $this->assertSame(12, $parentProps['gridColumns']);
        $this->assertSame(['containerRole' => 'grid-item'], $invalidChildProps);
        $this->assertSame(7, $spanChildProps['gridSpan']);
        $this->assertSame('grid', $parentProps['containerRole']);
        $this->assertSame('grid-item', $spanChildProps['containerRole']);
    }

    public function test_container_role_is_inferred_without_rewriting_existing_props(): void
    {
        $structure = app(PageService::class)->canonicalizeStructure([
            [
                'id' => 'section',
                'type' => 'section',
                'props' => [],
                'children' => [
                    [
                        'id' => 'layout-container',
                        'type' => 'container',
                        'props' => [
                            'maxWidth' => 980,
                            'paddingLeft' => 8,
                        ],
                        'children' => [
                            [
                                'id' => 'content-container',
                                'type' => 'container',
                                'props' => [
                                    'containerRole' => 'card',
                                    'paddingTop' => 32,
                                    'backgroundColor' => '#ffffff',
                                ],
                                'children' => [
                                    [
                                        'id' => 'text',
                                        'type' => 'text',
                                        'props' => [
                                            'text' => 'Hello',
                                        ],
                                        'children' => [],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $layoutProps = $structure[0]['children'][0]['props'];
        $cardProps = $structure[0]['children'][0]['children'][0]['props'];

        $this->assertSame('layout', $layoutProps['containerRole']);
        $this->assertSame(980, $layoutProps['maxWidth']);
        $this->assertSame(8, $layoutProps['paddingLeft']);

        $this->assertSame('card', $cardProps['containerRole']);
        $this->assertSame(32, $cardProps['paddingTop']);
        $this->assertSame('#ffffff', $cardProps['backgroundColor']);
    }

    public function test_page_structure_save_preserves_container_grid_layout_props(): void
    {
        $agency = $this->agency();
        $user = $this->user($agency);
        $page = $this->page($agency, []);

        app(PageService::class)->updateStructure($page, [
            [
                'id' => 'section',
                'type' => 'section',
                'props' => [],
                'children' => [
                    [
                        'id' => 'parent-container',
                        'type' => 'container',
                        'props' => [
                            'display' => 'grid',
                            'gridColumns' => '12',
                        ],
                        'children' => [
                            [
                                'id' => 'child-container',
                                'type' => 'container',
                                'props' => [
                                    'gridSpan' => '7',
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ], $user);

        $structure = $page->fresh()->structure;
        $parentProps = $structure[0]['children'][0]['props'];
        $childProps = $structure[0]['children'][0]['children'][0]['props'];

        $this->assertSame('grid', $parentProps['display']);
        $this->assertSame('12', $parentProps['gridColumns']);
        $this->assertSame('7', $childProps['gridSpan']);
    }

    public function test_builder_structure_json_outputs_empty_props_as_object(): void
    {
        $structure = app(PageService::class)->structureForBuilder([
            [
                'id' => 'container',
                'type' => 'container',
                'props' => [],
                'children' => [],
            ],
        ]);

        $this->assertStringContainsString(
            '"props":{"containerRole":"group"}',
            json_encode($structure)
        );
    }

    public function test_builder_structure_preserves_container_role_preset_metadata(): void
    {
        $structure = app(PageService::class)->structureForBuilder([
            [
                'id' => 'container',
                'type' => 'container',
                'props' => [
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
                ],
                'children' => [],
            ],
        ]);

        $this->assertSame(
            'grid',
            $structure[0]['props']['rolePreset']['role']
        );

        $this->assertSame(
            24,
            $structure[0]['props']['rolePreset']['applied']['gap']
        );
    }

    public function test_renderer_renders_array_roots_and_rejects_object_roots(): void
    {
        Component::create([
            'type' => 'text',
            'name' => 'Text',
            'category' => 'basic',
            'schema_json' => [
                'props' => [
                    'text' => ['type' => 'text'],
                ],
            ],
        ]);

        Cache::flush();

        $renderer = new PageRenderer(
            new ComponentRegistry,
            new ComponentValidator
        );

        $html = $renderer->render([
            [
                'id' => 'node-text',
                'type' => 'text',
                'props' => [
                    'text' => 'Rendered text',
                ],
            ],
        ], 'live');

        $this->assertStringContainsString('Rendered text', $html);
        $this->assertSame('', $renderer->render([
            'type' => 'text',
            'props' => [
                'text' => 'Should not render',
            ],
        ], 'live'));
    }

    public function test_builder_render_rejects_object_root_payloads(): void
    {
        $agency = $this->agency();
        $user = $this->user($agency);

        $this->actingAs($user)
            ->postJson('/builder/render', [
                'mode' => 'editor',
                'structure' => [
                    'type' => 'page',
                    'children' => [],
                ],
            ])
            ->assertStatus(422);
    }

    public function test_renderer_outputs_real_grid_spans_for_nested_containers(): void
    {
        foreach (['container'] as $type) {
            Component::create([
                'type' => $type,
                'name' => str($type)->title()->toString(),
                'category' => 'layout',
                'schema_json' => [
                    'props' => [],
                ],
            ]);
        }

        Cache::flush();

        $renderer = new PageRenderer(
            new ComponentRegistry,
            new ComponentValidator
        );

        $html = $renderer->render([
            [
                'id' => 'container-grid',
                'type' => 'container',
                'props' => [
                    'display' => 'grid',
                    'gridColumns' => 12,
                    'gap' => 20,
                ],
                'children' => [
                    [
                        'id' => 'container-1',
                        'type' => 'container',
                        'props' => [
                            'gridSpan' => 7,
                        ],
                        'children' => [],
                    ],
                    [
                        'id' => 'container-2',
                        'type' => 'container',
                        'props' => [
                            'gridSpan' => 5,
                        ],
                        'children' => [],
                    ],
                ],
            ],
        ], 'editor');

        $this->assertStringContainsString('grid-template-columns', $html);
        $this->assertStringContainsString('repeat', $html);
        $this->assertStringContainsString('12', $html);
        $this->assertStringContainsString('grid-column: span 7 / span 7;', $html);
        $this->assertStringContainsString('grid-column: span 5 / span 5;', $html);
    }

    public function test_component_validator_limits_container_grid_props(): void
    {
        $validator = new ComponentValidator;

        $validator->validate('container', [
            'display' => 'grid',
            'containerRole' => 'grid',
            'gridSpan' => 12,
            'gridColumns' => 12,
            'gap' => 20,
            'justifyContent' => 'center',
            'alignItems' => 'stretch',
            'flexDirection' => 'row',
        ], null);

        try {
            $validator->validate('container', [
                'gridSpan' => 13,
            ], null);

            $this->fail('Grid span values above 12 should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString(
                'Invalid value for gridSpan',
                $exception->errors()['structure'][0]
            );
        }
    }

    private function agency(): Agency
    {
        $name = fake()->company().' '.uniqid();

        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    private function user(Agency $agency): User
    {
        return User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'agency_id' => $agency->id,
        ]);
    }

    private function userWithPermissions(Agency $agency, array $permissionSlugs): User
    {
        $role = Role::create([
            'agency_id' => $agency->id,
            'name' => 'Member '.uniqid(),
        ]);

        foreach ($permissionSlugs as $slug) {
            $role->permissions()->attach(
                Permission::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => str($slug)->replace('.', ' ')->title()->toString()]
                )
            );
        }

        return User::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'agency_id' => $agency->id,
            'role_id' => $role->id,
        ]);
    }

    private function page(Agency $agency, array $structure): Page
    {
        return Page::create([
            'agency_id' => $agency->id,
            'title' => 'Test Page '.uniqid(),
            'slug' => 'test-page-'.uniqid(),
            'structure' => $structure,
            'status' => Page::STATUS_DRAFT,
        ]);
    }

    private function node(string $type): array
    {
        return [
            'id' => 'node-'.$type,
            'type' => $type,
            'props' => [],
            'children' => [],
        ];
    }
}
