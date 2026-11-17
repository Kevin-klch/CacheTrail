<?php

namespace Database\Seeders;

use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Fictional demo routes for local development.
 */
class TrailSeeder extends Seeder
{
    public function run(): void
    {
        $testUser = User::where('email', 'test@example.com')->firstOrFail();
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $trails = [
            [
                'user' => $testUser,
                'name' => 'Waldabenteuer am Niederrhein',
                'minutes' => 120,
                'transportations' => [Transportation::Foot, Transportation::Bike],
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 2,
                'description' => "Eine abwechslungsreiche Runde durch einen alten Laubwald mit kleinen Bachläufen und einer Aussichtsplattform am Ende.\n\nDer Weg ist größtenteils unbefestigt und bei Regen auch mal matschig. Festes Schuhwerk ist empfehlenswert.\n\nParken kannst du am Waldparkplatz an der Landstraße. Von dort sind es nur wenige Meter bis zur ersten Station.",
            ],
            [
                'user' => $testUser,
                'name' => 'Zechenrunde im Revier',
                'minutes' => 150,
                'transportations' => [Transportation::Foot],
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 6,
                'description' => "Mit dieser Runde erinnern wir an den Steinkohlenbergbau, der unsere Region geprägt hat.\n\nDie Stationen führen über das ehemalige Zechengelände, vorbei an Fördertürmen und alten Werkshallen. An einigen Stellen gibt es Infotafeln zur Geschichte.\n\nGlück auf!",
            ],
            [
                'user' => $admin,
                'name' => 'Rheinufer-Radtour',
                'minutes' => 300,
                'transportations' => [Transportation::Bike],
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 10,
                'description' => "Eine längere Tour mit dem Rad, immer am Rheinufer entlang. Die Strecke ist fast durchgehend asphaltiert und eben.\n\nPlane genug Zeit für Pausen ein, unterwegs gibt es mehrere Biergärten und Rastplätze.",
            ],
            [
                'user' => $admin,
                'name' => 'Familienrunde am Weiher',
                'minutes' => 60,
                'transportations' => [Transportation::Foot],
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 15,
                'description' => "Eine kurze Runde, ideal für Familien mit Kindern. Die Caches sind gut zu finden und kinderwagentauglich erreichbar.\n\nAm Weiher gibt es einen Spielplatz, der sich gut für eine Pause eignet.",
            ],
            [
                'user' => $testUser,
                'name' => 'Altstadt-Spaziergang',
                'minutes' => 90,
                'transportations' => [Transportation::Foot],
                'visibility' => TrailVisibility::Draft,
                'published_days_ago' => null,
                'description' => 'Ein gemütlicher Spaziergang durch die historische Altstadt. Diese Route ist noch in Arbeit.',
            ],
        ];

        foreach ($trails as $data) {
            $trail = new Trail([
                'name' => $data['name'],
                'description' => $data['description'],
                'estimated_duration_minutes' => $data['minutes'],
                'transportations' => $data['transportations'],
            ]);
            $trail->user()->associate($data['user']);
            $trail->visibility = $data['visibility'];
            $trail->published_at = $data['published_days_ago'] !== null ? now()->subDays($data['published_days_ago']) : null;
            $trail->save();
        }
    }
}
