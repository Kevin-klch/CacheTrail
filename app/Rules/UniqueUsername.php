<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * A valid, free username: letters, digits, spaces and . _ - ' (nothing that breaks the profile address),
 * not taken yet regardless of upper and lower case ("TestCacher" = "testcacher").
 */
class UniqueUsername implements ValidationRule
{
    private const string PATTERN = "/^[\\pL\\pN](?:[\\pL\\pN ._'-]*[\\pL\\pN._])?$/u";

    public function __construct(private ?User $ignore = null) {}

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match(self::PATTERN, (string) $value) !== 1) {
            $fail(__('The username may only contain letters, digits, spaces and the characters . _ - \' and must start with a letter or digit.'));

            return;
        }

        $existing = User::findByName((string) $value);

        if ($existing !== null && ! $existing->is($this->ignore)) {
            $fail(__('This username is already taken.'));
        }
    }
}
