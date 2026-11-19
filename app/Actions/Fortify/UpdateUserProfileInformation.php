<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

/**
 * Users can change their email address here. The name is their geocaching username and cannot be
 * changed by the user (only by administrators, and later by connecting the Geocaching.com account).
 */
class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public const int EMAIL_CHANGES_PER_HOUR = 3;

    public function update(User $user, array $input): void
    {
        if ($user->isFeedbackAccount() && ($input['email'] ?? null) !== $user->email) {
            throw ValidationException::withMessages(['email' => __('The shared feedback account cannot be changed.')])
                ->errorBag('updateProfileInformation');
        }

        Validator::make($input, [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail && config('cachetrail.email_verification')) {
            // Each change sends a confirmation email to the new address, so changes are limited.
            if (RateLimiter::tooManyAttempts('email-change:'.$user->id, self::EMAIL_CHANGES_PER_HOUR)) {
                throw ValidationException::withMessages(['email' => __('You have changed your email address several times. Please try again later.')])
                    ->errorBag('updateProfileInformation');
            }

            RateLimiter::hit('email-change:'.$user->id, 3600);

            $user->forceFill([
                'email' => $input['email'],
                'email_verified_at' => null,
            ])->save();

            $user->sendEmailVerificationNotification();
        } else {
            $user->forceFill(['email' => $input['email']])->save();
        }
    }
}
