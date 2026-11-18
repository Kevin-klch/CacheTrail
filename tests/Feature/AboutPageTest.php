<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_read_about_cachetrail(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSeeInOrder(['Warum es CacheTrail gibt', 'So funktioniert es', 'Neu beim Geocachen?', 'Gut zu wissen'])
            ->assertSee('https://www.geocaching.com/play')
            ->assertSee(route('register'));
    }

    public function test_the_about_page_is_linked_from_the_home_page_and_the_footer(): void
    {
        $this->get(route('home'))
            ->assertSee(route('about'))
            ->assertSee(route('about').'#neu');
    }

    public function test_the_page_is_available_in_english(): void
    {
        $this->withSession(['locale' => 'en'])
            ->get(route('about'))
            ->assertSee('Why CacheTrail exists')
            ->assertSee('New to geocaching?');
    }
}
