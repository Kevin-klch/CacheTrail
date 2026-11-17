<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\Premium\ConfigPremiumAccessChecker;
use PHPUnit\Framework\TestCase;

class ConfigPremiumAccessCheckerTest extends TestCase
{
    public function test_grants_access_to_every_user_when_enabled(): void
    {
        $this->assertTrue((new ConfigPremiumAccessChecker(true))->hasPremiumAccess(new User));
    }

    public function test_denies_access_to_every_user_when_disabled(): void
    {
        $this->assertFalse((new ConfigPremiumAccessChecker(false))->hasPremiumAccess(new User));
    }
}
