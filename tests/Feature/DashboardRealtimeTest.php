<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRealtimeTest extends TestCase
{
    use RefreshDatabase;

    private function user(
        string $role
    ): User {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_authenticated_user_can_request_dashboard_partial(): void
    {
        $user =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route(
                        'dashboard',
                        [
                            'partial' => 1,
                        ]
                    )
                );

        $response
            ->assertOk()
            ->assertSee(
                'System Overview'
            )
            ->assertSee(
                'Current Operations'
            )
            ->assertSee(
                'Needs Attention'
            )
            ->assertHeader(
                'Cache-Control'
            );
    }

    public function test_dashboard_partial_remains_role_aware(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this
            ->actingAs($employee)
            ->get(
                route(
                    'dashboard',
                    [
                        'partial' => 1,
                    ]
                )
            )
            ->assertOk()
            ->assertSee(
                'Available Facilities'
            )
            ->assertDontSee(
                'Legal Action Required'
            )
            ->assertDontSee(
                'Contract Renewals'
            );
    }

    public function test_guest_cannot_poll_dashboard_partial(): void
    {
        $this
            ->get(
                route(
                    'dashboard',
                    [
                        'partial' => 1,
                    ]
                )
            )
            ->assertRedirect(
                route('login')
            );
    }

    public function test_full_dashboard_contains_realtime_client(): void
    {
        $user =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($user)
            ->get(
                route('dashboard')
            )
            ->assertOk()
            ->assertSee(
                'data-dashboard-realtime-root',
                false
            )
            ->assertSee(
                'data-dashboard-live-status',
                false
            )
            ->assertSee(
                'Live · connecting...'
            );
    }
}
