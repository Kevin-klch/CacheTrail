<?php

namespace Database\Factories;

use App\Enums\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserBadge>
 */
class UserBadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $badge = fake()->randomElement(Badge::cases());

        return [
            'user_id' => User::factory(),
            'badge' => $badge,
            'level' => fake()->randomElement($badge->levels()),
            'earned_at' => now(),
            'seen_at' => now(),
        ];
    }

    public function unseen(): static
    {
        return $this->state(fn (array $attributes) => ['seen_at' => null]);
    }
}
