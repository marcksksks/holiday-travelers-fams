<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_account_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'active-login@example.test',
            'password' => Hash::make(
                'SecureLoginPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $response = $this->post(
            '/login',
            [
                'email' => $user->email,
                'password' =>
                    'SecureLoginPassword123!',
            ]
        );

        $response->assertRedirect(
            route('dashboard')
        );

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_account_cannot_authenticate_even_with_correct_password(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive-login@example.test',
            'password' => Hash::make(
                'SecureLoginPassword123!'
            ),
            'is_active' => false,
            'force_password_change' => false,
        ]);

        $response = $this->from('/login')->post(
            '/login',
            [
                'email' => $user->email,
                'password' =>
                    'SecureLoginPassword123!',
            ]
        );

        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_invalid_credentials_do_not_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'invalid-login@example.test',
            'password' => Hash::make(
                'SecureLoginPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->from('/login')
            ->post(
                '/login',
                [
                    'email' => $user->email,
                    'password' =>
                        'IncorrectPassword123!',
                ]
            )
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'throttled-login@example.test',
            'password' => Hash::make(
                'SecureLoginPassword123!'
            ),
            'is_active' => true,
            'force_password_change' => false,
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->from('/login')
                ->post(
                    '/login',
                    [
                        'email' => $user->email,
                        'password' =>
                            'IncorrectPassword123!',
                    ]
                )
                ->assertRedirect('/login')
                ->assertSessionHasErrors('email');
        }

        $response = $this->from('/login')->post(
            '/login',
            [
                'email' => $user->email,
                'password' =>
                    'SecureLoginPassword123!',
            ]
        );

        $response
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $message = session(
            'errors'
        )->first('email');

        $this->assertStringContainsString(
            'Too many login attempts',
            $message
        );

        $this->assertGuest();
    }

    public function test_logout_invalidates_authenticated_session(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'force_password_change' => false,
        ]);

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(
                route('login')
            );

        $this->assertGuest();
    }
}