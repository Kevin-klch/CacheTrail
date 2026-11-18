<?php

namespace App\Models;

use App\Enums\FeedbackCategory;
use Database\Factories\FeedbackFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A message sent with the feedback button, read by the moderators and administrators.
 */
#[Fillable(['category', 'message', 'contact', 'page_url'])]
class Feedback extends Model
{
    /** @use HasFactory<FeedbackFactory> */
    use HasFactory;

    protected $table = 'feedback';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => FeedbackCategory::class,
            'done_at' => 'datetime',
        ];
    }

    /**
     * The sender; null for guests and deleted accounts.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  Builder<Feedback>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->whereNull('done_at');
    }

    public function isDone(): bool
    {
        return $this->done_at !== null;
    }
}
