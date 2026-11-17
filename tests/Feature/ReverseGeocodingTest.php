<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Geocoding\ReverseGeocoder;
use App\Support\UserLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReverseGeocodingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_place_name_is_looked_up_with_rounded_coordinates_and_cached(): void
    {
        Http::fake(['*' => Http::response(['address' => ['town' => 'Moers', 'state' => 'Nordrhein-Westfalen']])]);

        $geocoder = $this->app->make(ReverseGeocoder::class);

        $this->assertSame('Moers', $geocoder->placeName(51.45, 6.63));
        $this->assertSame('Moers', $geocoder->placeName(51.45, 6.63));

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request): bool => str_contains($request->url(), 'lat=51.45')
            && str_contains($request->url(), 'lon=6.63')
            && str_contains($request->url(), 'zoom=10'));
    }

    public function test_failures_return_null_and_are_not_cached(): void
    {
        Http::fake(['*' => Http::sequence()->push('', 500)->push(['address' => ['city' => 'Duisburg']])]);

        $geocoder = $this->app->make(ReverseGeocoder::class);

        $this->assertNull($geocoder->placeName(51.43, 6.76));
        $this->assertSame('Duisburg', $geocoder->placeName(51.43, 6.76));
    }

    public function test_sharing_the_location_shows_the_place_name(): void
    {
        Http::fake(['*' => Http::response(['address' => ['town' => 'Moers']])]);

        $this->actingAs(User::factory()->create())
            ->post(route('location.store'), ['latitude' => 51.4512, 'longitude' => 6.6289]);

        $this->assertSame('Moers', UserLocation::fromSession(session()->driver())->placeName);

        $this->get(route('trails.index'))
            ->assertSee('Dein Standort:')
            ->assertSee('Moers');
    }

    public function test_coordinates_are_shown_when_no_place_name_is_known(): void
    {
        Http::fake(['*' => Http::response([], 500)]);

        $this->actingAs(User::factory()->create())
            ->post(route('location.store'), ['latitude' => 51.4512, 'longitude' => 6.6289]);

        $this->get(route('trails.index'))->assertSee('51,45° N, 6,63° O');
    }
}
