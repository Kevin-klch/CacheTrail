<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Favorite and rating figures stored on the trail for sorting by popularity and rating.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->unsignedInteger('favorite_count')->default(0)->after('start_longitude');
            $table->unsignedInteger('rating_count')->default(0)->after('favorite_count');
            $table->decimal('average_rating', 3, 2)->nullable()->after('rating_count');

            $table->index('favorite_count');
            $table->index(['rating_count', 'average_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->dropIndex(['favorite_count']);
            $table->dropIndex(['rating_count', 'average_rating']);
            $table->dropColumn(['favorite_count', 'rating_count', 'average_rating']);
        });
    }
};
