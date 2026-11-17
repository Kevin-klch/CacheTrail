<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ratings of routes (specification 9.6): 1 to 5 stars with an optional comment,
 * one per user and route, plus one optional reply by the creator of the route.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trail_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trail_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->text('creator_reply')->nullable();
            $table->timestamp('creator_replied_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'trail_id']);
            $table->index(['trail_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trail_ratings');
    }
};
