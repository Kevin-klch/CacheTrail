<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\View\View;

class HomeController extends Controller
{
    public const int LATEST_TRAILS_COUNT = 3;

    public function __invoke(): View
    {
        return view('home', [
            'latestTrails' => Trail::published()->with(['user', 'caches:id,trail_id,position,latitude,longitude'])->latest('published_at')->limit(self::LATEST_TRAILS_COUNT)->get(),
        ]);
    }
}
