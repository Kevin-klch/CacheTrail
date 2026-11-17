<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarksController extends Controller
{
    /**
     * The user's private list "Für später merken": published routes, most recently saved first.
     */
    public function __invoke(Request $request): View
    {
        return view('bookmarks.index', [
            'trails' => $request->user()->bookmarkedTrails()
                ->published()
                ->with(['user', 'caches:id,trail_id,position,latitude,longitude'])
                ->orderByPivot('created_at', 'desc')
                ->paginate(TrailController::PER_PAGE),
        ]);
    }
}
