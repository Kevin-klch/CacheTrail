<?php

namespace App\Models;

use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use Database\Factories\TrailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'description', 'estimated_duration_minutes', 'transportations'])]
class Trail extends Model
{
    /** @use HasFactory<TrailFactory> */
    use HasFactory;

    /**
     * Slugs that would collide with fixed URLs below /routen.
     */
    private const array RESERVED_SLUGS = ['erstellen'];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'visibility' => TrailVisibility::Draft,
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
            'visibility' => TrailVisibility::class,
            'published_at' => 'datetime',
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

    public static function minCachesToPublish(): int
    {
        return (int) config('cachetrail.trails.min_caches_to_publish');
    }

    public function hasEnoughCachesToPublish(): bool
    {
        return $this->caches()->count() >= static::minCachesToPublish();
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
        });
    }

    /**
     * @param  Builder<Trail>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('visibility', TrailVisibility::Published);
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

        if ($visibility === TrailVisibility::Published && $this->published_at === null) {
            $this->published_at = now();
        }
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
