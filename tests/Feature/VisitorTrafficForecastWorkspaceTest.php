<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use App\Services\VisitorForecastDemoDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorTrafficForecastWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'visitor_forecast.demo_mode' => false,
        ]);
    }

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

    public function test_visitor_desk_displays_cold_start_forecast_truthfully(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $response =
            $this
                ->actingAs(
                    $receptionist
                )
                ->get(
                    route(
                        'visitors.index'
                    )
                );

        $response
            ->assertOk()
            ->assertSee(
                'Visitor Traffic Intelligence'
            )
            ->assertSee(
                'AI-Assisted Predictive Analytics'
            )
            ->assertSee(
                'Cold Start'
            )
            ->assertSee(
                'Historical learning is not available yet'
            )
            ->assertSee(
                'the system does not invent an ML prediction'
            )
            ->assertSee(
                'data-forecast-status="cold_start"',
                false
            )
            ->assertSee(
                'Zero API Cost'
            )
            ->assertSee(
                'data-synthetic-demo="false"',
                false
            )
            ->assertDontSee(
                'Synthetic Demonstration Data Active'
            );
    }

    public function test_scheduled_appointment_is_included_as_known_forecast_demand(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        Appointment::create([
            'visitor_name' => 'Forecast Test Visitor',

            'visitor_type' => 'guest',

            'date' => now()
                ->addDay()
                ->toDateString(),

            'start_time' => '10:00',

            'end_time' => '10:30',

            'purpose' => 'Forecast workspace verification',

            'status' => 'scheduled',
        ]);

        $this
            ->actingAs(
                $receptionist
            )
            ->get(
                route(
                    'visitors.index'
                )
            )
            ->assertOk()
            ->assertSee(
                'Scheduled Demand'
            )
            ->assertSee(
                'Known appointments from PostgreSQL'
            )
            ->assertSee(
                'data-forecast-scheduled="1"',
                false
            );
    }

    public function test_traffic_intelligence_preserves_existing_ai_assistant_during_transition(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $this
            ->actingAs(
                $receptionist
            )
            ->get(
                route(
                    'visitors.index'
                )
            )
            ->assertOk()
            ->assertSee(
                'Visitor Traffic Intelligence'
            )
            ->assertSee(
                'AI Assistant'
            )
            ->assertSee(
                'Register Visitor'
            )
            ->assertSee(
                'Visitor Activity'
            );
    }

    public function test_synthetic_demo_history_is_explicitly_disclosed_in_visitor_intelligence(): void
    {
        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        app(
            VisitorForecastDemoDataService::class
        )->seed(
            '2026-09-26'
        );

        $response =
            $this
                ->actingAs(
                    $receptionist
                )
                ->get(
                    route(
                        'visitors.index'
                    )
                );

        $response
            ->assertOk()
            ->assertSee(
                'Synthetic Demonstration Data Active'
            )
            ->assertSee(
                'Academic Demo'
            )
            ->assertSee(
                'These records are not client production history'
            )
            ->assertSee(
                'must not be presented as actual Holiday Travelers visitor traffic'
            )
            ->assertSee(
                'data-synthetic-demo="true"',
                false
            )
            ->assertSee(
                'data-synthetic-forecast-disclosure',
                false
            )
            ->assertSee(
                'Local ML Ready'
            );
    }

    public function test_in_memory_demo_mode_is_disclosed_without_persisting_operational_records(): void
    {
        config([
            'visitor_forecast.demo_mode' => true,
        ]);

        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $response =
            $this
                ->actingAs(
                    $receptionist
                )
                ->get(
                    route(
                        'visitors.index'
                    )
                );

        $response
            ->assertOk()
            ->assertSee(
                'Synthetic Demonstration Data Active'
            )
            ->assertSee(
                'Academic Demo'
            )
            ->assertSee(
                'In-memory demo mode'
            )
            ->assertSee(
                'No synthetic visitor, appointment, or reservation records are stored'
            )
            ->assertSee(
                'data-synthetic-demo="true"',
                false
            )
            ->assertSee(
                'Local ML Ready'
            )
            ->assertSee(
                'Demonstration'
            );

        $this->assertDatabaseCount(
            'visitors',
            0
        );

        $this->assertDatabaseCount(
            'appointments',
            0
        );

        $this->assertDatabaseCount(
            'reservations',
            0
        );
    }

    public function test_demo_workspace_displays_synthetic_backtest_without_accuracy_claim(): void
    {
        config([
            'visitor_forecast.demo_mode' => true,
        ]);

        $receptionist =
            $this->user(
                User::ROLE_RECEPTIONIST
            );

        $this
            ->actingAs(
                $receptionist
            )
            ->get(
                route(
                    'visitors.index'
                )
            )
            ->assertOk()
            ->assertSee(
                'Synthetic Model Validation'
            )
            ->assertSee(
                'Chronological Backtest'
            )
            ->assertSee(
                'Synthetic Backtest Only'
            )
            ->assertSee(
                'MAE'
            )
            ->assertSee(
                'WAPE'
            )
            ->assertSee(
                'Training Observations'
            )
            ->assertSee(
                'Holdout Observations'
            )
            ->assertSee(
                'do not establish production accuracy'
            )
            ->assertSee(
                'data-synthetic-backtest',
                false
            );
    }
}
