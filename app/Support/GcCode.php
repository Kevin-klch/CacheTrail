<?php

namespace App\Support;

/**
 * Normalizing and validating geocache codes such as "GC12AB3".
 */
final class GcCode
{
    /**
     * "GC" followed by 1 to 6 characters of the Geocaching.com alphabet (no I, L, O, S, U).
     */
    private const string PATTERN = '/^GC[0-9A-HJKMNPQRTV-Z]{1,6}$/';

    public static function normalize(string $code): string
    {
        return strtoupper((string) preg_replace('/\s+/', '', $code));
    }

    public static function isValid(string $code): bool
    {
        return preg_match(self::PATTERN, self::normalize($code)) === 1;
    }

    /**
     * Splits user input separated by spaces, commas, semicolons or new lines into normalized, unique codes.
     *
     * @return list<string>
     */
    public static function parseList(string $input): array
    {
        $parts = preg_split('/[\s,;]+/', $input, flags: PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_unique(array_map(self::normalize(...), $parts)));
    }
}
