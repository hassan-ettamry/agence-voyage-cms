<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            if (! Schema::hasColumn('destinations', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }

            if (! Schema::hasColumn('destinations', 'status')) {
                $table->string('status')->default('draft')->after('is_featured');
            }

            $table->unique(['agency_id', 'slug']);
            $table->index(['agency_id', 'status']);
            $table->index(['agency_id', 'is_featured']);
            $table->foreign('agency_id')->references('id')->on('agencies')->cascadeOnDelete();
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('agency_id');
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->timestamps();

            $table->index(['agency_id', 'created_at']);
            $table->foreign('agency_id')->references('id')->on('agencies')->cascadeOnDelete();
        });

        Schema::create('destination_media', function (Blueprint $table) {
            $table->uuid('destination_id');
            $table->uuid('media_asset_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->primary(['destination_id', 'media_asset_id']);
            $table->foreign('destination_id')->references('id')->on('destinations')->cascadeOnDelete();
            $table->foreign('media_asset_id')->references('id')->on('media_assets')->restrictOnDelete();
        });

        Schema::table('offers', function (Blueprint $table) {
            if (! Schema::hasColumn('offers', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            }

            if (! Schema::hasColumn('offers', 'media_asset_id')) {
                $table->uuid('media_asset_id')->nullable()->after('destination_id');
            }

            if (! Schema::hasColumn('offers', 'status')) {
                $table->string('status')->default('draft')->after('is_special');
            }

            $table->unique(['agency_id', 'slug']);
            $table->index(['agency_id', 'status']);
            $table->index(['agency_id', 'is_special']);
            $table->foreign('agency_id')->references('id')->on('agencies')->cascadeOnDelete();
            $table->foreign('destination_id')->references('id')->on('destinations')->nullOnDelete();
            $table->foreign('media_asset_id')->references('id')->on('media_assets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['media_asset_id']);
            $table->dropForeign(['destination_id']);
            $table->dropForeign(['agency_id']);
            $table->dropUnique(['agency_id', 'slug']);
            $table->dropIndex(['agency_id', 'status']);
            $table->dropIndex(['agency_id', 'is_special']);
            $table->dropColumn(['slug', 'media_asset_id', 'status']);
        });

        Schema::dropIfExists('destination_media');
        Schema::dropIfExists('media_assets');

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['agency_id']);
            $table->dropUnique(['agency_id', 'slug']);
            $table->dropIndex(['agency_id', 'status']);
            $table->dropIndex(['agency_id', 'is_featured']);
            $table->dropColumn(['slug', 'status']);
        });
    }
};
