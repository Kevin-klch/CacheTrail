<?php

namespace App\Models;

use Database\Factories\GeocachingAccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A user's linked geocaching account (specification 9.2). Tokens are stored encrypted.
 * Until the official API is available, accounts only exist as demo data for local development.
 */
#[Fillable(['provider', 'external_user_id', 'username', 'membership_status', 'finds_count', 'member_since', 'last_verified_at'])]
#[Hidden(['access_token', 'refresh_token'])]
class GeocachingAccount extends Model
{
    /** @use HasFactory<GeocachingAccountFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'token_expires_at' => 'datetime',
            'finds_count' => 'integer',
            'member_since' => 'date',
            'last_verified_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
