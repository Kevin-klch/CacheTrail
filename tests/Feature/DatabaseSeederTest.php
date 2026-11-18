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

        $this->assertDatabaseCount('users', 3);
        $this->assertSame(4, Trail::published()->count());
        $this->assertSame(5, Trail::count());
        $this->assertDatabaseCount('trail_caches', 29);
        $this->assertDatabaseCount('trail_ratings', 4);
        $this->assertDatabaseCount('trail_completions', 2);
        $this->assertDatabaseCount('geocaching_accounts', 1);
        $this->assertSame(1, Trail::where('slug', 'waldabenteuer-am-niederrhein')->value('like_count'));
        $this->assertTrue(Trail::published()->get()->every(fn (Trail $trail): bool => $trail->hasEnoughCachesToPublish()));
    }
}
