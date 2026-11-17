<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\View\View;

class HomeController extends Controller
{
    public const int LATEST_TRAILS_COUNT = 3;

    public const int POPULAR_TRAILS_COUNT = 3;

    public function __invoke(): View
    {
        $withPreview = ['user', 'caches:id,trail_id,position,latitude,longitude'];

        return view('home', [
            'latestTrails' => Trail::published()->with($withPreview)->latest('published_at')->limit(self::LATEST_TRAILS_COUNT)->get(),
            'popularTrails' => Trail::published()->with($withPreview)
                ->where('like_count', '>', 0)
                ->orderByDesc('like_count')
                ->orderByDesc('average_rating')
                ->limit(self::POPULAR_TRAILS_COUNT)
                ->get(),
        ]);
    }
}
