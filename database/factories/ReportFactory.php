<?php

namespace Database\Factories;

use App\Enums\ReportReason;
use App\Models\Report;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
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
            'reportable_type' => (new Trail)->getMorphClass(),
            'reportable_id' => Trail::factory()->published(),
            'reason' => fake()->randomElement(ReportReason::cases()),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
