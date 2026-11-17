<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoritesController extends Controller
{
    /**
     * The user's favorite routes that are still published, most recently saved first.
     */
    public function __invoke(Request $request): View
    {
        return view('favorites.index', [
            'trails' => $request->user()->favoriteTrails()
                ->published()
                ->with(['user', 'caches:id,trail_id,position,latitude,longitude'])
                ->orderByPivot('created_at', 'desc')
                ->paginate(TrailController::PER_PAGE),
        ]);
    }
}
