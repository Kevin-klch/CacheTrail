<?php

namespace App\Livewire;

use App\Actions\Trails\AddCachesToTrail;
use App\Contracts\GeocacheProvider;
use App\Models\Trail;
use App\Models\TrailCache;
use App\Services\Geocaching\MockGeocacheProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Steps 2 and 3 of the route editor: add caches by GC code and arrange them in the creator's order.
 */
class TrailCacheEditor extends Component
{
    #[Locked]
    public Trail $trail;

    public string $codes = '';

    /**
     * @var array<string, string>
     */
    public array $failures = [];

    /**
     * @var list<string>
     */
    public array $added = [];

    public function mount(Trail $trail): void
    {
        Gate::authorize('update', $trail);

        $this->trail = $trail;
    }

    public function addCaches(AddCachesToTrail $addCaches): void
    {
        Gate::authorize('update', $this->trail);

        $this->validate(['codes' => ['required', 'string', 'max:2000']], attributes: ['codes' => __('GC codes')]);

        $result = $addCaches->handle($this->trail, $this->codes);

        $this->added = $result->added;
        $this->failures = $result->failures;
        $this->codes = implode("\n", array_keys($result->failures));

        $this->cachesChanged();
    }

    /**
     * Handler for wire:sort; $position is the new zero-based index.
     */
    public function sortCache(int $cacheId, int $position): void
    {
        $this->trail->moveCache($this->findCache($cacheId), $position);
        $this->cachesChanged();
    }

    public function moveUp(int $cacheId): void
    {
        $cache = $this->findCache($cacheId);
        $this->trail->moveCache($cache, $cache->position - 1);
        $this->cachesChanged();
    }

    public function moveDown(int $cacheId): void
    {
        $cache = $this->findCache($cacheId);
        $this->trail->moveCache($cache, $cache->position + 1);
        $this->cachesChanged();
    }

    public function removeCache(int $cacheId): void
    {
        $this->trail->removeCache($this->findCache($cacheId));
        $this->added = [];
        $this->cachesChanged();
    }

    public function render(GeocacheProvider $provider): View
    {
        return view('livewire.trail-cache-editor', [
            'caches' => $this->trail->caches()->get(),
            'usesExampleData' => $provider instanceof MockGeocacheProvider,
        ]);
    }

    /**
     * Refreshes the key figures shown in the editor and lets the route map redraw immediately (specification 10.4).
     */
    private function cachesChanged(): void
    {
        $this->trail->refresh()->load('caches');

        $this->dispatch('trail-caches-updated', caches: $this->trail->mapMarkers());
    }

    /**
     * Only caches of this trail can be changed, and only by someone allowed to edit it.
     */
    private function findCache(int $cacheId): TrailCache
    {
        Gate::authorize('update', $this->trail);

        return $this->trail->caches()->findOrFail($cacheId);
    }
}
