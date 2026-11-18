<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Always: an owner account "admin". Locally its password is "admin"; elsewhere it is only created with
 * the password from CACHETRAIL_ADMIN_PASSWORD (or a random one if demo data needs it).
 *
 * With CACHETRAIL_DEMO_DATA=true (development and test servers): demo routes, a demo community with
 * ratings, likes and badges, and the shared "Feedback" account for testers. Locally all demo accounts
 * use the password "password"; elsewhere they get random passwords, so testers can only log in with the
 * feedback account or an account of their own.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoData = config('cachetrail.feedback.demo_data');
        $adminPassword = app()->isLocal() ? 'admin' : config('cachetrail.admin_password');

        if (blank($adminPassword) && $demoData) {
            $adminPassword = Str::random(40);
        }

        if (filled($adminPassword)) {
            User::factory()->owner()->create([
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => $adminPassword,
            ]);
        }

        if (! $demoData) {
            return;
        }

        $password = fn (): string => app()->isLocal() ? 'password' : Str::random(40);

        User::factory()->supporter()->create([
            'name' => 'TestCacher',
            'email' => 'test@example.com',
            'password' => $password(),
        ]);

        User::factory()->moderator()->create([
            'name' => 'Moderator',
            'email' => 'moderator@example.com',
            'password' => $password(),
        ]);

        $this->call([TrailSeeder::class, CommunitySeeder::class, DemoCommunitySeeder::class, FeedbackAccountSeeder::class]);

        // On a test server, only the feedback account (and the admin) can be logged into.
        if (! app()->isLocal()) {
            User::query()
                ->whereNotIn('email', [config('cachetrail.feedback.email'), 'admin@example.com'])
                ->each(fn (User $user) => $user->forceFill(['password' => Str::random(40)])->save());
        }

        // Hand out the badges the demo accounts have earned with the seeded data.
        $this->command->callSilent('badges:award');
    }
}
