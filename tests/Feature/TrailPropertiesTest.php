<?php

namespace Tests\Feature;

use App\Enums\TrailAttribute;
use App\Enums\TrailShape;
use App\Models\Trail;
use App\Models\User;
use App\Support\Coordinates;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailPropertiesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function trailData(array $overrides = []): array
    {
        return [
            'name' => 'Runde am See',
            'description' => 'Eine schöne Runde.',
            'duration_hours' => 1,
            'duration_minutes' => 30,
            'transportations' => ['foot'],
            ...$overrides,
        ];
    }

    public function test_shape_suitability_and_parking_can_be_saved(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('trails.store'), $this->trailData([
                'shape' => 'loop',
                'suitability' => ['dog_friendly', 'kid_friendly'],
                'parking' => 'N 51° 28.338 E 006° 31.212',
            ]))
            ->assertSessionHasNoErrors();

        $trail = Trail::sole();
        $this->assertSame(TrailShape::Loop, $trail->shape);
        $this->assertEquals([TrailAttribute::KidFriendly, TrailAttribute::DogFriendly], $trail->suitability->all());
        $this->assertEqualsWithDelta(51.4723, $trail->parking_latitude, 0.0001);
        $this->assertEqualsWithDelta(6.5202, $trail->parking_longitude, 0.0001);
        $this->assertSame('N 51° 28.338 E 006° 31.212', $trail->parkingLabel());
    }

    public function test_properties_are_optional_and_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('trails.store'), $this->trailData())->assertSessionHasNoErrors();
        $this->assertNull(Trail::sole()->shape);
        $this->assertFalse(Trail::sole()->hasParking());

        $this->post(route('trails.store'), $this->trailData([
            'shape' => 'zickzack',
            'suitability' => ['cats'],
            'parking' => 'beim Bäcker',
        ]))->assertSessionHasErrors(['shape', 'suitability.0', 'parking']);
    }

    public function test_coordinates_are_read_in_both_common_formats(): void
    {
        $this->assertSame(['latitude' => 51.4723, 'longitude' => 6.5202], Coordinates::parse('N 51° 28.338 E 006° 31.212'));
        $this->assertSame(['latitude' => 51.4723, 'longitude' => 6.5202], Coordinates::parse('N51 28.338 E6 31.212'));
        $this->assertSame(['latitude' => 51.4723, 'longitude' => 6.5202], Coordinates::parse('51.4723, 6.5202'));
        $this->assertSame(['latitude' => -33.5, 'longitude' => -70.25], Coordinates::parse('S 33° 30.000 W 070° 15.000'));
        $this->assertNull(Coordinates::parse('N 95° 00.000 E 006° 00.000'));
        $this->assertNull(Coordinates::parse('irgendwo'));
    }

    public function test_the_detail_page_shows_the_properties_and_the_parking_spot(): void
    {
        $trail = Trail::factory()->published()->withCaches(2)->create([
            'shape' => TrailShape::Loop,
            'suitability' => [TrailAttribute::StrollerFriendly],
            'parking_latitude' => 51.4723,
            'parking_longitude' => 6.5202,
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.show', $trail))
            ->assertSeeInOrder(['Eigenschaften', 'Rundweg', 'Kinderwagentauglich', 'Parkplatz', 'N 51° 28.338 E 006° 31.212'])
            ->assertSee('destination=51.4723,6.5202', false);
    }

    public function test_routes_can_be_filtered_by_their_properties(): void
    {
        Trail::factory()->published()->create(['name' => 'Hunderunde', 'shape' => TrailShape::Loop, 'suitability' => [TrailAttribute::DogFriendly], 'parking_latitude' => 51.0, 'parking_longitude' => 7.0]);
        Trail::factory()->published()->create(['name' => 'Streckentour', 'shape' => TrailShape::OneWay, 'suitability' => [TrailAttribute::DogFriendly, TrailAttribute::KidFriendly]]);
        Trail::factory()->published()->create(['name' => 'Ohne Angaben']);

        $this->actingAs(User::factory()->create());

        $this->get(route('trails.index', ['shape' => 'loop']))->assertSee('Hunderunde')->assertDontSee('Streckentour')->assertDontSee('Ohne Angaben');
        $this->get(route('trails.index', ['suitability' => ['dog_friendly', 'kid_friendly']]))->assertSee('Streckentour')->assertDontSee('Hunderunde');
        $this->get(route('trails.index', ['parking' => 1]))->assertSee('Hunderunde')->assertDontSee('Streckentour');
    }
}
