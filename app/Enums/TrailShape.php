<?php

namespace App\Enums;

/**
 * Whether a trail ends where it started.
 */
enum TrailShape: string
{
    case Loop = 'loop';
    case OneWay = 'one_way';

    public function label(): string
    {
        return match ($this) {
            self::Loop => __('Round trip'),
            self::OneWay => __('Start and finish differ'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Loop => 'loop',
            self::OneWay => 'route',
        };
    }
}
