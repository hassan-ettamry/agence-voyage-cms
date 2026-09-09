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
        Schema::create('permission_role', function (Blueprint $table) {
            $table->uuid('role_id');
            $table->uuid('permission_id');
        
            $table->primary(['role_id', 'permission_id']);
            $table->index('permission_id'); // performance
        
            $table->foreign('role_id')->cascadeOnDelete()->references('id')->on('roles');
            $table->foreign('permission_id')->cascadeOnDelete()->references('id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
