<?php

namespace App\Enums;

/**
 * Roles of CacheTrail accounts.
 *
 * - Admin: everything, including user and role management.
 * - Moderator: handles reports and may delete routes and ratings of others.
 * - Supporter: no extra rights, only the badge (e.g. for people supporting the project).
 * - User: normal account.
 */
enum UserRole: string
{
    case User = 'user';
    case Supporter = 'supporter';
    case Moderator = 'moderator';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => __('User'),
            self::Supporter => __('Supporter'),
            self::Moderator => __('Moderator'),
            self::Admin => __('Admin'),
        };
    }

    public function canModerate(): bool
    {
        return $this === self::Admin || $this === self::Moderator;
    }

    /**
     * Whether a badge is shown after the name (normal users get none).
     */
    public function hasBadge(): bool
    {
        return $this !== self::User;
    }

    /**
     * Tailwind classes of the badge after the name.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Admin => 'bg-trail-500 text-white',
            self::Moderator => 'bg-forest-700 text-paper',
            self::Supporter => 'bg-[#c9971c] text-white',
            self::User => 'bg-sand-200 text-ink',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Admin => 'key',
            self::Moderator => 'shield',
            self::Supporter => 'heart',
            self::User => 'user',
        };
    }
}
