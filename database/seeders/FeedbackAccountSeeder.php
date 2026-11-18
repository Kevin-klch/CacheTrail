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
            'bio' => "Willkommen! Mit diesem Konto kannst du CacheTrail ausprobieren: Routen entdecken, bewerten, als absolviert markieren und eigene Routen anlegen.\n\nDas Konto wird von allen Testerinnen und Testern gemeinsam genutzt.",
        ])->save();

        // Testers cannot like or save routes with the shared account, so a few are marked for them to see how it looks.
        $user->bookmarkedTrails()->attach(Trail::whereIn('name', ['Römerspuren in Xanten', 'Deichtour Wesel', 'Rheinufer-Radtour'])->pluck('id'));

        $likedTrails = Trail::whereIn('name', ['Römerspuren in Xanten', 'Klosterrunde Kamp', 'Waldabenteuer am Niederrhein'])->get();
        $user->likedTrails()->attach($likedTrails->pluck('id'));
        $likedTrails->each->refreshCommunityStatistics();

        $user->completedTrails()->attach(Trail::where('name', 'Klosterrunde Kamp')->value('id'), ['completed_at' => now()->subDay()]);
    }
}
