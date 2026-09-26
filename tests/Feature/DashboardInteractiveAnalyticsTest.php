<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardInteractiveAnalyticsTest extends TestCase
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

    public function test_dashboard_contains_interactive_operational_analytics(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $facility =
            Facility::factory()
                ->create([
                    'name' => 'Dashboard Analytics Room',

                    'status' => 'available',
                ]);

        foreach ([
            today()
                ->subDays(2)
                ->toDateString(),

            today()
                ->subDays(15)
                ->toDateString(),
        ] as $date) {
            Reservation::create([
                'facility_id' => $facility->id,

                'facility_name' => $facility->name,

                'requester_email' => 'dashboard.analytics@example.test',

                'requester_name' => 'Dashboard Analytics User',

                'date' => $date,

                'start_time' => '09:00',

                'end_time' => '10:00',

                'attendees' => 5,

                'status' => 'approved',
            ]);
        }

        $response =
            $this
                ->actingAs($manager)
                ->get(
                    route('dashboard')
                );

        $response
            ->assertOk()
            ->assertSee(
                'Interactive Analytics'
            )
            ->assertSee(
                'Operational Activity Trend'
            )
            ->assertSee(
                'Reservation Status'
            )
            ->assertSee(
                'Facility Utilization'
            )
            ->assertSee(
                'Last 7 days'
            )
            ->assertSee(
                'Last 30 days'
            )
            ->assertSee(
                'data-dashboard-analytics-root',
                false
            )
            ->assertSee(
                'data-dashboard-analytics-range="7"',
                false
            )
            ->assertSee(
                'data-dashboard-chart-drilldown',
                false
            )
            ->assertSee(
                'Dashboard Analytics Room'
            )
            ->assertSee(
                'status=approved',
                false
            )
            ->assertSee(
                'facility='.$facility->id,
                false
            );

        $analytics =
            $response->viewData(
                'dashboardAnalytics'
            );

        $sevenStatuses =
            collect(
                $analytics[
                    'ranges'
                ][7][
                    'reservation_statuses'
                ]
            )
                ->keyBy(
                    'key'
                );

        $thirtyStatuses =
            collect(
                $analytics[
                    'ranges'
                ][30][
                    'reservation_statuses'
                ]
            )
                ->keyBy(
                    'key'
                );

        $sevenFacility =
            collect(
                $analytics[
                    'ranges'
                ][7][
                    'facility_utilization'
                ]
            )
                ->firstWhere(
                    'facility_id',
                    $facility->id
                );

        $thirtyFacility =
            collect(
                $analytics[
                    'ranges'
                ][30][
                    'facility_utilization'
                ]
            )
                ->firstWhere(
                    'facility_id',
                    $facility->id
                );

        $this->assertSame(
            1,
            $sevenStatuses[
                'approved'
            ][
                'count'
            ]
        );

        $this->assertSame(
            2,
            $thirtyStatuses[
                'approved'
            ][
                'count'
            ]
        );

        $this->assertSame(
            1,
            $sevenFacility[
                'count'
            ]
        );

        $this->assertSame(
            2,
            $thirtyFacility[
                'count'
            ]
        );
    }

    public function test_dashboard_analytics_series_respect_user_permissions(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $response =
            $this
                ->actingAs($employee)
                ->get(
                    route('dashboard')
                );

        $response->assertOk();

        $analytics =
            $response->viewData(
                'dashboardAnalytics'
            );

        $this->assertSame(
            $employee->can(
                'viewAppointments'
            ),
            $analytics[
                'can_view_appointments'
            ]
        );

        $this->assertSame(
            $employee->can(
                'viewVisitors'
            ),
            $analytics[
                'can_view_visitors'
            ]
        );
    }

    public function test_dashboard_chart_client_supports_range_switching_and_live_refresh(): void
    {
        $applicationSource =
            file_get_contents(
                resource_path(
                    'js/app.js'
                )
            );

        $realtimeSource =
            file_get_contents(
                resource_path(
                    'views/dashboard/_realtime-script.blade.php'
                )
            );

        $this->assertIsString(
            $applicationSource
        );

        $this->assertIsString(
            $realtimeSource
        );

        foreach ([
            'FAMS DASHBOARD INTERACTIVE ANALYTICS',
            '[data-dashboard-analytics-root]',
            '[data-dashboard-analytics-range]',
            '[data-dashboard-analytics-panel]',
            '[data-dashboard-line-chart]',
            'renderLineChart',
            'famsDashboardAnalyticsRefresh',
        ] as $expected) {
            $this->assertStringContainsString(
                $expected,
                $applicationSource
            );
        }

        $this->assertStringContainsString(
            'famsDashboardAnalyticsRefresh',
            $realtimeSource
        );
    }
}
