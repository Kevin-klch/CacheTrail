<?php

namespace Database\Factories;

use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trail>
 */
class TrailFactory extends Factory
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
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->paragraphs(2, true),
            'estimated_duration_minutes' => fake()->numberBetween(30, 480),
            'transportations' => fake()->randomElements(Transportation::cases(), fake()->numberBetween(1, count(Transportation::cases()))),
            'visibility' => TrailVisibility::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => TrailVisibility::Published,
            'published_at' => now(),
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => TrailVisibility::Hidden,
            'published_at' => now(),
        ]);
    }
}
