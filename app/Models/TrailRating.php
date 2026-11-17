<?php

namespace App\Models;

use Database\Factories\TrailRatingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A star rating with an optional comment; the creator of the route may reply once.
 */
#[Fillable(['rating', 'comment'])]
class TrailRating extends Model
{
    /** @use HasFactory<TrailRatingFactory> */
    use HasFactory;

    public const int MIN_STARS = 1;

    public const int MAX_STARS = 5;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'creator_replied_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Trail, $this>
     */
    public function trail(): BelongsTo
    {
        return $this->belongsTo(Trail::class);
    }

    public function reply(?string $reply): void
    {
        $reply = $reply === null ? null : trim($reply);

        $this->forceFill([
            'creator_reply' => $reply === '' ? null : $reply,
            'creator_replied_at' => $reply === null || $reply === '' ? null : now(),
        ])->save();
    }
}
