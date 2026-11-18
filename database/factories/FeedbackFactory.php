<?php

namespace Database\Factories;

use App\Enums\FeedbackCategory;
use App\Models\Feedback;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(FeedbackCategory::cases()),
            'message' => fake()->paragraph(),
            'contact' => fake()->optional()->safeEmail(),
            'page_url' => url('/'),
        ];
    }

    public function done(): static
    {
        return $this->state(fn (): array => ['done_at' => now()]);
    }
}
