<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Connection of a CacheTrail account with an external geocaching account (specification 9.2),
 * plus the public profile figures shown on the CacheTrail profile. The OAuth fields stay empty until
 * the Geocaching.com API has been approved (phase 6); their exact form follows the official API.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('geocaching_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('provider', 30);
            $table->string('external_user_id')->nullable();
            $table->string('username');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('membership_status', 30)->nullable();
            $table->unsignedInteger('finds_count')->nullable();
            $table->date('member_since')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geocaching_accounts');
    }
};
