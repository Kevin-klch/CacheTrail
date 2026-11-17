<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Favorites" are split into a private bookmark list ("Für später merken") and public likes
 * ("Gefällt mir") that determine popularity. Existing favorites expressed both, so they are kept
 * as bookmarks and also copied to likes; the stored counter becomes the like count.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('trail_favorites', 'trail_bookmarks');

        Schema::create('trail_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trail_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'trail_id']);
        });

        DB::table('trail_likes')->insertUsing(
            ['user_id', 'trail_id', 'created_at', 'updated_at'],
            DB::table('trail_bookmarks')->select(['user_id', 'trail_id', 'created_at', 'updated_at']),
        );

        Schema::table('trails', function (Blueprint $table) {
            $table->renameColumn('favorite_count', 'like_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->renameColumn('like_count', 'favorite_count');
        });

        Schema::dropIfExists('trail_likes');
        Schema::rename('trail_bookmarks', 'trail_favorites');
    }
};
