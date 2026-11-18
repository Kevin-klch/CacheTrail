<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Illustrated profile pictures and cover images (SVG) for the demo community, generated here so no
 * photos have to be downloaded. Users themselves can only upload JPG, PNG or WebP.
 */
class DemoProfileImagesSeeder extends Seeder
{
    /**
     * Per demo account: avatar background, avatar motif, and the colours of the cover landscape
     * (sky from, sky to, far hills, middle hills, front hills) plus an extra element.
     *
     * @var array<string, array{background: string, motif: string, sky: array{0: string, 1: string}, hills: array{0: string, 1: string, 2: string}, extra: string}>
     */
    private const array PEOPLE = [
        'rheinpirat@example.com' => [
            'background' => '#3d5a80', 'motif' => 'waves',
            'sky' => ['#f4a261', '#fbe3c4'], 'hills' => ['#8fa3b8', '#5b7590', '#2f4560'], 'extra' => 'river',
        ],
        'mia.moorhuhn@example.com' => [
            'background' => '#cc5a3d', 'motif' => 'puzzle',
            'sky' => ['#2d2a4a', '#6b5b95'], 'hills' => ['#4a4373', '#332f57', '#1f1c3a'], 'extra' => 'stars',
        ],
        'kaffee@example.com' => [
            'background' => '#7a5230', 'motif' => 'coffee',
            'sky' => ['#fde9c9', '#f7f2e6'], 'hills' => ['#d9c8a4', '#b89b6a', '#7a5230'], 'extra' => 'sun',
        ],
        'traditom@example.com' => [
            'background' => '#284c39', 'motif' => 'box',
            'sky' => ['#cfe3d4', '#eef4ef'], 'hills' => ['#7fa88b', '#3a6b4f', '#1a3226'], 'extra' => 'trees',
        ],
        'waldlaeufer@example.com' => [
            'background' => '#1a3226', 'motif' => 'tree',
            'sky' => ['#dfe7e1', '#b9c9bd'], 'hills' => ['#a3b8a9', '#6f8c78', '#3b5a47'], 'extra' => 'mist',
        ],
    ];

    public function run(): void
    {
        $disk = Storage::disk('public');

        foreach (self::PEOPLE as $email => $look) {
            $user = User::query()->where('email', $email)->first();

            if ($user === null) {
                continue;
            }

            $key = substr(md5($email), 0, 8);
            $avatar = "avatars/demo-{$key}.svg";
            $cover = "covers/demo-{$key}.svg";

            $disk->put($avatar, $this->avatar($look['background'], $look['motif']));
            $disk->put($cover, $this->cover($look['sky'], $look['hills'], $look['extra']));

            $user->forceFill(['avatar_path' => $avatar, 'cover_path' => $cover])->save();
        }
    }

    private function avatar(string $background, string $motif): string
    {
        $shape = match ($motif) {
            'waves' => '<circle cx="132" cy="70" r="22" fill="#f4a261"/><path d="M30 112c18-14 34-14 52 0s34 14 52 0 34-14 52 0M30 140c18-14 34-14 52 0s34 14 52 0 34-14 52 0" fill="none" stroke="#fff" stroke-width="12" stroke-linecap="round"/>',
            'puzzle' => '<path d="M60 60h30a16 16 0 1 1 32 0h18v32a16 16 0 1 0 0 32v26h-28a16 16 0 1 0-32 0H60v-30a16 16 0 1 1 0-32Z" fill="#fff"/>',
            'coffee' => '<path d="M52 92h82v34a30 30 0 0 1-30 30H82a30 30 0 0 1-30-30Z" fill="#fff"/><path d="M134 100h10a16 16 0 0 1 0 32h-12" fill="none" stroke="#fff" stroke-width="10"/><path d="M76 74c-8-10 8-16 0-28M98 74c-8-10 8-16 0-28M120 74c-8-10 8-16 0-28" fill="none" stroke="#f7e3c4" stroke-width="7" stroke-linecap="round"/>',
            'box' => '<rect x="50" y="78" width="100" height="64" rx="8" fill="#c9971c"/><rect x="50" y="66" width="100" height="22" rx="6" fill="#e0b53b"/><rect x="88" y="62" width="24" height="12" rx="4" fill="#fff"/><path d="M70 110h60" stroke="#8a6512" stroke-width="6" stroke-linecap="round"/>',
            'tree' => '<path d="M100 36 140 96h-20l28 40H52l28-40H60Z" fill="#7fa88b"/><rect x="92" y="136" width="16" height="28" rx="3" fill="#cc5a3d"/>',
        };

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="400" height="400">
            <rect width="200" height="200" fill="{$background}"/>
            {$shape}
            </svg>
            SVG;
    }

    /**
     * @param  array{0: string, 1: string}  $sky
     * @param  array{0: string, 1: string, 2: string}  $hills
     */
    private function cover(array $sky, array $hills, string $extra): string
    {
        [$far, $middle, $front] = $hills;

        $decoration = match ($extra) {
            'river' => '<circle cx="1180" cy="150" r="70" fill="#ffd8a8"/><path d="M0 360c300-40 520 10 800-20s520-40 800 0v60H0Z" fill="#9cc3d5" opacity=".85"/>',
            'stars' => collect(range(1, 40))->map(fn (int $i): string => sprintf('<circle cx="%d" cy="%d" r="%.1f" fill="#fff" opacity=".8"/>', ($i * 397) % 1600, 20 + ($i * 131) % 200, 1 + ($i % 3) * .7))->implode('').'<circle cx="1320" cy="90" r="40" fill="#f6efd8"/>',
            'sun' => '<circle cx="1250" cy="170" r="90" fill="#f4c27a" opacity=".8"/>',
            'trees' => collect(range(0, 31))->map(fn (int $i): string => sprintf('<path d="M%1$d %2$dl26 70h-52Z" fill="%3$s"/>', $i * 52 + 20, 250 + ($i * 37) % 40, $front))->implode(''),
            'mist' => '<rect y="200" width="1600" height="60" fill="#fff" opacity=".35"/><rect y="290" width="1600" height="40" fill="#fff" opacity=".3"/>',
        };

        return <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1600 400" width="1600" height="400" preserveAspectRatio="xMidYMid slice">
            <defs><linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{$sky[0]}"/><stop offset="1" stop-color="{$sky[1]}"/></linearGradient></defs>
            <rect width="1600" height="400" fill="url(#sky)"/>
            <path d="M0 250c180-70 330-90 520-40s340 40 520-30 360-60 560 10v210H0Z" fill="{$far}"/>
            <path d="M0 300c200-50 380-40 560 0s400 20 600-30 300-30 440 10v120H0Z" fill="{$middle}"/>
            {$decoration}
            <path d="M0 350c260-30 520-20 800 10s520 10 800-20v60H0Z" fill="{$front}"/>
            </svg>
            SVG;
    }
}
