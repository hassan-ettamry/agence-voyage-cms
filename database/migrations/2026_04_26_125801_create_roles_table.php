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
        Schema::create('roles', function (Blueprint $table) {
            $table->uuid('id')->primary();
        
            $table->uuid('agency_id');
            $table->string('name');
            $table->string('slug');
        
            $table->timestamps();
        
            $table->unique(['agency_id', 'slug']);
        
            $table->foreign('agency_id')
                ->references('id')
                ->on('agencies')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
