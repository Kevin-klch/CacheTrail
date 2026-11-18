<?php

namespace App\Providers;

use App\Contracts\GeocacheProvider;
use App\Contracts\PremiumAccessChecker;
use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use App\Services\Geocaching\MockGeocacheProvider;
use App\Services\Premium\ConfigPremiumAccessChecker;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Messages\MailMessage;
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
        // Outside production, fail loudly on lazy loading (N+1 queries), silently discarded attributes
        // and access to attributes that were not loaded.
        Model::shouldBeStrict(! $this->app->isProduction());

        // Short, stable names for polymorphic relations (e.g. reported items) instead of class names.
        Relation::enforceMorphMap([
            'trail' => Trail::class,
            'trail_rating' => TrailRating::class,
            'user' => User::class,
        ]);

        Gate::define('admin', fn (User $user): bool => $user->isAdmin());

        Gate::define('premium', fn (User $user): bool => $user->isAdmin()
            || $this->app->make(PremiumAccessChecker::class)->hasPremiumAccess($user));

        $this->configureAccountEmails();
    }

    /**
     * Friendly German texts for the account emails sent by Fortify; the design comes from the
     * "cachetrail" mail theme in resources/views/vendor/mail.
     */
    private function configureAccountEmails(): void
    {
        $salutation = fn (): string => __('Happy caching!')."  \n".__('Your CacheTrail team');

        VerifyEmail::toMailUsing(fn (User $user, string $url): MailMessage => (new MailMessage)
            ->subject(__('Confirm your email address'))
            ->greeting(__('Hello :name,', ['name' => $user->name]))
            ->line(__('welcome to CacheTrail! Please confirm your email address so you can discover, save and share routes.'))
            ->action(__('Confirm email address'), $url)
            ->line(__('The link is valid for :count minutes.', ['count' => config('auth.verification.expire', 60)]))
            ->line(__('If you did not create an account on CacheTrail, you can simply ignore this email.'))
            ->salutation($salutation()));

        ResetPassword::toMailUsing(fn (User $user, string $token): MailMessage => (new MailMessage)
            ->subject(__('Reset your password'))
            ->greeting(__('Hello :name,', ['name' => $user->name]))
            ->line(__('we received a request to reset the password for your CacheTrail account.'))
            ->action(__('Choose a new password'), route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()]))
            ->line(__('The link is valid for :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(__('If you did not request this, you can ignore this email. Your password stays the same.'))
            ->salutation($salutation()));
    }
}
