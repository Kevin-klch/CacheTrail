<?php

namespace App\Enums;

/**
 * Roles of CacheTrail accounts.
 *
 * - Owner: runs the site; same rights as admins. Only owners can grant or take away the owner role.
 * - Developer: builds the site; same rights as admins.
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
    case Developer = 'developer';
    case Admin = 'admin';
    case Owner = 'owner';

    public function label(): string
    {
        return match ($this) {
            self::User => __('User'),
            self::Supporter => __('Supporter'),
            self::Moderator => __('Moderator'),
            self::Developer => __('Developer'),
            self::Admin => __('Admin'),
            self::Owner => __('Owner'),
        };
    }

    /**
     * Full administration rights: user and role management, moderation and editing all routes.
     */
    public function isAdministrative(): bool
    {
        return in_array($this, [self::Owner, self::Admin, self::Developer], true);
    }

    public function canModerate(): bool
    {
        return $this->isAdministrative() || $this === self::Moderator;
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
            self::Owner => 'bg-forest-950 text-[#f1d98f] ring-1 ring-[#c9971c]',
            self::Developer => 'bg-[#3d5a80] text-white',
            self::Admin => 'bg-trail-500 text-white',
            self::Moderator => 'bg-forest-700 text-paper',
            self::Supporter => 'bg-[#c9971c] text-white',
            self::User => 'bg-sand-200 text-ink',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Owner => 'crown',
            self::Developer => 'code',
            self::Admin => 'key',
            self::Moderator => 'shield',
            self::Supporter => 'heart',
            self::User => 'user',
        };
    }
}
