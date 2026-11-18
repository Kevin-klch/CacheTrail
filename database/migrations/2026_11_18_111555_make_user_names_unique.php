<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The name is now the geocaching username and identifies the profile (/nutzer/TestCacher),
     * so it must be unique regardless of upper and lower case. Existing duplicates get a number.
     */
    public function up(): void
    {
        $taken = [];

        foreach (DB::table('users')->orderBy('id')->get(['id', 'name']) as $user) {
            $name = trim($user->name);
            $candidate = $name;
            $suffix = 2;

            while (isset($taken[mb_strtolower($candidate)])) {
                $candidate = $name.' '.$suffix++;
            }

            $taken[mb_strtolower($candidate)] = true;

            if ($candidate !== $user->name) {
                DB::table('users')->where('id', $user->id)->update(['name' => $candidate]);
            }
        }

        // MySQL and MariaDB compare case-insensitively with the default collation and know no index on lower().
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('users', fn (Blueprint $table) => $table->unique('name', 'users_name_lower_unique'));

            return;
        }

        DB::statement('create unique index users_name_lower_unique on users (lower(name))');
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropUnique('users_name_lower_unique'));
    }
};
