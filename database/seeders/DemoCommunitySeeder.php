<?php

namespace Database\Seeders;

use App\Actions\Trails\AddCachesToTrail;
use App\Enums\SecondaryRole;
use App\Enums\TrailTool;
use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Five fictional geocachers with filled profiles, routes, ratings, likes, bookmarks and completed routes,
 * so the community features can be tried out with realistic data. All accounts use the password "password".
 * The people, Geocaching.com figures and caches are made up.
 */
class DemoCommunitySeeder extends Seeder
{
    public function run(AddCachesToTrail $addCaches): void
    {
        $users = $this->createUsers();
        $this->createTrails($addCaches, $users);
        $this->createActivity($users);
    }

    /**
     * @return array<string, User>
     */
    private function createUsers(): array
    {
        $people = [
            'Rheinpirat' => [
                'email' => 'rheinpirat@example.com',
                'member_for' => now()->subYears(3)->subMonths(2),
                'bio' => "Seit 2011 mit GPS und Kaffee am Rhein unterwegs. Am liebsten große Runden mit dem Rad und Industriekultur im Ruhrgebiet.\n\nWenn ihr bei meinen Routen Fragen habt: einfach eine Bewertung schreiben, ich antworte immer.",
                'geocaching' => ['finds' => 4821, 'since' => '2011-06-18', 'membership' => 'premium'],
            ],
            'Mia.Moorhuhn' => [
                'email' => 'mia.moorhuhn@example.com',
                'member_for' => now()->subYears(2)->subMonths(5),
                'bio' => 'Rätselfan und Mama von zwei kleinen Cachern. Mysteries knacke ich am liebsten abends auf dem Sofa, die Dosen holen wir dann am Wochenende zusammen.',
                'geocaching' => ['finds' => 1377, 'since' => '2017-03-02', 'membership' => 'premium'],
                'secondary_roles' => [SecondaryRole::Supporter],
            ],
            'KaffeeUndKoordinaten' => [
                'email' => 'kaffee@example.com',
                'member_for' => now()->subYear()->subMonths(4),
                'bio' => 'Feierabend-Cacher aus Kamp-Lintfort. Kurze Runden, schöne Orte, danach ein Kaffee: so mag ich das.',
                'geocaching' => ['finds' => 612, 'since' => '2019-09-14', 'membership' => 'basic'],
            ],
            'TradiTom' => [
                'email' => 'traditom@example.com',
                'member_for' => now()->subMonths(8),
                'bio' => 'Tradis, Tradis, Tradis. Je mehr Dosen auf einer Runde, desto besser. Klettercaches nur mit Seil und Helm!',
                'geocaching' => ['finds' => 9150, 'since' => '2009-05-30', 'membership' => 'premium'],
            ],
            'Waldläufer_NRW' => [
                'email' => 'waldlaeufer@example.com',
                'member_for' => now()->subWeeks(3),
                'bio' => null,
                'geocaching' => null,
            ],
        ];

        $users = [];

        foreach ($people as $name => $person) {
            $user = User::factory()->create([
                'name' => $name,
                'email' => $person['email'],
                'created_at' => $person['member_for'],
                'secondary_roles' => $person['secondary_roles'] ?? [],
            ]);
            $user->forceFill(['bio' => $person['bio']])->save();

            if ($person['geocaching'] !== null) {
                $user->geocachingAccount()->create([
                    'provider' => 'geocaching_com',
                    'username' => $name,
                    'membership_status' => $person['geocaching']['membership'],
                    'finds_count' => $person['geocaching']['finds'],
                    'member_since' => $person['geocaching']['since'],
                    'last_verified_at' => now(),
                ]);
            }

            $users[$name] = $user;
        }

        return $users;
    }

