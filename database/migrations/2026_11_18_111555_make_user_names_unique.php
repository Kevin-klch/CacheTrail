<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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

        DB::statement('create unique index users_name_lower_unique on users (lower(name))');
    }

    public function down(): void
    {
        DB::statement('drop index users_name_lower_unique');
    }
};
