<?php

namespace App\Providers;

use App\Contracts\PremiumAccessChecker;
use App\Models\User;
use App\Services\Premium\ConfigPremiumAccessChecker;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
