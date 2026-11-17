<?php

namespace App\Enums;

/**
 * Geocache status (specification 5.1); must be mapped to the official API values later.
 */
enum CacheStatus: string
{
    case Active = 'active';
    case TemporarilyDisabled = 'temporarily_disabled';
    case Archived = 'archived';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::TemporarilyDisabled => __('Temporarily disabled'),
            self::Archived => __('Archived'),
            self::Unknown => __('Unknown'),
        };
    }

    /**
     * Hint shown to geocachers for stations that cannot be found right now.
     */
    public function hint(): ?string
    {
        return match ($this) {
            self::Active => null,
            self::TemporarilyDisabled => __('This cache is temporarily unavailable.'),
            self::Archived => __('This station is no longer available.'),
            self::Unknown => __('The current status of this cache is unknown.'),
        };
    }
}
