<?php

namespace App\Models;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use Database\Factories\ReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A report of a route or rating by a user, handled by administrators (specification 9.7).
 */
#[Fillable(['reason', 'description'])]
class Report extends Model
{
    /** @use HasFactory<ReportFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ReportStatus::Open,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reason' => ReportReason::class,
            'status' => ReportStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * The reporting user.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The reported route or rating; null if it has been deleted in the meantime.
     *
     * @return MorphTo<Model, $this>
     */
    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * @param  Builder<Report>  $query
     */
    public function scopeOpen(Builder $query): void
    {
        $query->where('status', ReportStatus::Open);
    }

    public function isOpen(): bool
    {
        return $this->status === ReportStatus::Open;
    }

    public function close(ReportStatus $status, User $admin): void
    {
        $this->forceFill([
            'status' => $status,
            'resolved_by' => $admin->id,
            'resolved_at' => now(),
        ])->save();
    }
}
