<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (! Schema::hasColumn('menus', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }

            if (! Schema::hasColumn('menus', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('agency_id');
            }
        });

        DB::table('menus')
            ->whereNull('slug')
            ->orderBy('id')
            ->get()
            ->each(function ($menu) {
                DB::table('menus')
                    ->where('id', $menu->id)
                    ->update([
                        'slug' => Str::slug($menu->name) ?: 'menu-'.$menu->id,
                    ]);
            });

        Schema::table('menus', function (Blueprint $table) {
            $table->unique(['agency_id', 'slug']);
            $table->index(['agency_id', 'is_default']);
            $table->foreign('agency_id')->references('id')->on('agencies')->cascadeOnDelete();
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->index(['page_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['page_id', 'menu_id']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropForeign(['agency_id']);
            $table->dropUnique(['agency_id', 'slug']);
            $table->dropIndex(['agency_id', 'is_default']);
            $table->dropColumn(['slug', 'is_default']);
        });
    }
};
