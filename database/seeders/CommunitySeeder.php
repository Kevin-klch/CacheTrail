<?php

namespace Database\Seeders;

use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo likes, bookmarks, ratings, completions and profile data between the two local development accounts.
 */
class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        $testUser = User::where('email', 'test@example.com')->firstOrFail();
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $ratings = [
            ['waldabenteuer-am-niederrhein', $admin, 5, 'Wunderschöne Runde! Die Aussichtsplattform am Ende ist ein echtes Highlight.', 'Danke dir, freut mich sehr!'],
            ['zechenrunde-im-revier', $admin, 4, 'Toll gemacht, nur die Kauenhalle ist leider archiviert.', null],
            ['rheinufer-radtour', $testUser, 5, 'Perfekt für einen sonnigen Sonntag mit dem Rad.', null],
            ['familienrunde-am-weiher', $testUser, 4, null, null],
        ];

        // Fictional demo data until the Geocaching.com API is approved.
        $testUser->forceFill(['bio' => 'Ich cache seit Jahren am Niederrhein, am liebsten mit dem Rad.
Multis und Rätselcaches sind meine Leidenschaft.'])->save();
        $testUser->geocachingAccount()->create([
            'provider' => 'geocaching_com',
            'username' => 'TestCacher',
            'membership_status' => 'premium',
            'finds_count' => 1234,
            'member_since' => '2015-04-12',
            'last_verified_at' => now(),
        ]);

        foreach ($ratings as [$slug, $author, $stars, $comment, $reply]) {
            $trail = Trail::where('slug', $slug)->firstOrFail();

            $rating = new TrailRating(['rating' => $stars, 'comment' => $comment]);
            $rating->user()->associate($author);
            $rating->trail()->associate($trail);
            $rating->save();

            if ($reply !== null) {
                $rating->reply($reply);
            }

            $author->likedTrails()->syncWithoutDetaching([$trail->id]);
            $author->bookmarkedTrails()->syncWithoutDetaching([$trail->id]);
            $trail->refreshCommunityStatistics();
        }

        $admin->completedTrails()->attach(Trail::where('slug', 'waldabenteuer-am-niederrhein')->value('id'), ['completed_at' => now()->subDays(3)]);
        $testUser->completedTrails()->attach(Trail::where('slug', 'rheinufer-radtour')->value('id'), ['completed_at' => now()->subWeek()]);
    }
}
