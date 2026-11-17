<?php

namespace App\Actions\Trails;

final readonly class AddCachesResult
{
    /**
     * @param  list<string>  $added  codes that were added
     * @param  array<string, string>  $failures  error message per code
     */
    public function __construct(
        public array $added,
        public array $failures,
    ) {}
}
