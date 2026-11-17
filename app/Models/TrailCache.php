<?php

namespace App\Models;

use App\Data\GeocacheData;
use App\Enums\CacheStatus;
use App\Enums\CacheType;
use Database\Factories\TrailCacheFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A geocache as a station of a trail, at a user-defined position.
 */
#[Fillable(['position', 'provider', 'cache_code', 'cache_name', 'latitude', 'longitude', 'cache_type', 'difficulty', 'terrain', 'cache_status', 'last_checked_at'])]
class TrailCache extends Model
{
    /** @use HasFactory<TrailCacheFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'latitude' => 'float',
            'longitude' => 'float',
            'cache_type' => CacheType::class,
            'difficulty' => 'float',
            'terrain' => 'float',
            'cache_status' => CacheStatus::class,
            'last_checked_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Trail, $this>
     */
    public function trail(): BelongsTo
    {
        return $this->belongsTo(Trail::class);
    }

    /**
     * @return array<string, mixed>
     */
    public static function attributesFrom(GeocacheData $geocache, string $provider): array
    {
        return [
            'provider' => $provider,
            'cache_code' => $geocache->code,
            'cache_name' => $geocache->name,
            'latitude' => $geocache->latitude,
            'longitude' => $geocache->longitude,
            'cache_type' => $geocache->type,
            'difficulty' => $geocache->difficulty,
            'terrain' => $geocache->terrain,
            'cache_status' => $geocache->status,
            'last_checked_at' => now(),
        ];
    }

    /**
     * Difficulty and terrain in the usual short form, e.g. "D2 / T1,5".
     */
    public function ratingLabel(): string
    {
        $format = fn (float $value): string => str_replace('.', ',', (string) $value);

        return 'D'.$format($this->difficulty).' / T'.$format($this->terrain);
    }
}
