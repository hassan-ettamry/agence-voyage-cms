<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unique(['agency_id', 'slug'], 'pages_agency_id_slug_unique');
            $table->index(['agency_id', 'status'], 'pages_agency_id_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique('pages_agency_id_slug_unique');
            $table->dropIndex('pages_agency_id_status_index');
        });
    }
};
