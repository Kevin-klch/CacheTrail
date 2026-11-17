<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('Open'),
            self::Resolved => __('Resolved'),
            self::Dismissed => __('Dismissed'),
        };
    }
}
