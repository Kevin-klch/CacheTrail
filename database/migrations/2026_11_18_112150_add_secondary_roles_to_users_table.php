<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Secondary roles (badges such as supporter or developer) next to the one main role.
     * Former "supporter" accounts become users with the supporter badge; former "developer" accounts
     * keep their admin rights and get the developer badge.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('secondary_roles')->nullable()->after('role');
        });

        DB::table('users')->update(['secondary_roles' => json_encode([])]);
        DB::table('users')->where('role', 'supporter')->update(['role' => 'user', 'secondary_roles' => json_encode(['supporter'])]);
        DB::table('users')->where('role', 'developer')->update(['role' => 'admin', 'secondary_roles' => json_encode(['developer'])]);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'user')->whereLike('secondary_roles', '%supporter%')->update(['role' => 'supporter']);
        DB::table('users')->where('role', 'admin')->whereLike('secondary_roles', '%developer%')->update(['role' => 'developer']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('secondary_roles');
        });
    }
};
