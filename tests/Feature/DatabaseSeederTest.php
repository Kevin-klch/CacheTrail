<?php

namespace Tests\Feature;

use App\Models\Trail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_can_be_seeded(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 8);
        $this->assertSame(9, Trail::published()->count());
        $this->assertSame(11, Trail::count());
        $this->assertDatabaseCount('trail_caches', 61);
        $this->assertDatabaseCount('trail_ratings', 15);
        $this->assertDatabaseCount('trail_completions', 13);
        $this->assertDatabaseCount('geocaching_accounts', 5);
        $this->assertSame(2, Trail::where('slug', 'waldabenteuer-am-niederrhein')->value('like_count'));
        $this->assertTrue(Trail::published()->get()->every(fn (Trail $trail): bool => $trail->hasEnoughCachesToPublish()));
    }
}
