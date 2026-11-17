<?php

namespace App\Enums;

/**
 * How the total distance of a trail was determined (specification 4.4, step 4).
 */
enum DistanceMethod: string
{
    /** Sum of the direct distances between consecutive stations. */
    case StraightLine = 'straight_line';

    /** Calculated along real paths by a routing service (not available yet). */
    case Routed = 'routed';

    public function label(): string
    {
        return match ($this) {
            self::StraightLine => __('straight line'),
            self::Routed => __('along paths'),
        };
    }
}
