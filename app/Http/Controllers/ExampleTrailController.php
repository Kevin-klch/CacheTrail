<?php

namespace App\Http\Controllers;

use App\Contracts\GeocacheProvider;
use App\Queries\ExampleTrail;
use App\Services\Geocaching\MockGeocacheProvider;
use Illuminate\View\View;

/**
 * Public, read-only detail page of the example route, so visitors can see what a route looks like
 * before signing up.
 */
class ExampleTrailController extends Controller
{
    public function __invoke(ExampleTrail $exampleTrail, GeocacheProvider $geocacheProvider): View
    {
        $trail = $exampleTrail->find();

        abort_if($trail === null, 404);

        return view('trails.example', [
            'trail' => $trail,
            'geocacheProvider' => $geocacheProvider,
            'usesExampleData' => $geocacheProvider instanceof MockGeocacheProvider,
        ]);
    }
}
