<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
            $table->string('avatar_path')->nullable()->after('role_id');
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('language', 20)->default('en')->after('bio');
            $table->string('timezone')->default('Africa/Casablanca')->after('language');
            $table->string('date_format', 40)->default('M d, Y')->after('timezone');
            $table->string('time_format', 10)->default('24h')->after('date_format');
            $table->json('profile_preferences')->nullable()->after('time_format');
            $table->timestamp('last_login_at')->nullable()->after('profile_preferences');
            $table->timestamp('password_changed_at')->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'display_name',
                'avatar_path',
                'phone',
                'bio',
                'language',
                'timezone',
                'date_format',
                'time_format',
                'profile_preferences',
                'last_login_at',
                'password_changed_at',
            ]);
        });
    }
};
