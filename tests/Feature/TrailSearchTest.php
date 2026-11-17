<?php

namespace Tests\Feature;

use App\Enums\Transportation;
use App\Http\Controllers\TrailController;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_trails_can_be_searched_by_name_and_description_case_insensitively(): void
    {
        Trail::factory()->published()->create(['name' => 'Waldrunde', 'description' => 'Schöne Tour.']);
        Trail::factory()->published()->create(['name' => 'Stadtrunde', 'description' => 'Durch den WALD am Stadtrand.']);
        Trail::factory()->published()->create(['name' => 'Seerunde', 'description' => 'Am Wasser.']);

        $this->get(route('trails.index', ['q' => 'wald']))
            ->assertOk()
            ->assertSee('Waldrunde')
            ->assertSee('Stadtrunde')
            ->assertDontSee('Seerunde');
    }

    public function test_the_transport_filter_matches_trails_that_include_it(): void
    {
        Trail::factory()->published()->create(['name' => 'Fussrunde', 'transportations' => [Transportation::Foot]]);
        Trail::factory()->published()->create(['name' => 'Radrunde', 'transportations' => [Transportation::Bike]]);
        Trail::factory()->published()->create(['name' => 'Kombirunde', 'transportations' => [Transportation::Foot, Transportation::Bike]]);

        $this->get(route('trails.index', ['transportation' => 'foot']))
            ->assertSee('Fussrunde')
            ->assertSee('Kombirunde')
            ->assertDontSee('Radrunde');

        $this->get(route('trails.index', ['transportation' => 'bike']))
            ->assertSee('Radrunde')
            ->assertSee('Kombirunde')
            ->assertDontSee('Fussrunde');
    }

    public function test_trails_can_be_filtered_by_duration(): void
    {
        Trail::factory()->published()->create(['name' => 'Kurzrunde', 'estimated_duration_minutes' => 45]);
        Trail::factory()->published()->create(['name' => 'Mittelrunde', 'estimated_duration_minutes' => 120]);
        Trail::factory()->published()->create(['name' => 'Langrunde', 'estimated_duration_minutes' => 400]);

        $this->get(route('trails.index', ['min_hours' => 1, 'max_hours' => 3]))
            ->assertSee('Mittelrunde')
            ->assertDontSee('Kurzrunde')
            ->assertDontSee('Langrunde');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->get(route('trails.index', ['transportation' => 'car']))
            ->assertSessionHasErrors('transportation');
    }

    public function test_results_are_sorted_by_newest_and_paginated(): void
    {
        Trail::factory()->published()->create(['name' => 'Älteste Runde', 'published_at' => now()->subYear()]);
        Trail::factory()->published()->count(TrailController::PER_PAGE)->create(['published_at' => now()]);

        $this->get(route('trails.index'))->assertDontSee('Älteste Runde');
        $this->get(route('trails.index', ['page' => 2]))->assertSee('Älteste Runde');
    }

    public function test_the_compact_view_is_the_default(): void
    {
        Trail::factory()->published()->withCaches(2)->create(['name' => 'Waldrunde']);

        $this->get(route('trails.index'))
            ->assertOk()
            ->assertSee('Waldrunde')
            ->assertDontSee('Route ansehen');
    }

    public function test_the_detailed_view_shows_large_cards_and_keeps_filters(): void
    {
        Trail::factory()->published()->withCaches(2)->create(['name' => 'Waldrunde', 'transportations' => [Transportation::Foot]]);

        $this->get(route('trails.index', ['view' => 'detailed', 'transportation' => 'foot']))
            ->assertOk()
            ->assertSee('Waldrunde')
            ->assertSee('Route ansehen')
            ->assertSee('<input type="hidden" name="view" value="detailed">', false)
            ->assertSee('view=compact', false);
    }

    public function test_unknown_views_fall_back_to_compact(): void
    {
        $this->get(route('trails.index', ['view' => 'fancy']))
            ->assertOk()
            ->assertDontSee('<input type="hidden" name="view" value="detailed">', false);
    }
}
