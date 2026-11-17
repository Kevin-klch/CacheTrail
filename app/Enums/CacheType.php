<?php

namespace App\Enums;

/**
 * Geocache types; values must be mapped to the official API values once a real provider exists.
 */
enum CacheType: string
{
    case Traditional = 'traditional';
    case Multi = 'multi';
    case Mystery = 'mystery';
    case Letterbox = 'letterbox';
    case Earthcache = 'earthcache';
    case Wherigo = 'wherigo';
    case Virtual = 'virtual';

    public function label(): string
    {
        return match ($this) {
            self::Traditional => __('Traditional'),
            self::Multi => __('Multi-cache'),
            self::Mystery => __('Mystery'),
            self::Letterbox => __('Letterbox'),
            self::Earthcache => __('EarthCache'),
            self::Wherigo => __('Wherigo'),
            self::Virtual => __('Virtual'),
        };
    }
}