    /**
     * @param  array<string, User>  $users
     */
    private function createTrails(AddCachesToTrail $addCaches, array $users): void
    {
        $trails = [
            [
                'user' => 'Mia.Moorhuhn',
                'name' => 'Römerspuren in Xanten',
                'caches' => 'GCCT61 GCCT62 GCCT63 GCCT64 GCCT65 GCCT66',
                'minutes' => 150,
                'transportations' => [Transportation::Foot],
                'tools' => [TrailTool::Flashlight],
                'other_tools' => 'Stift und Notizblock für den Multi',
                'published_days_ago' => 40,
                'description' => "Eine Runde durch die Römerstadt: vom Hafentempel über das Amphitheater bis zum Klever Tor. Unterwegs gibt es viel Geschichte und einen kleinen Rätselcache.\n\nDer Multi am Amphitheater braucht ein paar Zahlen von den Infotafeln, nehmt euch also etwas Zeit.\n\nKinderwagentauglich bis auf das kurze Stück am Legionärsweg.",
            ],
            [
                'user' => 'Rheinpirat',
                'name' => 'Hochofen-Tour im Landschaftspark',
                'caches' => 'GCCT67 GCCT68 GCCT69 GCCT70 GCCT71 GCCT72',
                'minutes' => 180,
                'transportations' => [Transportation::Foot],
                'tools' => [TrailTool::Flashlight, TrailTool::Gloves, TrailTool::ClimbingGear],
                'other_tools' => null,
                'published_days_ago' => 25,
                'description' => "Industriekultur pur: Erzbunker, Gasometer und natürlich Hochofen 5. Die Dose dort oben ist nur mit Klettergurt erreichbar, alle anderen Stationen sind ganz normal zu Fuß machbar.\n\nAbends wird der Park bunt beleuchtet, dann ist die Runde besonders schön.",
            ],
            [
                'user' => 'KaffeeUndKoordinaten',
                'name' => 'Klosterrunde Kamp',
                'caches' => 'GCCT73 GCCT74 GCCT75 GCCT76 GCCT77',
                'minutes' => 75,
                'transportations' => [Transportation::Foot],
                'tools' => [],
                'other_tools' => null,
                'published_days_ago' => 18,
                'description' => "Kurze Feierabendrunde rund um das Kloster Kamp mit dem schönen Terrassengarten. Ideal für den Einstieg.\n\nDanach unbedingt einen Kaffee im Klostercafé trinken!",
            ],
            [
                'user' => 'Mia.Moorhuhn',
                'name' => 'Rätselpfad im Stadtwald',
                'caches' => 'GCCT78 GCCT79 GCCT80 GCCT81 GCCT82',
                'minutes' => 120,
                'transportations' => [Transportation::Foot, Transportation::Bike],
                'tools' => [TrailTool::UvLight, TrailTool::Magnifier],
                'other_tools' => 'Handy mit Morse-App',
                'published_days_ago' => 9,
                'description' => "Fünf Rätsel, eine Schatzkiste: Zahlenschloss, Morsezeichen, Caesar-Verschlüsselung und ein Sudoku führen euch zum Final.\n\nDie Rätsel lassen sich vorab zu Hause lösen, vor Ort braucht ihr dann nur noch die UV-Lampe.",
            ],
            [
                'user' => 'Rheinpirat',
                'name' => 'Deichtour Wesel',
                'caches' => 'GCCT83 GCCT84 GCCT85 GCCT86 GCCT87 GCCT88',
                'minutes' => 210,
                'transportations' => [Transportation::Bike],
                'tools' => [],
                'other_tools' => 'Fahrradschloss',
                'published_days_ago' => 4,
                'description' => "Mit dem Rad von der Zitadelle über die Rheinbrücke zur Lippemündung und am Deich entlang zum Auesee. Fast alles auf asphaltierten Wegen.\n\nAchtung: Auf dem Deich stehen oft Schafe, bitte langsam fahren.",
            ],
            [
                'user' => 'KaffeeUndKoordinaten',
                'name' => 'Feierabendrunde Moers',
                'caches' => 'GCCT89 GCCT90 GCCT91 GCCT92',
                'minutes' => 45,
                'transportations' => [Transportation::Foot, Transportation::Bike],
                'tools' => [],
                'other_tools' => null,
                'published_days_ago' => null,
                'description' => 'Noch in Arbeit: eine kurze Runde durch den Schlosspark und an der Stadtmauer entlang.',
            ],
        ];

        foreach ($trails as $data) {
            $trail = new Trail([
                'name' => $data['name'],
                'description' => $data['description'],
                'estimated_duration_minutes' => $data['minutes'],
                'transportations' => $data['transportations'],
                'tools' => $data['tools'],
                'other_tools' => $data['other_tools'],
            ]);
            $trail->user()->associate($users[$data['user']]);
            $trail->visibility = $data['published_days_ago'] === null ? TrailVisibility::Draft : TrailVisibility::Published;
            $trail->published_at = $data['published_days_ago'] === null ? null : now()->subDays($data['published_days_ago']);
            $trail->save();

            $addCaches->handle($trail, $data['caches']);
        }
    }

