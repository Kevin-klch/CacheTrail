<?php

namespace Tests\Feature;

use App\Actions\Trails\AddCachesResult;
use App\Actions\Trails\AddCachesToTrail;
use App\Enums\CacheStatus;
use App\Models\Trail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddCachesToTrailTest extends TestCase
{
    use RefreshDatabase;

    private function addCaches(Trail $trail, string $input): AddCachesResult
    {
        return $this->app->make(AddCachesToTrail::class)->handle($trail, $input);
    }

    public function test_caches_are_added_in_the_entered_order_with_their_details(): void
    {
        $trail = Trail::factory()->create();

        $result = $this->addCaches($trail, "gcct03\nGCCT01, GCCT14");

        $this->assertSame(['GCCT03', 'GCCT01', 'GCCT14'], $result->added);
        $this->assertSame([], $result->failures);

        $caches = $trail->caches()->get();
        $this->assertSame(['GCCT03', 'GCCT01', 'GCCT14'], $caches->pluck('cache_code')->all());
        $this->assertSame([0, 1, 2], $caches->pluck('position')->all());
        $this->assertSame('Am kleinen Bach', $caches[0]->cache_name);
        $this->assertSame('mock', $caches[0]->provider);
        $this->assertSame(CacheStatus::Archived, $caches[2]->cache_status);
    }

    public function test_new_caches_are_appended_after_existing_ones(): void
    {
        $trail = Trail::factory()->create();
        $this->addCaches($trail, 'GCCT01 GCCT02');

        $this->addCaches($trail, 'GCCT03');

        $this->assertSame(['GCCT01', 'GCCT02', 'GCCT03'], $trail->caches()->pluck('cache_code')->all());
        $this->assertSame(2, $trail->caches()->where('cache_code', 'GCCT03')->value('position'));
    }

    public function test_each_failing_code_gets_an_understandable_message(): void
    {
        $trail = Trail::factory()->create();
        $this->addCaches($trail, 'GCCT01');

        $result = $this->addCaches($trail, 'GCCT01 12345 GC99999 GCCT02');

        $this->assertSame(['GCCT02'], $result->added);
        $this->assertSame([
            'GCCT01' => 'Dieser Cache ist schon Teil der Route.',
            '12345' => 'Das ist kein gültiger GC-Code.',
            'GC99999' => 'Zu diesem Code wurde kein Cache gefunden.',
        ], $result->failures);
    }

    public function test_a_trail_cannot_exceed_the_maximum_number_of_caches(): void
    {
        $trail = Trail::factory()->withCaches(AddCachesToTrail::MAX_CACHES_PER_TRAIL - 1)->create();

        $result = $this->addCaches($trail, 'GCCT01 GCCT02');

        $this->assertSame(['GCCT01'], $result->added);
        $this->assertArrayHasKey('GCCT02', $result->failures);
        $this->assertSame(AddCachesToTrail::MAX_CACHES_PER_TRAIL, $trail->caches()->count());
    }
}
