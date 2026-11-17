<?php

namespace App\Providers;

use App\Contracts\GeocacheProvider;
use App\Contracts\PremiumAccessChecker;
use App\Models\User;
use App\Services\Geocaching\MockGeocacheProvider;
use App\Services\Premium\ConfigPremiumAccessChecker;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PremiumAccessChecker::class, fn (): PremiumAccessChecker => new ConfigPremiumAccessChecker(
            grantToAllUsers: config('cachetrail.premium.grant_to_all_users'),
        ));

        $this->app->singleton(GeocacheProvider::class, fn (): GeocacheProvider => match (config('cachetrail.geocaching.provider')) {
            'mock' => new MockGeocacheProvider(config('cachetrail.geocaching.mock.data_file')),
            default => throw new InvalidArgumentException('Unknown geocache provider ['.config('cachetrail.geocaching.provider').'].'),
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn (User $user): bool => $user->isAdmin());

        Gate::define('premium', fn (User $user): bool => $user->isAdmin()
            || $this->app->make(PremiumAccessChecker::class)->hasPremiumAccess($user));
    }
}
