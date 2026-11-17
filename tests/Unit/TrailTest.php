<?php

namespace Tests\Unit;

use App\Enums\TrailVisibility;
use App\Models\Trail;
use Tests\TestCase;

class TrailTest extends TestCase
{
    public function test_duration_label_formats_hours_and_minutes(): void
    {
        $this->assertSame('45 Min.', (new Trail(['estimated_duration_minutes' => 45]))->durationLabel());
        $this->assertSame('2 Std.', (new Trail(['estimated_duration_minutes' => 120]))->durationLabel());
        $this->assertSame('2 Std. 30 Min.', (new Trail(['estimated_duration_minutes' => 150]))->durationLabel());
    }

    public function test_description_is_split_into_paragraphs_at_blank_lines(): void
    {
        $trail = new Trail(['description' => "Erster Absatz\nmit Zeilenumbruch.\r\n\r\nZweiter Absatz.\n\n\n  \nDritter."]);

        $this->assertSame(
            ["Erster Absatz\nmit Zeilenumbruch.", 'Zweiter Absatz.', 'Dritter.'],
            $trail->descriptionParagraphs(),
        );
    }

    public function test_publishing_sets_the_publication_date_only_once(): void
    {
        $trail = new Trail;

        $trail->changeVisibility(TrailVisibility::Published);
        $firstPublishedAt = $trail->published_at;

        $this->travel(1)->day();
        $trail->changeVisibility(TrailVisibility::Hidden);
        $trail->changeVisibility(TrailVisibility::Published);

        $this->assertNotNull($firstPublishedAt);
        $this->assertTrue($firstPublishedAt->equalTo($trail->published_at));
    }
}
