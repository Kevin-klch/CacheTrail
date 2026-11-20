<?php

namespace App\Models;

use App\Enums\DistanceMethod;
use App\Enums\TrailAttribute;
use App\Enums\TrailShape;
use App\Enums\TrailTool;
use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use App\Support\Coordinates;
use App\Support\Format;
use App\Support\GeoDistance;
use App\Support\UserLocation;
use Database\Factories\TrailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'description', 'estimated_duration_minutes', 'transportations', 'tools', 'other_tools', 'shape', 'suitability', 'parking_latitude', 'parking_longitude'])]
class Trail extends Model
{
    /** @use HasFactory<TrailFactory> */
    use HasFactory;

    /**
     * Slugs that would collide with fixed URLs below /routen.
     */
    private const array RESERVED_SLUGS = ['erstellen', 'standort'];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'visibility' => TrailVisibility::Draft,
        'tools' => '[]',
        'other_tools' => null,
        'shape' => null,
        'suitability' => '[]',
        'parking_latitude' => null,
        'parking_longitude' => null,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estimated_duration_minutes' => 'integer',
            'transportations' => AsEnumCollection::of(Transportation::class),
            'tools' => AsEnumCollection::of(TrailTool::class),
            'shape' => TrailShape::class,
            'suitability' => AsEnumCollection::of(TrailAttribute::class),
            'parking_latitude' => 'float',
            'parking_longitude' => 'float',
            'visibility' => TrailVisibility::class,
            'published_at' => 'datetime',
            'hidden_by_moderation_at' => 'datetime',
            'cache_count' => 'integer',
            'average_difficulty' => 'float',
            'average_terrain' => 'float',
            'total_distance_meters' => 'integer',
            'distance_method' => DistanceMethod::class,
            'start_latitude' => 'float',
            'start_longitude' => 'float',
            'like_count' => 'integer',
            'rating_count' => 'integer',
            'average_rating' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Trail $trail): void {
            $trail->slug ??= static::uniqueSlugFor($trail->name);
        });

        static::deleted(function (Trail $trail): void {
            if ($trail->image_path) {
                Storage::disk('public')->delete($trail->image_path);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The caches in the order defined by the creator.
     *
     * @return HasMany<TrailCache, $this>
     */
    public function caches(): HasMany
    {
        return $this->hasMany(TrailCache::class)->orderBy('position');
    }

    /**
     * @return HasMany<TrailRating, $this>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(TrailRating::class);
    }

    /**
     * Ratings shown publicly: those of blocked users are hidden.
     *
     * @return HasMany<TrailRating, $this>
     */
    public function visibleRatings(): HasMany
    {
        return $this->ratings()->whereHas('user', fn (Builder $user) => $user->notBlocked());
    }

    /**
     * Users who saved this trail on their private list "Für später merken".
     *
     * @return BelongsToMany<User, $this>
     */
    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trail_bookmarks')->withTimestamps();
    }

    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarkedBy()->whereKey($user->id)->exists();
    }

    /**
     * Users who have completed this trail.
     *
     * @return BelongsToMany<User, $this>
     */
    public function completedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trail_completions')->withPivot('completed_at')->withTimestamps();
    }

    public function isCompletedBy(User $user): bool
    {
        return $this->completedBy()->whereKey($user->id)->exists();
    }

    /**
     * Users who like this trail ("Gefällt mir"); the count determines popularity.
     *
     * @return BelongsToMany<User, $this>
     */
    public function likedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trail_likes')->withTimestamps();
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likedBy()->whereKey($user->id)->exists();
    }

    /**
     * Recalculates the like and rating figures used for sorting by popularity and rating.
     */
    public function refreshCommunityStatistics(): void
    {
        $average = $this->visibleRatings()->avg('rating');

        $this->forceFill([
            'like_count' => $this->likedBy()->count(),
            'rating_count' => $this->visibleRatings()->count(),
            'average_rating' => $average === null ? null : round((float) $average, 2),
        ])->saveQuietly();
    }

    /**
     * Average rating in the usual short form, e.g. "4,7"; null without ratings.
     */
    public function averageRatingLabel(): ?string
    {
        return $this->average_rating === null ? null : Format::number($this->average_rating, 1);
    }

    public static function minCachesToPublish(): int
    {
        return (int) config('cachetrail.trails.min_caches_to_publish');
    }

    public function hasEnoughCachesToPublish(): bool
    {
        return $this->caches()->count() >= static::minCachesToPublish();
    }

    /**
     * Recalculates the key figures from the caches in their current order (specification 4.4, step 4).
     * The distance is the sum of straight lines between the stations until a routing service is available.
     */
    public function refreshStatistics(): void
    {
        $caches = $this->caches()->get();

        $this->forceFill([
            'cache_count' => $caches->count(),
            'average_difficulty' => $caches->isEmpty() ? null : round($caches->avg('difficulty'), 2),
            'average_terrain' => $caches->isEmpty() ? null : round($caches->avg('terrain'), 2),
            'total_distance_meters' => $caches->isEmpty() ? null : (int) round(GeoDistance::alongPath(
                $caches->map(fn (TrailCache $cache): array => [$cache->latitude, $cache->longitude]),
            )),
            'distance_method' => $caches->isEmpty() ? null : DistanceMethod::StraightLine,
            'start_latitude' => $caches->first()?->latitude,
            'start_longitude' => $caches->first()?->longitude,
        ])->saveQuietly();
    }

    /**
     * Moves a cache to a new zero-based position; the other caches keep their relative order.
     */
    public function moveCache(TrailCache $cache, int $position): void
    {
        $caches = $this->caches()->get();
        $ordered = $caches->reject(fn (TrailCache $other): bool => $other->is($cache))->values();
        $ordered->splice(max(0, min($position, $ordered->count())), 0, [$caches->firstWhere('id', $cache->id)]);

        $this->savePositions($ordered);
    }

    public function removeCache(TrailCache $cache): void
    {
        DB::transaction(function () use ($cache): void {
            $cache->delete();
            $this->savePositions($this->caches()->get());
        });
    }

    /**
     * @param  Collection<int, TrailCache>  $caches
     */
    private function savePositions(Collection $caches): void
    {
        DB::transaction(function () use ($caches): void {
            $caches->values()->each(function (TrailCache $cache, int $position): void {
                if ($cache->position !== $position) {
                    $cache->update(['position' => $position]);
                }
            });

            $this->refreshStatistics();
        });
    }

    /**
     * @param  Builder<Trail>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('visibility', TrailVisibility::Published)
            ->whereHas('user', fn (Builder $user) => $user->notBlocked());
    }

    /**
     * @param  Builder<Trail>  $query
     */
    public function scopeSuitableFor(Builder $query, Transportation $transportation): void
    {
        $query->whereJsonContains('transportations', $transportation->value);
    }

    public function isPublished(): bool
    {
        return $this->visibility === TrailVisibility::Published;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Changes the visibility and keeps the first publication date.
     */
    public function changeVisibility(TrailVisibility $visibility): void
    {
        $this->visibility = $visibility;

        if ($visibility !== TrailVisibility::Hidden) {
            $this->hidden_by_moderation_at = null;
        }

        if ($visibility === TrailVisibility::Published && $this->published_at === null) {
            $this->published_at = now();
        }
    }

    /**
     * Hides the route after a report; only the moderation team can show it again.
     */
    public function hideByModeration(): void
    {
        $this->changeVisibility(TrailVisibility::Hidden);
        $this->hidden_by_moderation_at = now();
    }

    public function isHiddenByModeration(): bool
    {
        return $this->visibility === TrailVisibility::Hidden && $this->hidden_by_moderation_at !== null;
    }

    /**
     * Human readable duration, e.g. "45 Min.", "2 Std." or "2 Std. 30 Min.".
     */
    public function durationLabel(): string
    {
        $hours = intdiv($this->estimated_duration_minutes, 60);
        $minutes = $this->estimated_duration_minutes % 60;

        return collect([
            $hours > 0 ? __(':count h', ['count' => $hours]) : null,
            $minutes > 0 ? __(':count min', ['count' => $minutes]) : null,
        ])->filter()->implode(' ');
    }

    /**
     * The plain text description split at blank lines.
     *
     * @return list<string>
     */
    public function descriptionParagraphs(): array
    {
        return array_values(array_filter(
            preg_split('/\R\s*\R/', trim((string) $this->description)) ?: [],
            fn (string $paragraph): bool => trim($paragraph) !== '',
        ));
    }

    /**
     * Numbered markers for the route map, in the creator's order.
     *
     * @return list<array<string, mixed>>
     */
    public function mapMarkers(): array
    {
        return $this->caches->values()
            ->map(fn (TrailCache $cache, int $index): array => $cache->toMapMarker($index + 1))
            ->all();
    }

    /**
     * Distance from the given location to the start of the trail, e.g. "ca. 4 km entfernt"; null without caches.
     */
    public function distanceFromLabel(UserLocation $location): ?string
    {
        if ($this->start_latitude === null || $this->start_longitude === null) {
            return null;
        }

        $kilometers = $location->distanceTo($this->start_latitude, $this->start_longitude) / 1000;

        return $kilometers < 1
            ? __('less than 1 km away')
            : __('approx. :distance away', ['distance' => Format::number($kilometers, $kilometers < 10 ? 1 : 0).' km']);
    }

    /**
     * Start point and key figures for the map view of the route directory.
     *
     * @return array{name: string, url: string, latitude: float, longitude: float, summary: string, distance: ?string}
     */
    public function toDirectoryMarker(?UserLocation $location): array
    {
        return [
            'name' => $this->name,
            'url' => route('trails.show', $this),
            'latitude' => $this->start_latitude,
            'longitude' => $this->start_longitude,
            'summary' => collect([
                $this->durationLabel(),
                trans_choice(':count cache|:count caches', $this->cache_count),
                $this->distanceLabel(),
                $this->average_difficulty !== null
                    ? 'D/T '.Format::number($this->average_difficulty, 1).' / '.Format::number($this->average_terrain, 1)
                    : null,
            ])->filter()->implode(' · '),
            'distance' => $location ? $this->distanceFromLabel($location) : null,
        ];
    }

    /**
     * Approximate total distance, e.g. "ca. 850 m" or "ca. 3,2 km"; null without caches.
     */
    public function distanceLabel(): ?string
    {
        if ($this->total_distance_meters === null) {
            return null;
        }

        $distance = $this->total_distance_meters < 1000
            ? (round($this->total_distance_meters / 10) * 10).' m'
            : Format::number($this->total_distance_meters / 1000, 1).' km';

        return __('~ :distance', ['distance' => $distance]);
    }

    /**
     * Average rating on the 1 to 5 scale, e.g. "2,0 / 5"; null without caches.
     */
    public static function ratingAverageLabel(?float $average): ?string
    {
        return $average === null ? null : Format::number($average, 1).' / 5';
    }

    public function durationHours(): int
    {
        return intdiv((int) $this->estimated_duration_minutes, 60);
    }

    public function durationRemainingMinutes(): int
    {
        return (int) $this->estimated_duration_minutes % 60;
    }

    /**
     * Comma separated labels, e.g. "Zu Fuß, Fahrrad".
     */
    public function transportationLabel(): string
    {
        return $this->transportations->map(fn (Transportation $mode): string => $mode->label())->implode(', ');
    }

    /**
     * Labels of all equipment needed for the trail: the preset tools followed by the free text entry.
     *
     * @return list<string>
     */
    public function equipmentLabels(): array
    {
        $labels = ($this->tools ?? collect())->map(fn (TrailTool $tool): string => $tool->label())->values()->all();

        if (filled($this->other_tools)) {
            $labels[] = $this->other_tools;
        }

        return $labels;
    }

    public function hasParking(): bool
    {
        return $this->parking_latitude !== null && $this->parking_longitude !== null;
    }

    /**
     * The parking spot in geocaching notation, e.g. "N 51° 28.338 E 006° 31.212".
     */
    public function parkingLabel(): ?string
    {
        return $this->hasParking() ? Coordinates::format($this->parking_latitude, $this->parking_longitude) : null;
    }

    /**
     * The parking spot for the route map.
     *
     * @return array{latitude: float, longitude: float, label: string}|null
     */
    public function parkingMarker(): ?array
    {
        return $this->hasParking()
            ? ['latitude' => $this->parking_latitude, 'longitude' => $this->parking_longitude, 'label' => $this->parkingLabel()]
            : null;
    }

    /**
     * Shape and suitability as list of [icon, label] for the properties of a trail.
     *
     * @return list<array{0: string, 1: string}>
     */
    public function propertyBadges(): array
    {
        $badges = $this->shape ? [[$this->shape->icon(), $this->shape->label()]] : [];

        foreach (TrailAttribute::cases() as $attribute) {
            if (($this->suitability ?? collect())->contains($attribute)) {
                $badges[] = [$attribute->icon(), $attribute->label()];
            }
        }

        return $badges;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public static function uniqueSlugFor(string $name): string
    {
        $base = Str::slug($name, language: 'de') ?: 'route';
        $slug = $base;
        $suffix = 2;

        while (in_array($slug, self::RESERVED_SLUGS, true) || static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
