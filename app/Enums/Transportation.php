<?php

namespace App\Enums;

/**
 * Means of transport a trail is suitable for. A trail can have several;
 * new means of transport only need a new case here.
 */
enum Transportation: string
{
    case Foot = 'foot';
    case Bike = 'bike';

    public function label(): string
    {
        return match ($this) {
            self::Foot => __('On foot'),
            self::Bike => __('By bike'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Foot => 'foot',
            self::Bike => 'bike',
        };
    }
}
