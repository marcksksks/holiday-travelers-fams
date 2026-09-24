<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ReportExportTest extends TestCase
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

    private function reportFixture(): void
    {
        $facility =
            Facility::factory()
                ->create([
                    'name' => 'Executive Meeting Room',

                    'status' => 'available',
                ]);

        Reservation::create([
            'facility_id' => $facility->id,

            'facility_name' => $facility->name,

            'requester_email' => 'report@example.test',

            'requester_name' => 'Report User',

            'date' => '2026-09-10',

            'start_time' => '09:00',

            'end_time' => '10:00',

            'attendees' => 15,

            'status' => 'approved',
        ]);
    }

    public function test_manager_can_download_pdf_report(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this->reportFixture();

        $response =
            $this
                ->actingAs($manager)
                ->get(
                    route(
                        'reports.export.pdf',
                        [
                            'from' => '2026-09-01',

                            'to' => '2026-09-30',
                        ]
                    )
                );

        $response
            ->assertOk()
            ->assertHeader(
                'content-type',
                'application/pdf'
            );

        $this->assertStringStartsWith(
            '%PDF',
            $response->getContent()
        );
    }

    public function test_manager_can_download_csv_with_accurate_period_data(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this->reportFixture();

        $facility =
            Facility::query()
                ->firstOrFail();

        Reservation::create([
            'facility_id' => $facility->id,

            'facility_name' => $facility->name,

            'requester_email' => 'outside@example.test',

            'requester_name' => 'Outside Range',

            'date' => '2026-08-10',

            'start_time' => '09:00',

            'end_time' => '10:00',

            'attendees' => 99,

            'status' => 'approved',
        ]);

        $response =
            $this
                ->actingAs($manager)
                ->get(
                    route(
                        'reports.export.csv',
                        [
                            'from' => '2026-09-01',

                            'to' => '2026-09-30',
                        ]
                    )
                );

        $response->assertOk();

        $this->assertStringContainsString(
            'text/csv',
            (string) $response
                ->headers
                ->get(
                    'content-type'
                )
        );

        $csv =
            $response
                ->streamedContent();

        $csv =
            preg_replace(
                '/^\xEF\xBB\xBF/',
                '',
                $csv
            );

        $stream =
            fopen(
                'php://temp',
                'w+b'
            );

        $this->assertNotFalse(
            $stream
        );

        fwrite(
            $stream,
            $csv
        );

        rewind(
            $stream
        );

        $rows = [];

        while (
            (
                $row =
                    fgetcsv(
                        $stream
                    )
            ) !== false
        ) {
            $rows[] =
                $row;
        }

        fclose(
            $stream
        );

        $this->assertContains(
            [
                'Executive Summary',
                'Reservations',
                '1',
            ],
            $rows
        );

        $this->assertContains(
            [
                'Executive Summary',
                'Reservation Attendees',
                '15',
            ],
            $rows
        );

        $this->assertContains(
            [
                'Facility Utilization',
                'Executive Meeting Room',
                '1',
            ],
            $rows
        );

        $outsideRangeValues =
            array_filter(
                $rows,
                fn (array $row) => in_array(
                    '99',
                    $row,
                    true
                )
            );

        $this->assertCount(
            0,
            $outsideRangeValues,
            'Outside-range reservation leaked into the CSV report.'
        );
    }

    public function test_manager_can_download_valid_xlsx_report(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this->reportFixture();

        $response =
            $this
                ->actingAs($manager)
                ->get(
                    route(
                        'reports.export.xlsx',
                        [
                            'from' => '2026-09-01',

                            'to' => '2026-09-30',
                        ]
                    )
                );

        $response->assertOk();

        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response
                ->headers
                ->get(
                    'content-type'
                )
        );

        $content =
            $response
                ->streamedContent();

        $this->assertStringStartsWith(
            'PK',
            $content
        );

        $temporary =
            tempnam(
                sys_get_temp_dir(),
                'fams-xlsx-'
            );

        $xlsxPath =
            $temporary.
            '.xlsx';

        file_put_contents(
            $xlsxPath,
            $content
        );

        try {
            $book =
                IOFactory::load(
                    $xlsxPath
                );

            $this->assertSame(
                [
                    'Executive Summary',
                    'Breakdowns',
                    'Facility Utilization',
                ],
                $book->getSheetNames()
            );

            $this->assertSame(
                'HOLIDAY TRAVELERS INC.',
                $book
                    ->getSheetByName(
                        'Executive Summary'
                    )
                    ->getCell('A1')
                    ->getValue()
            );

            $this->assertSame(
                'Executive Meeting Room',
                $book
                    ->getSheetByName(
                        'Facility Utilization'
                    )
                    ->getCell('A2')
                    ->getValue()
            );
        } finally {
            @unlink(
                $xlsxPath
            );

            @unlink(
                $temporary
            );
        }
    }

    public function test_manager_can_open_branded_print_report(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this->reportFixture();

        $this
            ->actingAs($manager)
            ->get(
                route(
                    'reports.print',
                    [
                        'from' => '2026-09-01',

                        'to' => '2026-09-30',
                    ]
                )
            )
            ->assertOk()
            ->assertSee(
                'HOLIDAY TRAVELERS INC.'
            )
            ->assertSee(
                'Facilities &amp; Administrative Management System',
                false
            )
            ->assertSee(
                'Print Report'
            )
            ->assertSee(
                'Executive Meeting Room'
            );
    }

    public function test_employee_cannot_access_report_exports(): void
    {
        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        foreach ([
            'reports.export.pdf',
            'reports.export.xlsx',
            'reports.export.csv',
            'reports.print',
        ] as $routeName) {
            $this
                ->actingAs($employee)
                ->get(
                    route(
                        $routeName
                    )
                )
                ->assertForbidden();
        }
    }

    public function test_report_exports_are_audited(): void
    {
        $manager =
            $this->user(
                User::ROLE_MANAGER
            );

        $this
            ->actingAs($manager)
            ->get(
                route(
                    'reports.export.csv'
                )
            )
            ->assertOk();

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $manager->email,

                'action' => 'report_export',

                'module' => 'reports',
            ]
        );

        $this
            ->actingAs($manager)
            ->get(
                route(
                    'reports.print'
                )
            )
            ->assertOk();

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $manager->email,

                'action' => 'report_print',

                'module' => 'reports',
            ]
        );
    }
}
