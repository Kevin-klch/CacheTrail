<?php

namespace App\Models;

use App\Enums\Badge;
use Database\Factories\UserBadgeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One level of a badge a user has earned. "seen_at" stays empty until the owner has seen it on
 * their profile, so new badges can be marked as "Neu".
 */
#[Fillable(['badge', 'level', 'earned_at'])]
class UserBadge extends Model
{
    /** @use HasFactory<UserBadgeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'badge' => Badge::class,
            'level' => 'integer',
            'earned_at' => 'datetime',
            'seen_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function name(): string
    {
        return $this->badge->name();
    }

    public function description(): string
    {
        return $this->badge->description($this->level);
    }

    public function isNew(): bool
    {
        return $this->seen_at === null;
    }
}
