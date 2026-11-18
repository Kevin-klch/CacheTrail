<?php

namespace Database\Seeders;

use App\Actions\Trails\AddCachesToTrail;
use App\Enums\TrailAttribute;
use App\Enums\TrailShape;
use App\Enums\TrailTool;
use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Fictional demo routes with caches from the mock provider, for local development.
 */
class TrailSeeder extends Seeder
{
    public function run(AddCachesToTrail $addCaches): void
    {
        $testUser = User::where('email', 'test@example.com')->firstOrFail();
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $trails = [
            [
                'user' => $testUser,
                'name' => 'Waldabenteuer am Niederrhein',
                'shape' => TrailShape::Loop,
                'suitability' => [TrailAttribute::KidFriendly, TrailAttribute::DogFriendly],
                'parking' => [51.4716, 6.5189],
                'caches' => 'GCCT01 GCCT02 GCCT03 GCCT04 GCCT05 GCCT06 GCCT07 GCCT08',
                'minutes' => 120,
                'transportations' => [Transportation::Foot, Transportation::Bike],
                'tools' => [TrailTool::Tweezers, TrailTool::Flashlight],
                'other_tools' => 'Festes Schuhwerk',
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 2,
                'description' => "Eine abwechslungsreiche Runde durch einen alten Laubwald mit kleinen Bachläufen und einer Aussichtsplattform am Ende.\n\nDer Weg ist größtenteils unbefestigt und bei Regen auch mal matschig. Festes Schuhwerk ist empfehlenswert.\n\nParken kannst du am Waldparkplatz an der Landstraße. Von dort sind es nur wenige Meter bis zur ersten Station.",
            ],
            [
                'user' => $testUser,
                'name' => 'Zechenrunde im Revier',
                'shape' => TrailShape::Loop,
                'suitability' => [TrailAttribute::KidFriendly, TrailAttribute::StrollerFriendly],
                'caches' => 'GCCT11 GCCT12 GCCT13 GCCT14 GCCT15 GCCT16',
                'minutes' => 150,
                'transportations' => [Transportation::Foot],
                'tools' => [TrailTool::Magnet, TrailTool::Mirror, TrailTool::UvLight],
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 6,
                'description' => "Mit dieser Runde erinnern wir an den Steinkohlenbergbau, der unsere Region geprägt hat.\n\nDie Stationen führen über das ehemalige Zechengelände, vorbei an Fördertürmen und alten Werkshallen. An einigen Stellen gibt es Infotafeln zur Geschichte.\n\nGlück auf!",
            ],
            [
                'user' => $admin,
                'name' => 'Rheinufer-Radtour',
                'shape' => TrailShape::OneWay,
                'suitability' => [TrailAttribute::DogFriendly],
                'parking' => [51.3255, 6.7212],
                'caches' => 'GCCT21 GCCT22 GCCT23 GCCT24 GCCT25 GCCT26 GCCT27 GCCT28',
                'minutes' => 300,
                'transportations' => [Transportation::Bike],
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 10,
                'description' => "Eine längere Tour mit dem Rad, immer am Rheinufer entlang. Die Strecke ist fast durchgehend asphaltiert und eben.\n\nPlane genug Zeit für Pausen ein, unterwegs gibt es mehrere Biergärten und Rastplätze.",
            ],
            [
                'user' => $admin,
                'name' => 'Familienrunde am Weiher',
                'shape' => TrailShape::Loop,
                'suitability' => [TrailAttribute::KidFriendly, TrailAttribute::StrollerFriendly, TrailAttribute::DogFriendly],
                'parking' => [51.4212, 6.6012],
                'caches' => 'GCCT31 GCCT32 GCCT33 GCCT34 GCCT35',
                'minutes' => 60,
                'transportations' => [Transportation::Foot],
                'visibility' => TrailVisibility::Published,
                'published_days_ago' => 15,
                'description' => "Eine kurze Runde, ideal für Familien mit Kindern. Die Caches sind gut zu finden und kinderwagentauglich erreichbar.\n\nAm Weiher gibt es einen Spielplatz, der sich gut für eine Pause eignet.",
            ],
            [
                'user' => $testUser,
                'name' => 'Altstadt-Spaziergang',
                'caches' => 'GCCT41 GCCT42',
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
                'tools' => $data['tools'] ?? [],
                'other_tools' => $data['other_tools'] ?? null,
                'shape' => $data['shape'] ?? null,
                'suitability' => $data['suitability'] ?? [],
                'parking_latitude' => $data['parking'][0] ?? null,
                'parking_longitude' => $data['parking'][1] ?? null,
            ]);
            $trail->user()->associate($data['user']);
            $trail->visibility = $data['visibility'];
            $trail->published_at = $data['published_days_ago'] !== null ? now()->subDays($data['published_days_ago']) : null;
            $trail->save();

            $addCaches->handle($trail, $data['caches']);
        }
    }
}
