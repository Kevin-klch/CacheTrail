<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountEmailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_verification_email_is_friendly_and_in_the_cachetrail_design(): void
    {
        $user = User::factory()->unverified()->create(['name' => 'Waldfuchs']);

        $mail = (new VerifyEmail)->toMail($user);
        $html = (string) $mail->render();

        $this->assertSame('Bestätige deine E-Mail-Adresse', $mail->subject);
        $this->assertStringContainsString('Hallo Waldfuchs,', $html);
        $this->assertStringContainsString('E-Mail-Adresse bestätigen', $html);
        $this->assertStringContainsString('Dein CacheTrail-Team', $html);
        $this->assertStringContainsString('#cc5a3d', $html, 'The CacheTrail theme colours are inlined.');
        $this->assertStringContainsString(route('legal.privacy'), $html);
        $this->assertStringNotContainsString('Laravel', $html);
    }

    public function test_the_password_reset_email_links_to_the_reset_form(): void
    {
        $user = User::factory()->create(['name' => 'Waldfuchs']);

        $mail = (new ResetPassword('geheimes-token'))->toMail($user);

        $this->assertSame('Setze dein Passwort zurück', $mail->subject);
        $this->assertSame('Neues Passwort wählen', $mail->actionText);
        $this->assertStringContainsString('geheimes-token', $mail->actionUrl);
        $this->assertStringContainsString(urlencode($user->email), $mail->actionUrl);
        $this->assertStringContainsString('Der Link ist 60 Minuten gültig.', (string) $mail->render());
    }
}
