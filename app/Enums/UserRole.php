<?php

namespace App\Enums;

/**
 * Main role of an account; every user has exactly one. It decides the rights:
 *
 * - Owner: runs the site; same rights as admins. Only owners can grant or take away the owner role.
 * - Admin: everything, including user and role management.
 * - Moderator: handles reports and may delete routes and ratings of others.
 * - User: normal account.
 *
 * Additional badges such as "Supporter" or "Developer" are secondary roles (see SecondaryRole).
 */
enum UserRole: string
{
    case User = 'user';
    case Moderator = 'moderator';
    case Admin = 'admin';
    case Owner = 'owner';

    public function label(): string
    {
        return match ($this) {
            self::User => __('User'),
            self::Moderator => __('Moderator'),
            self::Admin => __('Admin'),
            self::Owner => __('Owner'),
        };
    }

    /**
     * Full administration rights: user and role management, moderation and editing all routes.
     */
    public function isAdministrative(): bool
    {
        return $this === self::Owner || $this === self::Admin;
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
            self::Admin => 'bg-trail-500 text-white',
            self::Moderator => 'bg-forest-500 text-white ring-1 ring-forest-300/50',
            self::User => 'bg-sand-200 text-ink',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Owner => 'crown',
            self::Admin => 'key',
            self::Moderator => 'shield',
            self::User => 'user',
        };
    }
}
