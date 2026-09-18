<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetEnumerationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_and_unknown_email_receive_same_public_response(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'known-user@example.test',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $knownResponse = $this
            ->from(route('password.request'))
            ->post(
                route('password.email'),
                [
                    'email' => $user->email,
                ]
            );

        $knownResponse->assertRedirect(
            route('password.request')
        );

        $knownMessage = session('status');

        Notification::assertSentTo(
            $user,
            ResetPassword::class
        );

        $unknownResponse = $this
            ->from(route('password.request'))
            ->post(
                route('password.email'),
                [
                    'email' =>
                        'unknown-user@example.test',
                ]
            );

        $unknownResponse->assertRedirect(
            route('password.request')
        );

        $unknownMessage = session('status');

        $this->assertNotNull(
            $knownMessage
        );

        $this->assertSame(
            $knownMessage,
            $unknownMessage
        );

        $this->assertSame(
            'If an account exists for that email address, a password reset link has been sent.',
            $knownMessage
        );
    }

    public function test_invalid_email_format_still_fails_validation(): void
    {
        $this
            ->from(route('password.request'))
            ->post(
                route('password.email'),
                [
                    'email' => 'not-an-email',
                ]
            )
            ->assertRedirect(
                route('password.request')
            )
            ->assertSessionHasErrors(
                'email'
            );
    }
}