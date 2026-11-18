<?php

namespace App\Enums;

/**
 * What a feedback message is about.
 */
enum FeedbackCategory: string
{
    case Bug = 'bug';
    case Idea = 'idea';
    case Praise = 'praise';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Bug => __('Something does not work'),
            self::Idea => __('Idea or wish'),
            self::Praise => __('Praise'),
            self::Other => __('Other'),
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Bug => __('Bug'),
            self::Idea => __('Idea'),
            self::Praise => __('Praise'),
            self::Other => __('Other'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Bug => 'alert',
            self::Idea => 'star',
            self::Praise => 'heart',
            self::Other => 'mail',
        };
    }
}
