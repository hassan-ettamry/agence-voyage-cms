<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('continent', 50)->nullable()->after('country');
            $table->string('region', 120)->nullable()->after('continent');
            $table->json('travel_types')->nullable()->after('description');
            $table->json('ideal_months')->nullable()->after('travel_types');
            $table->text('practical_information')->nullable()->after('ideal_months');
            $table->decimal('latitude', 10, 7)->nullable()->after('practical_information');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');

            $table->index(['agency_id', 'continent']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->string('summary', 320)->nullable()->after('description');
            $table->json('itinerary')->nullable()->after('duration_days');
            $table->json('inclusions')->nullable()->after('itinerary');
            $table->json('exclusions')->nullable()->after('inclusions');
            $table->text('practical_information')->nullable()->after('exclusions');
        });

        Schema::table('media_assets', function (Blueprint $table) {
            $table->string('copyright_holder')->nullable()->after('alt_text');
            $table->string('license', 100)->nullable()->after('copyright_holder');
            $table->string('source_url', 2048)->nullable()->after('license');
        });
    }

    public function down(): void
    {
        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropColumn(['copyright_holder', 'license', 'source_url']);
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'summary',
                'itinerary',
                'inclusions',
                'exclusions',
                'practical_information',
            ]);
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->dropIndex(['agency_id', 'continent']);
            $table->dropColumn([
                'continent',
                'region',
                'travel_types',
                'ideal_months',
                'practical_information',
                'latitude',
                'longitude',
            ]);
        });
    }
};
