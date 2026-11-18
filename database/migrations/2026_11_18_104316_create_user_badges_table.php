<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Badges a user has earned: one row per badge and level (e.g. "completed_routes" level 10).
     * Earned badges are kept even if the underlying figure drops again later.
     */
    public function up(): void
    {
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('badge', 40);
            $table->unsignedInteger('level');
            $table->timestamp('earned_at');
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'badge', 'level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
    }
};
