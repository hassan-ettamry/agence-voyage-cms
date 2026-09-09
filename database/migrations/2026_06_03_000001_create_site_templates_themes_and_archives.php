<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('preview')->nullable();
            $table->json('variables')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('site_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('preview')->nullable();
            $table->foreignUuid('theme_id')
                ->nullable()
                ->constrained('themes')
                ->nullOnDelete();
            $table->json('pages')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();
        });

        Schema::create('site_archives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agency_id')
                ->constrained('agencies')
                ->cascadeOnDelete();
            $table->string('name');
            $table->json('snapshot');
            $table->foreignUuid('source_template_id')
                ->nullable()
                ->constrained('site_templates')
                ->nullOnDelete();
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['agency_id', 'created_at']);
        });

        Schema::table('agencies', function (Blueprint $table) {
            $table->foreignUuid('theme_id')
                ->nullable()
                ->after('status')
                ->constrained('themes')
                ->nullOnDelete();

            $table->foreignUuid('active_site_template_id')
                ->nullable()
                ->after('theme_id')
                ->constrained('site_templates')
                ->nullOnDelete();

            $table->timestamp('template_applied_at')
                ->nullable()
                ->after('active_site_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropForeign(['active_site_template_id']);
            $table->dropForeign(['theme_id']);
            $table->dropColumn([
                'theme_id',
                'active_site_template_id',
                'template_applied_at',
            ]);
        });

        Schema::dropIfExists('site_archives');
        Schema::dropIfExists('site_templates');
        Schema::dropIfExists('themes');
    }
};
