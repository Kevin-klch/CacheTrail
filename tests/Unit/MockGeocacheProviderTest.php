<?php

namespace Tests\Unit;

use App\Contracts\GeocacheProvider;
use App\Enums\CacheStatus;
use App\Services\Geocaching\MockGeocacheProvider;
use App\Support\GcCode;
use Tests\TestCase;

class MockGeocacheProviderTest extends TestCase
{
    private MockGeocacheProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new MockGeocacheProvider(database_path('data/mock-geocaches.json'));
    }

    public function test_the_mock_is_the_configured_default_provider(): void
    {
        $this->assertInstanceOf(MockGeocacheProvider::class, $this->app->make(GeocacheProvider::class));
    }

    public function test_known_codes_are_found_case_insensitively(): void
    {
        $cache = $this->provider->findByCode('gcct01');

        $this->assertNotNull($cache);
        $this->assertSame('GCCT01', $cache->code);
        $this->assertSame('Der alte Baum', $cache->name);
    }

    public function test_unknown_codes_return_null(): void
    {
        $this->assertNull($this->provider->findByCode('GC99999'));
    }

    public function test_several_codes_can_be_looked_up_at_once(): void
    {
        $found = $this->provider->findManyByCodes(['GCCT01', 'GC99999', 'GCCT02']);

        $this->assertSame(['GCCT01', 'GCCT02'], array_keys($found));
    }

    public function test_the_data_set_is_valid_and_contains_every_status_needed_for_testing(): void
    {
        $caches = $this->provider->all();
        $statuses = array_map(fn ($cache) => $cache->status, $caches);

        foreach ($caches as $cache) {
            $this->assertTrue(GcCode::isValid($cache->code), "{$cache->code} is not a valid GC code.");
            $this->assertGreaterThanOrEqual(1, $cache->difficulty);
            $this->assertLessThanOrEqual(5, $cache->terrain);
        }

        $this->assertContains(CacheStatus::TemporarilyDisabled, $statuses);
        $this->assertContains(CacheStatus::Archived, $statuses);
    }

    public function test_mock_caches_have_no_external_page(): void
    {
        $this->assertNull($this->provider->cacheUrl('GCCT01'));
    }
}
