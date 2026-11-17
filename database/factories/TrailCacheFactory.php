<?php

namespace Database\Factories;

use App\Enums\CacheStatus;
use App\Enums\CacheType;
use App\Models\Trail;
use App\Models\TrailCache;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrailCache>
 */
class TrailCacheFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trail_id' => Trail::factory(),
            'position' => 0,
            'provider' => 'mock',
            'cache_code' => 'GC'.fake()->unique()->regexify('[0-9A-HJKMNPQRTV-Z]{5}'),
            'cache_name' => fake()->words(2, true),
            'latitude' => fake()->latitude(51.2, 51.6),
            'longitude' => fake()->longitude(6.4, 6.9),
            'cache_type' => CacheType::Traditional,
            'difficulty' => fake()->randomElement([1.0, 1.5, 2.0, 2.5, 3.0]),
            'terrain' => fake()->randomElement([1.0, 1.5, 2.0, 2.5, 3.0]),
            'cache_status' => CacheStatus::Active,
            'last_checked_at' => now(),
        ];
    }

    public function status(CacheStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['cache_status' => $status]);
    }
}
