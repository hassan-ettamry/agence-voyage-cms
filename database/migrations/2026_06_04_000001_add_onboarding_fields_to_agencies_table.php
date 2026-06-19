<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('onboarding_status')->default('pending')->index();
            $table->string('onboarding_step')->default('profile');
            $table->json('onboarding_data')->nullable();
            $table->boolean('onboarding_auto_start')->default(false)->index();
            $table->timestamp('onboarding_completed_at')->nullable();
        });

        DB::table('agencies')
            ->whereNotNull('template_applied_at')
            ->update([
                'onboarding_status' => 'completed',
                'onboarding_step' => 'review',
                'onboarding_auto_start' => false,
                'onboarding_completed_at' => DB::raw('template_applied_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('agencies', function (Blueprint $table) {
            $table->dropIndex(['onboarding_status']);
            $table->dropIndex(['onboarding_auto_start']);
            $table->dropColumn([
                'address',
                'onboarding_status',
                'onboarding_step',
                'onboarding_data',
                'onboarding_auto_start',
                'onboarding_completed_at',
            ]);
        });
    }
};
