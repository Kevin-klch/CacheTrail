<?php

namespace App\Models;

use Database\Factories\LoginRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * One successful login, shown to administrators and deleted after KEEP_DAYS days.
 */
#[Fillable(['ip_address', 'user_agent'])]
class LoginRecord extends Model
{
    /** @use HasFactory<LoginRecordFactory> */
    use HasFactory, MassPrunable;

    public const int KEEP_DAYS = 30;

    /** Logins are never changed, so there is only a created_at column. */
    public const UPDATED_AT = null;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Builder<LoginRecord>
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subDays(self::KEEP_DAYS));
    }

    /**
     * Rough device and browser from the user agent, e.g. "iPhone · Safari".
     */
    public function deviceLabel(): string
    {
        $agent = (string) $this->user_agent;

        $device = match (true) {
            Str::contains($agent, 'iPhone') => 'iPhone',
            Str::contains($agent, 'iPad') => 'iPad',
            Str::contains($agent, 'Android') => 'Android',
            Str::contains($agent, 'Windows') => 'Windows',
            Str::contains($agent, 'Macintosh') => 'Mac',
            Str::contains($agent, 'Linux') => 'Linux',
            default => __('Unknown device'),
        };

        $browser = match (true) {
            Str::contains($agent, 'Edg/') => 'Edge',
            Str::contains($agent, ['Firefox/', 'FxiOS/']) => 'Firefox',
            Str::contains($agent, ['Chrome/', 'CriOS/']) => 'Chrome',
            Str::contains($agent, 'Safari/') => 'Safari',
            default => null,
        };

        return $browser === null ? $device : $device.' · '.$browser;
    }
}
