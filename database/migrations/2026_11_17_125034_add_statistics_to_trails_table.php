<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Key figures calculated from the caches of a trail (specification 4.4, step 4).
 * They are stored so the route directory can filter and sort by them;
 * run "php artisan trails:refresh-statistics" to calculate them for existing trails.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->unsignedSmallInteger('cache_count')->default(0)->after('transportations');
            $table->decimal('average_difficulty', 3, 2)->nullable()->after('cache_count');
            $table->decimal('average_terrain', 3, 2)->nullable()->after('average_difficulty');
            $table->unsignedInteger('total_distance_meters')->nullable()->after('average_terrain');
            $table->string('distance_method', 20)->nullable()->after('total_distance_meters');
            $table->decimal('start_latitude', 9, 6)->nullable()->after('distance_method');
            $table->decimal('start_longitude', 9, 6)->nullable()->after('start_latitude');

            $table->index('cache_count');
            $table->index('total_distance_meters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->dropIndex(['cache_count']);
            $table->dropIndex(['total_distance_meters']);
            $table->dropColumn([
                'cache_count',
                'average_difficulty',
                'average_terrain',
                'total_distance_meters',
                'distance_method',
                'start_latitude',
                'start_longitude',
            ]);
        });
    }
};
