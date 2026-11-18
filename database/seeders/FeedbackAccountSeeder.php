<?php

namespace Database\Seeders;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The shared account testers log in with to try out CacheTrail. Its email address, password, and the
 * account's own profile cannot be changed and it cannot be deleted (see User::isFeedbackAccount()).
 */
class FeedbackAccountSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Feedback',
            'email' => config('cachetrail.feedback.email'),
            'password' => config('cachetrail.feedback.password'),
            'created_at' => now()->subDays(2),
        ]);

        $user->forceFill([
            'bio' => "Willkommen! Mit diesem Konto kannst du CacheTrail ausprobieren: Routen entdecken, merken, bewerten, als absolviert markieren und eigene Routen anlegen.\n\nDas Konto wird von allen Testerinnen und Testern gemeinsam genutzt.",
        ])->save();

        // A little activity, so the lists are not empty on the first visit.
        $user->bookmarkedTrails()->attach(Trail::whereIn('name', ['Römerspuren in Xanten', 'Deichtour Wesel'])->pluck('id'));
        $user->completedTrails()->attach(Trail::where('name', 'Klosterrunde Kamp')->value('id'), ['completed_at' => now()->subDay()]);
    }
}
