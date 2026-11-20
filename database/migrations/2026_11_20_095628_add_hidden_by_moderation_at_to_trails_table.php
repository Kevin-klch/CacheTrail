<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remembers when the moderation team hid a route, so its creator cannot simply publish it again.
     */
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->timestamp('hidden_by_moderation_at')->nullable()->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->dropColumn('hidden_by_moderation_at');
        });
    }
};
