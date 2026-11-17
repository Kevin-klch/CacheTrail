<?php

namespace App\Http\Controllers;

use App\Contracts\GeocacheProvider;
use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use App\Http\Requests\StoreTrailRequest;
use App\Http\Requests\UpdateTrailRequest;
use App\Models\Trail;
use App\Services\Geocaching\MockGeocacheProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TrailController extends Controller
{
    public const int PER_PAGE = 12;

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Trail::class);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'transportation' => ['nullable', Rule::enum(Transportation::class)],
            'min_hours' => ['nullable', 'numeric', 'min:0', 'max:72'],
            'max_hours' => ['nullable', 'numeric', 'min:0', 'max:72'],
        ]);

        $trails = Trail::query()
            ->published()
            ->with(['user', 'caches:id,trail_id,position,latitude,longitude'])
            ->when($filters['q'] ?? null, function (Builder $query, string $search): void {
                $query->where(fn (Builder $query) => $query
                    ->whereLike('name', "%{$search}%")
                    ->orWhereLike('description', "%{$search}%"));
            })
            ->when($filters['transportation'] ?? null, function (Builder $query, string $transportation): void {
                $query->suitableFor(Transportation::from($transportation));
            })
            ->when(isset($filters['min_hours']), fn (Builder $query) => $query->where('estimated_duration_minutes', '>=', (int) round($filters['min_hours'] * 60)))
            ->when(isset($filters['max_hours']), fn (Builder $query) => $query->where('estimated_duration_minutes', '<=', (int) round($filters['max_hours'] * 60)))
            ->latest('published_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('trails.index', [
            'trails' => $trails,
            'filters' => $filters,
            'view' => $request->query('view') === 'detailed' ? 'detailed' : 'compact',
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
