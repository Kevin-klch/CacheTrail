<?php

namespace App\Enums;

/**
 * Who or when a trail is suitable for. A trail can have several; new ones only need a new case here.
 */
enum TrailAttribute: string
{
    case KidFriendly = 'kid_friendly';
    case StrollerFriendly = 'stroller_friendly';
    case DogFriendly = 'dog_friendly';
    case NightFriendly = 'night_friendly';

    public function label(): string
    {
        return match ($this) {
            self::KidFriendly => __('Kid-friendly'),
            self::StrollerFriendly => __('Stroller-friendly'),
            self::DogFriendly => __('Dog-friendly'),
            self::NightFriendly => __('Possible at night'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::KidFriendly => 'kids',
            self::StrollerFriendly => 'stroller',
            self::DogFriendly => 'dog',
            self::NightFriendly => 'moon',
        };
    }
}
