<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function manager(): User
    {
        return User::factory()
            ->role(
                User::ROLE_MANAGER
            )
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_reports_workspace_exposes_export_print_and_sort_controls(): void
    {
        $manager =
            $this->manager();

        $this
            ->actingAs($manager)
            ->get(
                route(
                    'reports.index',
                    [
                        'from' => '2026-09-01',

                        'to' => '2026-09-30',

                        'facility_sort' => 'name_asc',
                    ]
                )
            )
            ->assertOk()
            ->assertSee(
                'Export &amp; Print',
                false
            )
            ->assertSee(
                'Download PDF'
            )
            ->assertSee(
                'Download Excel'
            )
            ->assertSee(
                'Download CSV'
            )
            ->assertSee(
                'Print Report'
            )
            ->assertSee(
                'Facility Sort'
            )
            ->assertSee(
                'Most Booked First'
            )
            ->assertSee(
                'Least Booked First'
            )
            ->assertSee(
                'Facility Name A-Z'
            )
            ->assertSee(
                'Facility Name Z-A'
            )
            ->assertSee(
                'data-report-export',
                false
            )
            ->assertSee(
                'facility_sort=name_asc',
                false
            );
    }

    public function test_facility_utilization_can_be_sorted_by_facility_name(): void
    {
        $manager =
            $this->manager();

        $zulu =
            Facility::factory()
                ->create([
                    'name' => 'Zulu Conference Room',

                    'status' => 'available',
                ]);

        $alpha =
            Facility::factory()
                ->create([
                    'name' => 'Alpha Meeting Room',

                    'status' => 'available',
                ]);

        foreach ([
            [
                $zulu,
                '2026-09-10',
            ],
            [
                $alpha,
                '2026-09-11',
            ],
        ] as [
            $facility,
            $date,
        ]) {
            Reservation::create([
                'facility_id' => $facility->id,

                'facility_name' => $facility->name,

                'requester_email' => strtolower(
                    str_replace(
                        ' ',
                        '.',
                        $facility->name
                    )
                ).
                    '@example.test',

                'requester_name' => 'Report User',

                'date' => $date,

                'start_time' => '09:00',

                'end_time' => '10:00',

                'attendees' => 5,

                'status' => 'approved',
            ]);
        }

        $this
            ->actingAs($manager)
            ->get(
                route(
                    'reports.index',
                    [
                        'from' => '2026-09-01',

                        'to' => '2026-09-30',

                        'facility_sort' => 'name_asc',
                    ]
                )
            )
            ->assertOk()
            ->assertViewHas(
                'facilityUtilization',
                function ($rows) {
                    return
                        $rows
                            ->map(
                                fn ($row) => $row
                                    ->facility
                                    ?->name
                            )
                            ->values()
                            ->all()
                        ===
                        [
                            'Alpha Meeting Room',
                            'Zulu Conference Room',
                        ];
                }
            );
    }

    public function test_invalid_facility_sort_is_rejected(): void
    {
        $manager =
            $this->manager();

        $this
            ->actingAs($manager)
            ->get(
                route(
                    'reports.index',
                    [
                        'facility_sort' => 'unsupported_sort',
                    ]
                )
            )
            ->assertRedirect()
            ->assertSessionHasErrors(
                'facility_sort'
            );
    }
}
