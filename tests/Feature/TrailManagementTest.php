<?php

namespace Tests\Feature;

use App\Enums\TrailVisibility;
use App\Enums\Transportation;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TrailManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return [
            'name' => 'Waldabenteuer am Niederrhein',
            'description' => "Erster Absatz.\n\nZweiter Absatz.",
            'duration_hours' => 2,
            'duration_minutes' => 30,
            'transportations' => [Transportation::Foot->value, Transportation::Bike->value],
            ...$overrides,
        ];
    }

    /**
     * A real 1x1 PNG, so the tests do not depend on the GD extension.
     */
    private function fakeImage(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        ));
    }

    public function test_create_page_can_be_rendered(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('trails.create'))
            ->assertOk()
            ->assertSee('Weiter zu den Caches');
    }

    public function test_creating_a_trail_saves_a_draft_and_continues_to_the_caches(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('trails.store'), $this->validData());

        $trail = Trail::sole();
        $response->assertRedirect(route('trails.caches', $trail));

        $this->assertTrue($trail->user->is($user));
        $this->assertSame('waldabenteuer-am-niederrhein', $trail->slug);
        $this->assertSame(150, $trail->estimated_duration_minutes);
        $this->assertEquals([Transportation::Foot, Transportation::Bike], $trail->transportations->all());
        $this->assertSame(TrailVisibility::Draft, $trail->visibility);
        $this->assertNull($trail->published_at);
    }

    public function test_creating_a_trail_never_publishes_it_directly(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('trails.store'), $this->validData(['visibility' => 'published']));

        $this->assertSame(TrailVisibility::Draft, Trail::sole()->visibility);
    }

    public function test_a_cover_image_can_be_uploaded(): void
    {
        $this->actingAs(User::factory()->create())->post(route('trails.store'), $this->validData([
            'image' => $this->fakeImage('wald.png'),
        ]));

        $trail = Trail::sole();

        $this->assertNotNull($trail->image_path);
        Storage::disk('public')->assertExists($trail->image_path);
    }

    public function test_non_image_files_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('trails.store'), $this->validData([
                'image' => UploadedFile::fake()->create('virus.php', 10, 'application/x-php'),
            ]))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('trails', 0);
    }

    public function test_validation_errors_are_shown(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('trails.store'), $this->validData([
                'name' => '',
                'transportations' => ['car'],
                'duration_hours' => 0,
                'duration_minutes' => 0,
            ]))
            ->assertSessionHasErrors([
                'name' => 'Name der Route ist ein Pflichtfeld.',
                'transportations.0',
                'duration_hours' => 'Die geschätzte Dauer muss mindestens 5 Minuten betragen.',
            ]);
    }

    public function test_at_least_one_means_of_transport_is_required(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('trails.store'), $this->validData(['transportations' => []]))
            ->assertSessionHasErrors('transportations');

        $this->assertDatabaseCount('trails', 0);
    }

    public function test_means_of_transport_are_stored_in_canonical_order(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('trails.store'), $this->validData(['transportations' => ['bike', 'foot']]));

        $this->assertEquals([Transportation::Foot, Transportation::Bike], Trail::sole()->transportations->all());
    }

    public function test_slugs_are_unique_and_avoid_reserved_words(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('trails.store'), $this->validData(['name' => 'Runde am See']));
        $this->actingAs($user)->post(route('trails.store'), $this->validData(['name' => 'Runde am See']));
        $this->actingAs($user)->post(route('trails.store'), $this->validData(['name' => 'Erstellen']));
        $this->actingAs($user)->post(route('trails.store'), $this->validData(['name' => 'Über die Höhen']));

        $this->assertSame(
            ['runde-am-see', 'runde-am-see-2', 'erstellen-2', 'ueber-die-hoehen'],
            Trail::orderBy('id')->pluck('slug')->all(),
        );
    }

    public function test_the_owner_can_update_a_trail(): void
    {
        $trail = Trail::factory()->withCaches(2)->create(['name' => 'Alter Name']);

        $this->actingAs($trail->user)
            ->put(route('trails.update', $trail), $this->validData([
                'name' => 'Neuer Name',
                'visibility' => TrailVisibility::Published->value,
            ]))
            ->assertRedirect(route('trails.show', $trail));

        $trail->refresh();

        $this->assertSame('Neuer Name', $trail->name);
        $this->assertSame('alter-name', $trail->slug, 'The slug stays stable so shared links keep working.');
        $this->assertSame(TrailVisibility::Published, $trail->visibility);
        $this->assertNotNull($trail->published_at);
    }

    public function test_hiding_and_republishing_keeps_the_first_publication_date(): void
    {
        $trail = Trail::factory()->published()->withCaches(2)->create(['published_at' => now()->subMonth()]);
        $firstPublishedAt = $trail->published_at;

        foreach ([TrailVisibility::Hidden, TrailVisibility::Published] as $visibility) {
            $this->actingAs($trail->user)->put(route('trails.update', $trail), $this->validData(['visibility' => $visibility->value]));
        }

        $this->assertTrue($firstPublishedAt->equalTo($trail->fresh()->published_at));
    }

    public function test_publishing_via_the_edit_form_requires_enough_caches(): void
    {
        $trail = Trail::factory()->withCaches(1)->create();

        $this->actingAs($trail->user)
            ->put(route('trails.update', $trail), $this->validData(['visibility' => TrailVisibility::Published->value]))
            ->assertSessionHasErrors(['visibility' => 'Eine Route braucht mindestens 2 Caches, um veröffentlicht zu werden.']);

        $this->assertSame(TrailVisibility::Draft, $trail->fresh()->visibility);
    }

    public function test_already_published_trails_without_caches_stay_published_when_edited(): void
    {
        $trail = Trail::factory()->published()->create(['name' => 'Alte Route']);

        $this->actingAs($trail->user)
            ->put(route('trails.update', $trail), $this->validData(['name' => 'Alte Route, neu', 'visibility' => 'published']))
            ->assertSessionHasNoErrors();

        $this->assertSame('Alte Route, neu', $trail->fresh()->name);
    }

    public function test_replacing_and_removing_the_image_deletes_old_files(): void
    {
        $trail = Trail::factory()->create();
        $this->actingAs($trail->user);

        $this->put(route('trails.update', $trail), $this->validData([
            'visibility' => 'draft',
            'image' => $this->fakeImage('eins.png'),
        ]));
        $firstImage = $trail->fresh()->image_path;

        $this->put(route('trails.update', $trail), $this->validData([
            'visibility' => 'draft',
            'image' => $this->fakeImage('zwei.png'),
        ]));
        $secondImage = $trail->fresh()->image_path;

        Storage::disk('public')->assertMissing($firstImage);
        Storage::disk('public')->assertExists($secondImage);

        $this->put(route('trails.update', $trail), $this->validData(['visibility' => 'draft', 'remove_image' => '1']));

        Storage::disk('public')->assertMissing($secondImage);
        $this->assertNull($trail->fresh()->image_path);
    }

    public function test_other_users_cannot_edit_update_or_delete_a_trail(): void
    {
        $trail = Trail::factory()->published()->create(['name' => 'Original']);
        $this->actingAs(User::factory()->create());

        $this->get(route('trails.edit', $trail))->assertForbidden();
        $this->put(route('trails.update', $trail), $this->validData(['name' => 'Gekapert', 'visibility' => 'published']))->assertForbidden();
        $this->delete(route('trails.destroy', $trail))->assertForbidden();

        $this->assertSame('Original', $trail->fresh()->name);
    }

    public function test_admins_can_update_and_delete_any_trail(): void
    {
        $trail = Trail::factory()->published()->create();
        $this->actingAs(User::factory()->admin()->create());

        $this->get(route('trails.edit', $trail))->assertOk();
        $this->delete(route('trails.destroy', $trail))->assertRedirect(route('trails.mine'));

        $this->assertModelMissing($trail);
    }

    public function test_the_owner_can_delete_a_trail_and_its_image(): void
    {
        $trail = Trail::factory()->create();
        $this->actingAs($trail->user)->put(route('trails.update', $trail), $this->validData([
            'visibility' => 'draft',
            'image' => $this->fakeImage('wald.png'),
        ]));
        $imagePath = $trail->fresh()->image_path;

        $this->delete(route('trails.destroy', $trail))->assertRedirect(route('trails.mine'));

        $this->assertModelMissing($trail);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_deleting_an_account_deletes_its_trails(): void
    {
        $trail = Trail::factory()->create();

        $this->actingAs($trail->user)->delete(route('profile.destroy'), ['password' => 'password']);

        $this->assertModelMissing($trail);
    }
}
