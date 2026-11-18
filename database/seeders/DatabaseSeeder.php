<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with local development accounts.
     * Both accounts use the password "password".
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        $this->call([TrailSeeder::class, CommunitySeeder::class]);

        // Hand out the badges the demo accounts have earned with the seeded data.
        $this->command->callSilent('badges:award');
    }
}
