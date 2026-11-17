<?php

namespace App\Http\Controllers;

use App\Contracts\GeocacheProvider;
use App\Enums\TrailVisibility;
use App\Http\Requests\StoreTrailRequest;
use App\Http\Requests\TrailIndexRequest;
use App\Http\Requests\UpdateTrailRequest;
use App\Models\Trail;
use App\Queries\TrailDirectory;
use App\Services\Geocaching\MockGeocacheProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TrailController extends Controller
{
    public const int PER_PAGE = 12;

    /**
     * Maximum number of routes drawn in the map view of the directory.
     */
    public const int MAP_LIMIT = 500;

    public function index(TrailIndexRequest $request, TrailDirectory $directory): View
    {
        $filters = $request->filters();
        $location = $request->location();
        $view = $request->view();
        $query = $directory->query($filters, $request->sort(), $location);

        $trails = $view === 'map'
            ? $query->whereNotNull('start_latitude')->limit(self::MAP_LIMIT)->get()
            : $query->with(['user', 'caches:id,trail_id,position,latitude,longitude'])->paginate(self::PER_PAGE)->withQueryString();

        return view('trails.index', [
            'trails' => $trails,
            'filters' => $filters,
            'sort' => $request->sort(),
            'view' => $view,
            'location' => $location,
            'mapMarkers' => $view === 'map' ? $trails->map(fn (Trail $trail): array => $trail->toDirectoryMarker($location))->all() : [],
        ]);
    }

    public function show(Trail $trail, GeocacheProvider $geocacheProvider): View
    {
        Gate::authorize('view', $trail);

        return view('trails.show', [
            'trail' => $trail->load(['user', 'caches']),
            'geocacheProvider' => $geocacheProvider,
            'usesExampleData' => $geocacheProvider instanceof MockGeocacheProvider,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Trail::class);

        return view('trails.create', ['trail' => new Trail]);
    }

    public function store(StoreTrailRequest $request): RedirectResponse
    {
        $trail = new Trail($request->trailAttributes());
        $trail->user()->associate($request->user());
        $trail->changeVisibility(TrailVisibility::Draft);

        if ($request->hasFile('image')) {
            $trail->image_path = $this->storeImage($request->file('image'));
        }

        $trail->save();

        return redirect()->route('trails.caches', $trail)->with('status', 'trail-created');
    }

    /**
     * Steps 2 and 3 of the route editor, handled by the TrailCacheEditor Livewire component.
     */
    public function caches(Trail $trail): View
    {
        Gate::authorize('update', $trail);

        return view('trails.caches', ['trail' => $trail]);
    }

    /**
     * Step 3 of the route editor: shows the route as others will see it before it is published.
     */
    public function preview(Trail $trail, GeocacheProvider $geocacheProvider): View
    {
        Gate::authorize('update', $trail);

        return view('trails.preview', [
            'trail' => $trail->load(['user', 'caches']),
            'geocacheProvider' => $geocacheProvider,
            'usesExampleData' => $geocacheProvider instanceof MockGeocacheProvider,
        ]);
    }

    public function publish(Trail $trail): RedirectResponse
    {
        Gate::authorize('update', $trail);

        if (! $trail->hasEnoughCachesToPublish()) {
            return redirect()->route('trails.caches', $trail)->withErrors([
                'publish' => __('A route needs at least :count caches to be published.', ['count' => Trail::minCachesToPublish()]),
            ]);
        }

        $trail->changeVisibility(TrailVisibility::Published);
        $trail->save();

        return redirect()->route('trails.show', $trail)->with('status', 'trail-published');
    }

    public function edit(Trail $trail): View
    {
        Gate::authorize('update', $trail);

        return view('trails.edit', ['trail' => $trail]);
    }

    public function update(UpdateTrailRequest $request, Trail $trail): RedirectResponse
    {
        $trail->fill($request->trailAttributes());
        $trail->changeVisibility($request->visibility());

        if ($request->hasFile('image') || $request->boolean('remove_image')) {
            $this->deleteImage($trail);
        }

        if ($request->hasFile('image')) {
            $trail->image_path = $this->storeImage($request->file('image'));
        }

        $trail->save();

        return redirect()->route('trails.show', $trail)->with('status', 'trail-updated');
    }

    public function destroy(Trail $trail): RedirectResponse
    {
        Gate::authorize('delete', $trail);

        $trail->delete();

        return redirect()->route('trails.mine')->with('status', 'trail-deleted');
    }

    private function storeImage(UploadedFile $image): string
    {
        return $image->store('trails', 'public');
    }

    private function deleteImage(Trail $trail): void
    {
        if ($trail->image_path) {
            Storage::disk('public')->delete($trail->image_path);
            $trail->image_path = null;
        }
    }
}
