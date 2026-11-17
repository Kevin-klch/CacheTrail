<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => UserRole::User,
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
        ];
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
    public function favoriteTrails(): BelongsToMany
    {
        return $this->belongsToMany(Trail::class, 'trail_favorites')->withTimestamps();
    }

    /**
     * Public profile address, e.g. /nutzer/42-erika-muster. The number identifies the user,
     * the name part only makes the address readable (names are not unique).
     */
    public function profileUrl(): string
    {
        return route('profiles.show', ['user' => $this->profileSlug()]);
    }

    public function profileSlug(): string
    {
        return trim($this->id.'-'.Str::slug($this->name, language: 'de'), '-');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}
