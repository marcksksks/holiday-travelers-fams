<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiRateLimitSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function activeUser(): User
    {
        return User::factory()
            ->role(User::ROLE_MANAGER)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_api_limiter_is_registered_with_authenticated_user_identity(): void
    {
        $user =
            $this->activeUser();

        $request =
            Request::create(
                '/api/me',
                'GET'
            );

        $request->setUserResolver(
            fn (): User => $user
        );

        $callback =
            RateLimiter::limiter(
                'api'
            );

        $this->assertNotNull(
            $callback
        );

        $limit =
            $callback(
                $request
            );

        $this->assertInstanceOf(
            Limit::class,
            $limit
        );

        $this->assertSame(
            120,
            $limit->maxAttempts
        );

        $this->assertSame(
            "user:{$user->getAuthIdentifier()}",
            $limit->key
        );
    }

    public function test_api_throttle_returns_429_and_isolates_authenticated_users(): void
    {
        RateLimiter::for(
            'api',
            function (Request $request): Limit {
                $userId =
                    $request->user()
                        ?->getAuthIdentifier();

                return Limit::perMinute(1)
                    ->by(
                        $userId !== null
                            ? "test-user:{$userId}"
                            : 'test-guest'
                    );
            }
        );

        $firstUser =
            $this->activeUser();

        $secondUser =
            $this->activeUser();

        Sanctum::actingAs(
            $firstUser
        );

        $this
            ->getJson('/api/me')
            ->assertOk();

        $this
            ->getJson('/api/me')
            ->assertStatus(429);

        Sanctum::actingAs(
            $secondUser
        );

        $this
            ->getJson('/api/me')
            ->assertOk();
    }
}