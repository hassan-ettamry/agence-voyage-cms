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
