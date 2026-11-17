<?php

namespace Tests\Feature;

use App\Contracts\PremiumAccessChecker;
use App\Models\User;
use App\Services\Premium\ConfigPremiumAccessChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_rendered_for_guests(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Hier beginnt dein Abenteuer.')
            ->assertSee(route('login'))
            ->assertSee(route('register'))
            ->assertDontSee(route('profile.edit'))
            ->assertDontSee('Meine Routen');
    }

    public function test_premium_users_see_all_navigation_items_unlocked(): void
    {
        $this->app->instance(PremiumAccessChecker::class, new ConfigPremiumAccessChecker(true));

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('trails.index'))
            ->assertSee(route('trails.create'))
            ->assertSee(route('trails.mine'))
            ->assertSee(route('favorites.index'))
            ->assertSee(route('profile.edit'))
            ->assertDontSee('(Premium-Zugang erforderlich)')
            ->assertDontSee(route('register'));
    }

    public function test_users_without_premium_see_locked_navigation_items(): void
    {
        $this->app->instance(PremiumAccessChecker::class, new ConfigPremiumAccessChecker(false));

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertOk()
            ->assertSee(route('trails.index'))
            ->assertSee('(Premium-Zugang erforderlich)');
    }
}
