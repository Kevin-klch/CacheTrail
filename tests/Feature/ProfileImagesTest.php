<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * A PNG file with the given size. Only the header is real, which is all that type and size
     * detection need (the GD extension is not required).
     */
    private function png(string $name, int $width, int $height): UploadedFile
    {
        $header = 'IHDR'.pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0);

        return UploadedFile::fake()->createWithContent($name, "\x89PNG\r\n\x1a\n"
            .pack('N', 13).$header.pack('N', crc32($header))
            .pack('N', 0).'IEND'.pack('N', crc32('IEND')));
    }

    public function test_a_profile_picture_and_cover_image_can_be_uploaded(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('profile.images.update'), [
                'avatar' => $this->png('ich.png', 300, 300),
                'cover' => $this->png('wald.png', 1600, 500),
            ])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'images-updated');

        $user->refresh();
        Storage::disk('public')->assertExists([$user->avatar_path, $user->cover_path]);

        $this->get($user->profileUrl())
            ->assertSee($user->avatarUrl())
            ->assertSee($user->coverUrl());
    }

    public function test_replacing_and_removing_images_deletes_the_old_files(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put(route('profile.images.update'), ['avatar' => $this->png('alt.png', 200, 200)]);
        $old = $user->fresh()->avatar_path;

        $this->put(route('profile.images.update'), ['avatar' => $this->png('neu.png', 200, 200)]);
        $new = $user->fresh()->avatar_path;
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($new);

        $this->put(route('profile.images.update'), ['remove_avatar' => '1']);
        $this->assertNull($user->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($new);
    }

    public function test_too_small_or_invalid_images_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('profile.images.update'), [
                'avatar' => $this->png('winzig.png', 40, 40),
                'cover' => UploadedFile::fake()->create('titel.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrorsIn('updateImages', ['avatar', 'cover']);
    }

    public function test_without_a_profile_picture_the_initial_is_shown(): void
    {
        $user = User::factory()->create(['name' => 'Waldfuchs']);

        $this->actingAs($user)->get($user->profileUrl())->assertSee('>W</span>', false);
    }

    public function test_deleting_the_account_removes_the_images(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->put(route('profile.images.update'), [
            'avatar' => $this->png('ich.png', 200, 200),
            'cover' => $this->png('wald.png', 800, 300),
        ]);
        $paths = [$user->fresh()->avatar_path, $user->fresh()->cover_path];

        $this->delete(route('profile.destroy'), ['password' => 'password']);

        Storage::disk('public')->assertMissing($paths);
    }
}
