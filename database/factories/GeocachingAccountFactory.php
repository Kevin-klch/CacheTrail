<?php

namespace Database\Factories;

use App\Models\GeocachingAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GeocachingAccount>
 */
class GeocachingAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider' => 'geocaching_com',
            'username' => fake()->userName(),
            'membership_status' => 'premium',
            'finds_count' => fake()->numberBetween(10, 5000),
            'member_since' => fake()->dateTimeBetween('-15 years', '-1 year'),
            'last_verified_at' => now(),
        ];
    }
}
