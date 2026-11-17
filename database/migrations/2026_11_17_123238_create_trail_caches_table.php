<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The caches of a trail in their user-defined order. Cache details are stored as a snapshot
 * from the provider; whether this is allowed for real API data must be clarified (specification 9.4).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trail_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trail_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('provider', 30);
            $table->string('cache_code', 10);
            $table->string('cache_name');
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->string('cache_type', 30);
            $table->decimal('difficulty', 2, 1);
            $table->decimal('terrain', 2, 1);
            $table->string('cache_status', 30);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->unique(['trail_id', 'cache_code']);
            $table->index(['trail_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trail_caches');
    }
};
