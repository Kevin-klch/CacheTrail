<?php

namespace App\Http\Controllers;

use App\Enums\TrailSort;
use App\Support\UserLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Stores or removes the visitor's approximate location in the session (never in URLs or the database).
 */
class UserLocationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        UserLocation::remember($request->session(), (float) $validated['latitude'], (float) $validated['longitude']);

        return redirect()->to($this->directoryUrl($request, ['sort' => TrailSort::Nearest->value]));
    }

    public function destroy(Request $request): RedirectResponse
    {
        UserLocation::forget($request->session());

        return redirect()->to($this->directoryUrl($request, ['sort' => null, 'radius' => null]));
    }

    /**
     * Back to the directory with the current search (sent as "return_query"), adjusted by the given values.
     *
     * @param  array<string, string|null>  $overrides
     */
    private function directoryUrl(Request $request, array $overrides): string
    {
        parse_str((string) $request->input('return_query', ''), $query);

        $query = array_filter(
            [...$query, ...$overrides, 'page' => null],
            fn (mixed $value): bool => is_string($value) && $value !== '',
        );

        return route('trails.index', $query);
    }
}
