<?php

namespace App\Enums;

enum ReportReason: string
{
    case Inappropriate = 'inappropriate';
    case WrongInformation = 'wrong_information';
    case Spam = 'spam';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Inappropriate => __('Inappropriate content'),
            self::WrongInformation => __('Wrong information'),
            self::Spam => __('Spam or advertising'),
            self::Other => __('Other'),
        };
    }
}
