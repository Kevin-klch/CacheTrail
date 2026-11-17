<?php

namespace Database\Factories;

use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrailRating>
 */
class TrailRatingFactory extends Factory
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
            'trail_id' => Trail::factory()->published(),
            'rating' => fake()->numberBetween(TrailRating::MIN_STARS, TrailRating::MAX_STARS),
            'comment' => fake()->optional()->sentence(),
        ];
    }
}
