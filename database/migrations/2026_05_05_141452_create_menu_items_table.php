<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {

            $table->uuid('id')->primary();

            // menus.id = BIGINT
            $table->foreignId('menu_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            // pages.id = UUID
            $table->foreignUuid('page_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->uuid('parent_id')->nullable();

            $table->string('url')->nullable();

            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};