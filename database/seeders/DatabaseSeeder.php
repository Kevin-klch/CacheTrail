<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The main version starts without demo data (no routes, ratings or community accounts).
 *
 * For local development there is an owner account to log in with right away: username "admin",
 * password "admin". Outside local development this account is only created when CACHETRAIL_ADMIN_PASSWORD
 * is set, and then uses that password; otherwise register the first account on the website and make it
 * the owner with "php artisan users:make-owner <email>".
 *
 * Demo data for trying out the site lives on the "feedback" branch.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = app()->isLocal() ? 'admin' : config('cachetrail.admin_password');

        if (blank($password)) {
            return;
        }

        User::factory()->owner()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => $password,
        ]);
    }
}
