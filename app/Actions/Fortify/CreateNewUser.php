<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public const int REGISTRATIONS_PER_HOUR = 5;

    public function create(array $input): User
    {
        $input['name'] = trim((string) ($input['name'] ?? ''));

        // The name is the user's geocaching username; it identifies the public profile and cannot be changed later.
        Validator::make($input, [
            'name' => User::usernameRules(),
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ], attributes: ['name' => __('Geocaching username')])->validate();

        // Every registration sends an email, so one connection may only create a few accounts per hour.
        $key = 'register:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::REGISTRATIONS_PER_HOUR)) {
            throw ValidationException::withMessages(['email' => __('Too many accounts have been created from your connection. Please try again later.')]);
        }

        RateLimiter::hit($key, 3600);

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
