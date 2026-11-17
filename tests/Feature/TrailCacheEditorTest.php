<?php

namespace Tests\Feature;

use App\Enums\TrailVisibility;
use App\Livewire\TrailCacheEditor;
use App\Models\Trail;
use App\Models\TrailCache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TrailCacheEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_caches_page_can_be_rendered_by_the_owner(): void
    {
        $trail = Trail::factory()->create();

        $this->actingAs($trail->user)
            ->get(route('trails.caches', $trail))
            ->assertOk()
            ->assertSeeLivewire(TrailCacheEditor::class);
    }

    public function test_other_users_cannot_open_the_caches_page(): void
    {
        $trail = Trail::factory()->published()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('trails.caches', $trail))
            ->assertForbidden();
    }

    public function test_caches_can_be_added_and_failures_stay_in_the_input(): void
    {
        $trail = Trail::factory()->create();

        Livewire::actingAs($trail->user)
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->set('codes', 'GCCT01, GCCT02, GC99999')
            ->call('addCaches')
            ->assertSet('added', ['GCCT01', 'GCCT02'])
            ->assertSet('codes', 'GC99999')
            ->assertSee('Der alte Baum')
            ->assertSee('Zu diesem Code wurde kein Cache gefunden.');

        $this->assertSame(2, $trail->caches()->count());
    }

    public function test_an_empty_input_shows_a_validation_error(): void
    {
        $trail = Trail::factory()->create();

        Livewire::actingAs($trail->user)
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->call('addCaches')
            ->assertHasErrors(['codes' => 'required']);
    }

    public function test_caches_can_be_reordered_by_drag_and_drop_and_arrows(): void
    {
        $trail = Trail::factory()->withCaches(4)->create();
        [$first, $second, $third, $fourth] = $trail->caches()->get()->all();

        $component = Livewire::actingAs($trail->user)->test(TrailCacheEditor::class, ['trail' => $trail]);

        $component->call('sortCache', $first->id, 2);
        $this->assertSame([$second->id, $third->id, $first->id, $fourth->id], $trail->caches()->pluck('id')->all());

        $component->call('moveUp', $fourth->id);
        $this->assertSame([$second->id, $third->id, $fourth->id, $first->id], $trail->caches()->pluck('id')->all());

        $component->call('moveDown', $second->id);
        $this->assertSame([$third->id, $second->id, $fourth->id, $first->id], $trail->caches()->pluck('id')->all());

        $this->assertSame([0, 1, 2, 3], $trail->caches()->pluck('position')->all());
    }

    public function test_moving_beyond_the_ends_keeps_the_order_intact(): void
    {
        $trail = Trail::factory()->withCaches(2)->create();
        [$first, $second] = $trail->caches()->get()->all();

        Livewire::actingAs($trail->user)
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->call('moveUp', $first->id)
            ->call('moveDown', $second->id);

        $this->assertSame([$first->id, $second->id], $trail->caches()->pluck('id')->all());
    }

    public function test_a_cache_can_be_removed_and_positions_are_closed(): void
    {
        $trail = Trail::factory()->withCaches(3)->create();
        [$first, $second, $third] = $trail->caches()->get()->all();

        Livewire::actingAs($trail->user)
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->call('removeCache', $second->id);

        $this->assertModelMissing($second);
        $this->assertSame([$first->id, $third->id], $trail->caches()->pluck('id')->all());
        $this->assertSame([0, 1], $trail->caches()->pluck('position')->all());
    }

    public function test_caches_of_other_trails_cannot_be_changed(): void
    {
        $trail = Trail::factory()->create();
        $foreignCache = TrailCache::factory()->create();

        Livewire::actingAs($trail->user)
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->call('removeCache', $foreignCache->id)
            ->assertNotFound();

        $this->assertModelExists($foreignCache);
    }

    public function test_other_users_cannot_use_the_editor(): void
    {
        $trail = Trail::factory()->withCaches(1)->create();

        Livewire::actingAs(User::factory()->create())
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->assertForbidden();
    }

    public function test_publishing_requires_at_least_two_caches(): void
    {
        $trail = Trail::factory()->withCaches(1)->create();

        Livewire::actingAs($trail->user)
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->call('publish')
            ->assertHasErrors('publish');

        $this->assertSame(TrailVisibility::Draft, $trail->fresh()->visibility);
    }

    public function test_a_trail_with_enough_caches_can_be_published(): void
    {
        $trail = Trail::factory()->withCaches(2)->create();

        Livewire::actingAs($trail->user)
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->call('publish')
            ->assertRedirect(route('trails.show', $trail));

        $this->assertSame(TrailVisibility::Published, $trail->fresh()->visibility);
        $this->assertNotNull($trail->fresh()->published_at);
    }

    public function test_admins_can_edit_caches_of_any_trail(): void
    {
        $trail = Trail::factory()->create();

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(TrailCacheEditor::class, ['trail' => $trail])
            ->set('codes', 'GCCT01')
            ->call('addCaches')
            ->assertSet('added', ['GCCT01']);
    }
}