    /**
     * Ratings (with replies), likes, bookmarks and completed routes between the demo users and the
     * existing local accounts.
     *
     * @param  array<string, User>  $users
     */
    private function createActivity(array $users): void
    {
        $users += User::query()->whereIn('email', ['test@example.com', 'admin@example.com'])->get()->keyBy('name')->all();

        // [route name, author, stars, comment, reply of the creator, completed days ago]
        $ratings = [
            ['Römerspuren in Xanten', 'Rheinpirat', 5, 'Super gemacht, der Multi am Amphitheater hat richtig Spaß gemacht!', 'Danke! Freut mich, dass du ihn geknackt hast.', 30],
            ['Römerspuren in Xanten', 'TradiTom', 4, 'Schöne Runde, für mich hätten es noch ein paar mehr Dosen sein dürfen.', null, 21],
            ['Römerspuren in Xanten', 'TestCacher', 5, null, null, 12],
            ['Hochofen-Tour im Landschaftspark', 'TradiTom', 5, 'Hochofen 5 mit Seil war ein Highlight! Unbedingt abends machen.', 'Genau so ist es gedacht, danke dir!', 14],
            ['Hochofen-Tour im Landschaftspark', 'KaffeeUndKoordinaten', 4, 'Ohne Klettern trotzdem eine tolle Runde. Die Sinteranlage war leider deaktiviert.', null, 10],
            ['Klosterrunde Kamp', 'Mia.Moorhuhn', 5, 'Perfekt mit Kindern, und der Kuchen im Klostercafé war auch super.', 'Danke, Mia! Der Kuchen gehört einfach dazu.', 11],
            ['Klosterrunde Kamp', 'Waldläufer_NRW', 4, 'Meine erste Route hier, sehr entspannt.', null, 6],
            ['Rätselpfad im Stadtwald', 'Rheinpirat', 3, 'Knackige Rätsel! Die Morsezeichen haben mich fast zur Verzweiflung gebracht.', 'Haha, das war Absicht. 😉', 5],
            ['Deichtour Wesel', 'TradiTom', 5, 'Schöne Strecke für eine Radtour, die Schafe waren auch da.', null, 2],
            ['Zechenrunde im Revier', 'Rheinpirat', 5, 'Glück auf! Tolle Idee für eine Revier-Runde.', null, 20],
            ['Waldabenteuer am Niederrhein', 'Mia.Moorhuhn', 4, 'Schöner Wald, nach Regen aber wirklich matschig.', null, 16],
        ];

        foreach ($ratings as [$routeName, $authorName, $stars, $comment, $reply, $completedDaysAgo]) {
            $trail = Trail::where('name', $routeName)->first();
            $author = $users[$authorName] ?? null;

            if ($trail === null || $author === null) {
                continue;
            }

            $author->completedTrails()->syncWithoutDetaching([$trail->id => ['completed_at' => now()->subDays($completedDaysAgo)]]);

            $rating = new TrailRating(['rating' => $stars, 'comment' => $comment]);
            $rating->user()->associate($author);
            $rating->trail()->associate($trail);
            $rating->save();

            if ($reply !== null) {
                $rating->reply($reply);
            }

            if ($stars >= 4) {
                $author->likedTrails()->syncWithoutDetaching([$trail->id]);
            }
        }

        // Bookmarks for later and a few more likes without a rating.
        $extra = [
            'Waldläufer_NRW' => ['bookmarks' => ['Hochofen-Tour im Landschaftspark', 'Deichtour Wesel', 'Römerspuren in Xanten'], 'likes' => []],
            'TradiTom' => ['bookmarks' => ['Rätselpfad im Stadtwald'], 'likes' => ['Klosterrunde Kamp']],
            'KaffeeUndKoordinaten' => ['bookmarks' => ['Deichtour Wesel', 'Rheinufer-Radtour'], 'likes' => ['Römerspuren in Xanten']],
            'TestCacher' => ['bookmarks' => ['Rätselpfad im Stadtwald'], 'likes' => ['Hochofen-Tour im Landschaftspark', 'Deichtour Wesel']],
            'Admin' => ['bookmarks' => [], 'likes' => ['Römerspuren in Xanten', 'Klosterrunde Kamp']],
        ];

        foreach ($extra as $name => $lists) {
            $user = $users[$name] ?? null;

            if ($user === null) {
                continue;
            }

            $user->bookmarkedTrails()->syncWithoutDetaching(Trail::whereIn('name', $lists['bookmarks'])->pluck('id'));
            $user->likedTrails()->syncWithoutDetaching(Trail::whereIn('name', $lists['likes'])->pluck('id'));
        }

        Trail::query()->each(fn (Trail $trail) => $trail->refreshCommunityStatistics());
    }
}
