<?php

namespace App\Enums;

enum TrailVisibility: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Published => __('Published'),
            self::Hidden => __('Hidden'),
        };
    }
}
