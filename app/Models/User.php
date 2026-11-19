<?php

namespace App\Models;

use App\Enums\SecondaryRole;
use App\Enums\UserRole;
use App\Rules\UniqueUsername;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasLocalePreference, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const int USERNAME_MIN_LENGTH = 2;

    public const int USERNAME_MAX_LENGTH = 50;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => UserRole::User,
        'blocked_at' => null,
        'bio' => null,
        'avatar_path' => null,
        'cover_path' => null,
        'secondary_roles' => '[]',
        'locale' => null,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'secondary_roles' => AsEnumCollection::of(SecondaryRole::class),
            'blocked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Delete the routes one by one instead of relying on the database cascade, so their
        // uploaded images are removed from storage as well.
        static::deleting(function (User $user): void {
            $user->trails()->each(fn (Trail $trail) => $trail->delete());
        });

        static::deleted(function (User $user): void {
            Storage::disk('public')->delete(array_filter([$user->avatar_path, $user->cover_path]));
        });
    }

    /**
     * Language for emails and the site, see SetLocale.
     */
    public function preferredLocale(): string
    {
        return $this->locale ?? config('app.locale');
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? Storage::disk('public')->url($this->avatar_path) : null;
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? Storage::disk('public')->url($this->cover_path) : null;
    }

    /**
     * First letter of the name, shown instead of a profile picture.
     */
    public function initial(): string
    {
        return mb_strtoupper(mb_substr($this->name, 0, 1));
    }

    public function isBlocked(): bool
    {
        return $this->blocked_at !== null;
    }

    /**
     * Blocks or unblocks the user. Their routes and ratings are hidden while blocked,
     * so the rating figures of the affected routes are recalculated.
     */
    public function setBlocked(bool $blocked): void
    {
        $this->forceFill(['blocked_at' => $blocked ? now() : null])->save();

        Trail::query()
            ->whereIn('id', $this->ratings()->select('trail_id'))
            ->each(fn (Trail $trail) => $trail->refreshCommunityStatistics());
    }

    /**
     * @param  Builder<User>  $query
     */
    public function scopeNotBlocked(Builder $query): void
    {
        $query->whereNull('blocked_at');
    }

    /**
     * @return HasMany<Trail, $this>
     */
    public function trails(): HasMany
    {
        return $this->hasMany(Trail::class);
    }

    /**
     * @return HasMany<TrailRating, $this>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(TrailRating::class);
    }

    /**
     * @return BelongsToMany<Trail, $this>
     */
    public function bookmarkedTrails(): BelongsToMany
    {
        return $this->belongsToMany(Trail::class, 'trail_bookmarks')->withTimestamps();
    }

    /**
     * @return BelongsToMany<Trail, $this>
     */
    public function likedTrails(): BelongsToMany
    {
        return $this->belongsToMany(Trail::class, 'trail_likes')->withTimestamps();
    }

    /**
     * Routes the user has completed, with the date in the pivot ("completed_at").
     *
     * @return BelongsToMany<Trail, $this>
     */
    public function completedTrails(): BelongsToMany
    {
        return $this->belongsToMany(Trail::class, 'trail_completions')->withPivot('completed_at')->withTimestamps();
    }

    /**
     * @return HasOne<GeocachingAccount, $this>
     */
    public function geocachingAccount(): HasOne
    {
        return $this->hasOne(GeocachingAccount::class);
    }

    /**
     * @return HasMany<UserBadge, $this>
     */
    public function badges(): HasMany
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Public profile address, e.g. /nutzer/TestCacher. The name is the geocaching username and unique.
     */
    public function profileUrl(): string
    {
        return route('profiles.show', ['user' => $this->name]);
    }

    /**
     * Finds a user by username, regardless of upper and lower case.
     */
    public static function findByName(string $name): ?self
    {
        return static::query()->whereRaw('lower(name) = ?', [mb_strtolower(trim($name))])->first();
    }

    /**
     * Validation rules for the username: the user's name on Geocaching.com.
     *
     * @return array<int, mixed>
     */
    public static function usernameRules(?self $ignore = null): array
    {
        return [
            'required',
            'string',
            'min:'.self::USERNAME_MIN_LENGTH,
            'max:'.self::USERNAME_MAX_LENGTH,
            new UniqueUsername($ignore),
        ];
    }

    /**
     * Owners, developers and admins have full administration rights.
     */
    public function isAdmin(): bool
    {
        return $this->role->isAdministrative();
    }

    public function isOwner(): bool
    {
        return $this->role === UserRole::Owner;
    }

    /**
     * Without email verification (no mail server yet) every account counts as confirmed.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! config('cachetrail.email_verification') || parent::hasVerifiedEmail();
    }

    /**
     * The shared account testers use on the feedback version. Its email address and password cannot be
     * changed and it cannot be deleted, so nobody can lock the others out.
     */
    public function isFeedbackAccount(): bool
    {
        // Without demo data there is no shared account; the address is then an ordinary one.
        return config('cachetrail.feedback.demo_data') && $this->email === config('cachetrail.feedback.email');
    }

    public function hasSecondaryRole(SecondaryRole $role): bool
    {
        return ($this->secondary_roles ?? collect())->contains($role);
    }

    /**
     * Badges after the name: the main role (unless it is a normal user) followed by the secondary roles,
     * each with label, icon and colour classes.
     *
     * @return list<UserRole|SecondaryRole>
     */
    public function roleBadges(): array
    {
        $secondary = SecondaryRole::cases();
        $own = array_values(array_filter($secondary, fn (SecondaryRole $role): bool => $this->hasSecondaryRole($role)));

        return $this->role->hasBadge() ? [$this->role, ...$own] : $own;
    }

    /**
     * Administrators and moderators handle reports and may remove content of others.
     */
    public function canModerate(): bool
    {
        return $this->role->canModerate();
    }
}
