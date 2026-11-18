<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with local development accounts.
     * All accounts use the password "password".
     */
    public function run(): void
    {
        User::factory()->supporter()->create([
            'name' => 'TestCacher',
            'email' => 'test@example.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        User::factory()->moderator()->create([
            'name' => 'Moderator',
            'email' => 'moderator@example.com',
        ]);

        $this->call([TrailSeeder::class, CommunitySeeder::class]);

        // Hand out the badges the demo accounts have earned with the seeded data.
        $this->command->callSilent('badges:award');
    }
}
