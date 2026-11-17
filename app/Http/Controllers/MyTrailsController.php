<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MyTrailsController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('trails.mine', [
            'trails' => $request->user()->trails()->withCount('caches')->latest('updated_at')->paginate(TrailController::PER_PAGE),
        ]);
    }
}
