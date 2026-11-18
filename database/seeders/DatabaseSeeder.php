<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Feedback version: a community full of demo data plus a shared "Feedback" account for testers.
 *
 * Locally all accounts use the password "password". On a test server the demo accounts get random
 * passwords, so testers can only log in with the feedback account (password from
 * CACHETRAIL_FEEDBACK_PASSWORD) or an account they register themselves; the admin password can be
 * set with CACHETRAIL_ADMIN_PASSWORD.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = fn (): string => app()->isLocal() ? 'password' : Str::random(40);

        User::factory()->supporter()->create([
            'name' => 'TestCacher',
            'email' => 'test@example.com',
            'password' => $password(),
        ]);

        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => env('CACHETRAIL_ADMIN_PASSWORD') ?: $password(),
        ]);

        User::factory()->moderator()->create([
            'name' => 'Moderator',
            'email' => 'moderator@example.com',
            'password' => $password(),
        ]);

        $this->call([TrailSeeder::class, CommunitySeeder::class, DemoCommunitySeeder::class, FeedbackAccountSeeder::class]);

        // Demo accounts other than the shared feedback account cannot be logged into on a test server.
        if (! app()->isLocal()) {
            User::query()
                ->where('email', '!=', config('cachetrail.feedback.email'))
                ->where('email', '!=', 'admin@example.com')
                ->each(fn (User $user) => $user->forceFill(['password' => Str::random(40)])->save());
        }

        // Hand out the badges the demo accounts have earned with the seeded data.
        $this->command->callSilent('badges:award');
    }
}
