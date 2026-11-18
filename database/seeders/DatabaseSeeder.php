<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * The main version starts empty: no demo accounts, routes or ratings.
 *
 * Register the first account on the website and make it the owner with
 * "php artisan users:make-owner <email>". The fictional geocaches the route editor needs until the
 * Geocaching.com API is approved are not seeded; they come from database/data/mock-geocaches.json.
 *
 * Demo data for trying out the site lives on the "feedback" branch.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //
    }
}
