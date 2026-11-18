<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Equipment needed for a trail: preset tools (JSON list of TrailTool values) and free text.
     */
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->json('tools')->nullable()->after('transportations');
            $table->string('other_tools', 120)->nullable()->after('tools');
        });
    }

    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->dropColumn(['tools', 'other_tools']);
        });
    }
};
