<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Properties of a trail: round trip or not, who it suits ("suitability", JSON list of TrailAttribute values)
     * and an optional parking spot.
     */
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->string('shape', 20)->nullable()->after('other_tools');
            $table->json('suitability')->nullable()->after('shape');
            $table->decimal('parking_latitude', 9, 6)->nullable()->after('suitability');
            $table->decimal('parking_longitude', 9, 6)->nullable()->after('parking_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->dropColumn(['shape', 'suitability', 'parking_latitude', 'parking_longitude']);
        });
    }
};
