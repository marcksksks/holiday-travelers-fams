<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SensitiveEndpointRateLimitSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()
            ->role(User::ROLE_MANAGER)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_sensitive_limiters_are_registered_with_expected_policies(): void
    {
        $manager =
            $this->manager();

        $authenticatedRequest =
            Request::create(
                '/visitors/ai-assist',
                'POST'
            );

        $authenticatedRequest
            ->setUserResolver(
                fn (): User => $manager
            );

        $guestRequest =
            Request::create(
                '/forgot-password',
                'POST',
                [],
                [],
                [],
                [
                    'REMOTE_ADDR' =>
                        '198.51.100.25',
                ]
            );

        $passwordLimiter =
            RateLimiter::limiter(
                'password-reset-link'
            );

        $aiLimiter =
            RateLimiter::limiter(
                'ai-assist'
            );

        $calendarLimiter =
            RateLimiter::limiter(
                'calendar-sync'
            );

        $this->assertNotNull(
            $passwordLimiter
        );

        $this->assertNotNull(
            $aiLimiter
        );

        $this->assertNotNull(
            $calendarLimiter
        );

        $passwordLimit =
            $passwordLimiter(
                $guestRequest
            );

        $aiLimit =
            $aiLimiter(
                $authenticatedRequest
            );

        $calendarLimit =
            $calendarLimiter(
                $authenticatedRequest
            );

        $this->assertInstanceOf(
            Limit::class,
            $passwordLimit
        );

        $this->assertSame(
            5,
            $passwordLimit->maxAttempts
        );

        $this->assertSame(
            'password-reset-link:198.51.100.25',
            $passwordLimit->key
        );

        $this->assertSame(
            10,
            $aiLimit->maxAttempts
        );

        $this->assertSame(
            "ai-assist:user:{$manager->id}",
            $aiLimit->key
        );

        $this->assertSame(
            5,
            $calendarLimit->maxAttempts
        );

        $this->assertSame(
            "calendar-sync:user:{$manager->id}",
            $calendarLimit->key
        );
    }

    public function test_sensitive_routes_have_targeted_throttle_middleware(): void
    {
        $passwordRoute =
            Route::getRoutes()
                ->getByName(
                    'password.email'
                );

        $aiRoute =
            Route::getRoutes()
                ->getByName(
                    'visitors.ai-assist'
                );

        $calendarRoute =
            Route::getRoutes()
                ->getByName(
                    'visitors.sync-calendar'
                );

        $this->assertNotNull(
            $passwordRoute
        );

        $this->assertNotNull(
            $aiRoute
        );

        $this->assertNotNull(
            $calendarRoute
        );

        $this->assertContains(
            'throttle:password-reset-link',
            $passwordRoute
                ->gatherMiddleware()
        );

        $this->assertContains(
            'throttle:ai-assist',
            $aiRoute
                ->gatherMiddleware()
        );

        $this->assertContains(
            'throttle:calendar-sync',
            $calendarRoute
                ->gatherMiddleware()
        );
    }

    public function test_password_reset_link_route_returns_429_after_limit(): void
    {
        RateLimiter::for(
            'password-reset-link',
            fn (Request $request): Limit =>
                Limit::perMinute(1)
                    ->by(
                        'test-password-reset:'.
                        $request->ip()
                    )
        );

        $server = [
            'REMOTE_ADDR' =>
                '198.51.100.77',
        ];

        $first =
            $this
                ->withServerVariables(
                    $server
                )
                ->post(
                    route('password.email'),
                    [
                        'email' =>
                            'missing@example.test',
                    ]
                );

        $first->assertRedirect();

        $this
            ->withServerVariables(
                $server
            )
            ->post(
                route('password.email'),
                [
                    'email' =>
                        'other@example.test',
                ]
            )
            ->assertStatus(429);
    }

    public function test_ai_assist_route_returns_429_after_per_user_limit(): void
    {
        config([
            'services.ai_assist.provider' =>
                'none',

            'services.ai_assist.api_key' =>
                null,

            'services.ai_assist.endpoint' =>
                null,
        ]);

        RateLimiter::for(
            'ai-assist',
            function (Request $request): Limit {
                $userId =
                    $request->user()
                        ?->getAuthIdentifier();

                return Limit::perMinute(1)
                    ->by(
                        "test-ai-user:{$userId}"
                    );
            }
        );

        $manager =
            $this->manager();

        $first =
            $this
                ->actingAs($manager)
                ->postJson(
                    route(
                        'visitors.ai-assist'
                    ),
                    [
                        'mode' =>
                            'summary',

                        'text' =>
                            'Visitor security test.',
                    ]
                );

        $first->assertOk();

        $this
            ->actingAs($manager)
            ->postJson(
                route(
                    'visitors.ai-assist'
                ),
                [
                    'mode' =>
                        'summary',

                    'text' =>
                        'Second visitor security test.',
                ]
            )
            ->assertStatus(429);
    }
}