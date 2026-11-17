<?php

namespace Tests\Unit;

use App\Support\GcCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GcCodeTest extends TestCase
{
    public function test_codes_are_normalized_to_upper_case_without_whitespace(): void
    {
        $this->assertSame('GC12AB', GcCode::normalize(' gc12 ab '));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function codes(): array
    {
        return [
            'short code' => ['GC1', true],
            'six characters' => ['GCABCDEF', true],
            'lower case' => ['gc4hk2', true],
            'missing prefix' => ['12345', false],
            'only prefix' => ['GC', false],
            'too long' => ['GC1234567', false],
            'forbidden letter I' => ['GC12I4', false],
            'forbidden letter O' => ['GC12O4', false],
            'special characters' => ['GC12-4', false],
            'other prefix' => ['OC12345', false],
        ];
    }

    #[DataProvider('codes')]
    public function test_validation(string $code, bool $valid): void
    {
        $this->assertSame($valid, GcCode::isValid($code));
    }

    public function test_lists_are_split_on_spaces_commas_semicolons_and_new_lines_without_duplicates(): void
    {
        $this->assertSame(
            ['GC111', 'GC222', 'GC333', 'GC444'],
            GcCode::parseList("gc111, GC222;GC333\r\n\n  GC444 gc111"),
        );
    }
}
