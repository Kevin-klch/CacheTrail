<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;

/**
 * Numbers and dates in the format of the current language: "1.234,5" and "18. November 2026" in German,
 * "1,234.5" and "November 18, 2026" in English.
 */
final class Format
{
    public static function number(float|int $value, int $decimals = 0, bool $grouping = true): string
    {
        $german = app()->getLocale() === 'de';

        return number_format($value, $decimals, $german ? ',' : '.', $grouping ? ($german ? '.' : ',') : '');
    }

    /**
     * Difficulty or terrain without needless decimals, e.g. "2" or "1,5".
     */
    public static function rating(float|int $value): string
    {
        return self::number($value, fmod((float) $value, 1.0) === 0.0 ? 0 : 1, grouping: false);
    }

    public static function date(DateTimeInterface|string $date): string
    {
        return Carbon::parse($date)->translatedFormat(app()->getLocale() === 'de' ? 'j. F Y' : 'F j, Y');
    }

    public static function month(DateTimeInterface|string $date): string
    {
        return Carbon::parse($date)->translatedFormat('F Y');
    }

    public static function decimalSeparator(): string
    {
        return app()->getLocale() === 'de' ? ',' : '.';
    }
}
