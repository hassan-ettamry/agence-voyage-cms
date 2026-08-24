<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\MediaAsset;
use App\Models\Role;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Throwable;

class UploadAndBuilderHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_filename_extension_is_derived_from_detected_mime_type(): void
    {
        Storage::fake('public');
        $user = $this->admin($this->agency('Media MIME'));

        $this->actingAs($user)
            ->post(route('media.store'), [
                'file' => $this->fakePng('disguised.jpeg', 300, 200),
                'title' => 'Safe image',
            ])
            ->assertRedirect(route('media.index'));

        $media = MediaAsset::withoutGlobalScopes()->firstOrFail();
        $extension = pathinfo($media->filename, PATHINFO_EXTENSION);

        $this->assertSame('png', $extension);
        $this->assertNotSame('jpeg', $extension);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_excessive_image_dimensions_are_rejected_before_storage(): void
    {
        Storage::fake('public');
        $user = $this->admin($this->agency('Media dimensions'));

        $this->actingAs($user)
            ->post(route('media.store'), [
                'file' => $this->fakePng('too-wide.png', 6001, 10),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertDatabaseCount('media_assets', 0);
    }

    public function test_stored_media_file_is_removed_when_database_creation_fails(): void
    {
        Storage::fake('public');
        $orphanAgencyId = (string) Str::uuid();
        $user = new User;
        $user->forceFill(['agency_id' => $orphanAgencyId]);

        try {
            app(MediaService::class)->upload(
                $this->fakePng('valid.png', 200, 100),
                ['title' => 'Will fail'],
                $user
            );
            $this->fail('The media insert should violate the agency foreign key.');
        } catch (Throwable) {
            // The assertion below verifies the important cleanup side effect.
        }

        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertDatabaseCount('media_assets', 0);
    }

    public function test_profile_avatar_uses_the_same_secure_storage_pipeline(): void
    {
        Storage::fake('public');
        $agency = $this->agency('Avatar MIME');
        $user = $this->admin($agency);
        $oldPath = "agencies/{$agency->id}/avatars/old.png";
        Storage::disk('public')->put($oldPath, 'old');
        $user->forceFill(['avatar_path' => $oldPath])->save();

        $this->actingAs($user)
            ->put(route('account.profile.update'), [
                'name' => $user->name,
                'display_name' => '',
                'phone' => '',
                'bio' => '',
                'avatar' => $this->fakePng('avatar.jpeg', 200, 200),
            ])
            ->assertSessionHasNoErrors();

        $newPath = $user->fresh()->avatar_path;

        $this->assertNotSame($oldPath, $newPath);
        $this->assertContains(pathinfo($newPath, PATHINFO_EXTENSION), ['jpg', 'png', 'webp']);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_builder_render_rejects_oversized_and_invalid_payloads(): void
    {
        $user = $this->admin($this->agency('Builder limits'));

        $this->actingAs($user)
            ->postJson('/builder/render', [
                'mode' => 'unsupported',
                'structure' => [],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('mode');

        $this->actingAs($user)
            ->postJson('/builder/render', [
                'mode' => 'editor',
                'structure' => [[
                    'type' => 'text',
                    'props' => ['text' => str_repeat('x', 1_048_576)],
                ]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('structure');
    }

    private function agency(string $name): Agency
    {
        return Agency::create([
            'name' => $name,
            'slug' => str($name)->slug()->append('-', str()->random(6))->toString(),
            'email' => str()->random(8).'@agency.test',
        ]);
    }

    private function admin(Agency $agency): User
    {
        $role = Role::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        return tap(User::withoutGlobalScopes()->create([
            'agency_id' => $agency->id,
            'role_id' => $role->id,
            'name' => 'Upload admin',
            'email' => str()->random(8).'@user.test',
            'password' => 'StrongPass1!',
        ]), fn (User $user) => $user->markEmailAsVerified());
    }

    private function fakePng(string $name, int $width = 1, int $height = 1): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'secure-image-test-');
        $row = "\0".str_repeat("\0", $width * 4);
        $imageData = str_repeat($row, $height);
        $png = "\x89PNG\r\n\x1a\n"
            .$this->pngChunk('IHDR', pack('NNCCCCC', $width, $height, 8, 6, 0, 0, 0))
            .$this->pngChunk('IDAT', gzcompress($imageData, 9))
            .$this->pngChunk('IEND', '');
        file_put_contents($path, $png);

        return new UploadedFile($path, $name, 'image/png', null, true);
    }

    private function pngChunk(string $type, string $data): string
    {
        return pack('N', strlen($data))
            .$type
            .$data
            .hash('crc32b', $type.$data, true);
    }
}
