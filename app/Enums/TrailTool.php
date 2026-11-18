<?php

namespace App\Enums;

/**
 * Equipment that may be needed to find or log the caches of a trail. Creators can add
 * anything else as free text; new presets only need a new case here.
 */
enum TrailTool: string
{
    case Tweezers = 'tweezers';
    case Magnifier = 'magnifier';
    case Flashlight = 'flashlight';
    case UvLight = 'uv_light';
    case Magnet = 'magnet';
    case Mirror = 'mirror';
    case Gloves = 'gloves';
    case ClimbingGear = 'climbing_gear';

    public function label(): string
    {
        return match ($this) {
            self::Tweezers => __('Tweezers'),
            self::Magnifier => __('Magnifying glass'),
            self::Flashlight => __('Flashlight'),
            self::UvLight => __('UV light'),
            self::Magnet => __('Magnet'),
            self::Mirror => __('Mirror'),
            self::Gloves => __('Gloves'),
            self::ClimbingGear => __('Climbing gear'),
        };
    }
}
