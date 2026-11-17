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

        $this->assertDatabaseCount('users', 2);
        $this->assertSame(4, Trail::published()->count());
        $this->assertSame(5, Trail::count());
    }
}
