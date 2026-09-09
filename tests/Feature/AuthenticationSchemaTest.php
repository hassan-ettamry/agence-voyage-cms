<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthenticationSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_authentication_schema_is_available(): void
    {
        $this->assertTrue(Schema::hasColumns('users', [
            'email_verified_at',
            'remember_token',
        ]));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertSame('varchar', Schema::getColumnType('sessions', 'user_id'));
    }

    public function test_authentication_migration_can_be_rolled_back_and_restored(): void
    {
        $migration = require database_path('migrations/2026_08_24_000001_harden_authentication_schema.php');

        $migration->down();

        $this->assertFalse(Schema::hasColumn('users', 'email_verified_at'));
        $this->assertFalse(Schema::hasColumn('users', 'remember_token'));
        $this->assertFalse(Schema::hasTable('password_reset_tokens'));
        $this->assertSame('integer', Schema::getColumnType('sessions', 'user_id'));

        $user = User::withoutGlobalScopes()->create([
            'name' => 'Existing User',
            'email' => 'existing@example.test',
            'password' => 'StrongPass1!',
        ]);

        $migration->up();

        $this->assertTrue(Schema::hasColumns('users', [
            'email_verified_at',
            'remember_token',
        ]));
        $this->assertTrue(Schema::hasTable('password_reset_tokens'));
        $this->assertSame('varchar', Schema::getColumnType('sessions', 'user_id'));
        $this->assertNotNull(
            User::withoutGlobalScopes()->findOrFail($user->id)->email_verified_at
        );
    }
}
