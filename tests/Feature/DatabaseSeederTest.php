<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_main_version_starts_without_demo_data(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('trails', 0);
        $this->assertDatabaseCount('trail_ratings', 0);
    }

    public function test_a_fresh_site_without_routes_still_works(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Noch keine Routen')
            ->assertDontSee(route('trails.example'));
    }
}
