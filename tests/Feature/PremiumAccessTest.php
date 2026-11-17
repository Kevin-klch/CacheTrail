<?php

namespace Tests\Feature;

use App\Contracts\PremiumAccessChecker;
use App\Models\User;
use App\Services\Premium\ConfigPremiumAccessChecker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PremiumAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function premiumRoutes(): array
    {
        return [
            'discover routes' => ['trails.index'],
            'create route' => ['trails.create'],
            'my routes' => ['trails.mine'],
            'saved for later' => ['bookmarks.index'],
        ];
    }

    #[DataProvider('premiumRoutes')]
    public function test_guests_are_redirected_to_login(string $routeName): void
    {
        $this->get(route($routeName))->assertRedirect(route('login'));
    }

    #[DataProvider('premiumRoutes')]
    public function test_premium_users_can_open_route_features(string $routeName): void
    {
        $this->grantPremiumToAllUsers(true);

        $this->actingAs(User::factory()->create())
            ->get(route($routeName))
            ->assertOk()
            ->assertDontSee('Premium-Zugang erforderlich');
    }

    #[DataProvider('premiumRoutes')]
    public function test_users_without_premium_see_an_explanation(string $routeName): void
    {
        $this->grantPremiumToAllUsers(false);

        $this->actingAs(User::factory()->create())
            ->get(route($routeName))
            ->assertForbidden()
            ->assertSee('Premium-Zugang erforderlich');
    }

    #[DataProvider('premiumRoutes')]
    public function test_admins_always_have_premium_access(string $routeName): void
    {
        $this->grantPremiumToAllUsers(false);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route($routeName))
            ->assertOk();
    }

    public function test_premium_gate_uses_the_bound_checker(): void
    {
        $user = User::factory()->create();

        $this->app->instance(PremiumAccessChecker::class, new class implements PremiumAccessChecker
        {
            public function hasPremiumAccess(User $user): bool
            {
                return $user->email === 'premium@example.com';
            }
        });

        $this->assertFalse(Gate::forUser($user)->allows('premium'));

        $user->email = 'premium@example.com';

        $this->assertTrue(Gate::forUser($user)->allows('premium'));
    }

    public function test_admin_gate_only_allows_admins(): void
    {
        $this->assertFalse(Gate::forUser(User::factory()->create())->allows('admin'));
        $this->assertTrue(Gate::forUser(User::factory()->admin()->create())->allows('admin'));
    }

    private function grantPremiumToAllUsers(bool $grant): void
    {
        $this->app->instance(PremiumAccessChecker::class, new ConfigPremiumAccessChecker($grant));
    }
}
