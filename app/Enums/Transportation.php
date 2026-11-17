<?php

namespace App\Enums;

enum Transportation: string
{
    case Foot = 'foot';
    case Bike = 'bike';
    case FootAndBike = 'foot_and_bike';

    public function label(): string
    {
        return match ($this) {
            self::Foot => __('On foot'),
            self::Bike => __('By bike'),
            self::FootAndBike => __('On foot and by bike'),
        };
    }

    public function allowsWalking(): bool
    {
        return $this !== self::Bike;
    }

    public function allowsCycling(): bool
    {
        return $this !== self::Foot;
    }
}
