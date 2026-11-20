<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_read_the_imprint_and_privacy_policy(): void
    {
        $this->get(route('legal.imprint'))
            ->assertOk()
            ->assertSee('Angaben gemäß § 5 DDG')
            ->assertSee('OpenStreetMap-Mitwirkende');

        $this->get(route('legal.privacy'))
            ->assertOk()
            ->assertSee('Karten von OpenStreetMap')
            ->assertSee('Ortssuche mit Nominatim')
            ->assertSee('Stand: 20. November 2026');
    }

    public function test_the_operator_details_come_from_the_configuration(): void
    {
        config([
            'cachetrail.legal.name' => 'Erika Musterfrau',
            'cachetrail.legal.street' => 'Waldweg 1',
            'cachetrail.legal.city' => '12345 Musterstadt',
            'cachetrail.legal.email' => 'kontakt@cachetrail.test',
        ]);

        $this->get(route('legal.imprint'))
            ->assertSeeInOrder(['Erika Musterfrau', 'Waldweg 1', '12345 Musterstadt', 'kontakt@cachetrail.test'])
            ->assertDontSee('CACHETRAIL_LEGAL_NAME');
    }

    public function test_missing_details_are_highlighted_outside_production(): void
    {
        config(['cachetrail.legal.name' => null]);

        $this->get(route('legal.imprint'))->assertSee('CACHETRAIL_LEGAL_NAME');
    }

    public function test_every_page_links_to_the_imprint_and_privacy_policy(): void
    {
        $this->get(route('home'))
            ->assertSee(route('legal.imprint'))
            ->assertSee(route('legal.privacy'));

        $this->get(route('register'))->assertSee(route('legal.privacy'));
    }
}
