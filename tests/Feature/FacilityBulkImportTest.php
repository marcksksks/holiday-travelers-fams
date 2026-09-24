<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class FacilityBulkImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()
            ->role(
                User::ROLE_ADMIN_OFFICER
            )
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function employee(): User
    {
        return User::factory()
            ->role(
                User::ROLE_EMPLOYEE
            )
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    public function test_facility_manager_can_open_bulk_import_workspace(): void
    {
        $this
            ->actingAs(
                $this->admin()
            )
            ->get(
                route(
                    'facilities.import.index'
                )
            )
            ->assertOk()
            ->assertSee(
                'Bulk Import Facilities'
            )
            ->assertSee(
                'CSV'
            )
            ->assertSee(
                'XLSX'
            )
            ->assertSee(
                'JSON'
            )
            ->assertSee(
                'Download CSV Template'
            );
    }

    public function test_employee_cannot_access_facility_import_workspace(): void
    {
        $employee =
            $this->employee();

        $this
            ->actingAs($employee)
            ->get(
                route(
                    'facilities.import.index'
                )
            )
            ->assertForbidden();

        $this
            ->actingAs($employee)
            ->get(
                route(
                    'facilities.import.template'
                )
            )
            ->assertForbidden();
    }

    public function test_csv_import_creates_multiple_facilities_and_audits_batch(): void
    {
        $admin =
            $this->admin();

        $csv = <<<'CSV'
name,description,location,capacity,facility_type,status,equipment
Board Room,Executive meetings,Main Office,20,meeting_room,available,Projector;Whiteboard
Training Hall,Staff training,Annex,40,training_room,maintenance,Projector;Sound System
CSV;

        $file =
            UploadedFile::fake()
                ->createWithContent(
                    'facilities.csv',
                    $csv
                );

        $this
            ->actingAs($admin)
            ->post(
                route(
                    'facilities.import.store'
                ),
                [
                    'import_file' => $file,
                ]
            )
            ->assertRedirect(
                route(
                    'facilities.index'
                )
            )
            ->assertSessionHas(
                'status',
                '2 facilities imported successfully from CSV.'
            );

        $this->assertDatabaseHas(
            'facilities',
            [
                'name' => 'Board Room',

                'capacity' => 20,

                'facility_type' => 'meeting_room',

                'status' => 'available',
            ]
        );

        $this->assertDatabaseHas(
            'facilities',
            [
                'name' => 'Training Hall',

                'capacity' => 40,

                'facility_type' => 'training_room',

                'status' => 'maintenance',
            ]
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $admin->email,

                'action' => 'bulk_import',

                'module' => 'facilities',

                'record_label' => 'Facility Bulk Import',
            ]
        );
    }

    public function test_json_import_creates_facilities(): void
    {
        $json =
            json_encode(
                [
                    'facilities' => [
                        [
                            'name' => 'JSON Room',

                            'description' => 'Imported from JSON',

                            'location' => 'Third Floor',

                            'capacity' => 8,

                            'facility_type' => 'conference room',

                            'status' => 'AVAILABLE',

                            'equipment' => [
                                'TV',
                                'Wi-Fi',
                            ],
                        ],
                    ],
                ],
                JSON_THROW_ON_ERROR
            );

        $file =
            UploadedFile::fake()
                ->createWithContent(
                    'facilities.json',
                    $json
                );

        $this
            ->actingAs(
                $this->admin()
            )
            ->post(
                route(
                    'facilities.import.store'
                ),
                [
                    'import_file' => $file,
                ]
            )
            ->assertRedirect(
                route(
                    'facilities.index'
                )
            );

        $this->assertDatabaseHas(
            'facilities',
            [
                'name' => 'JSON Room',

                'facility_type' => 'conference_room',

                'status' => 'available',
            ]
        );
    }

    public function test_xlsx_import_creates_facility(): void
    {
        $spreadsheet =
            new Spreadsheet;

        $sheet =
            $spreadsheet
                ->getActiveSheet();

        $sheet->fromArray(
            [
                [
                    'name',
                    'description',
                    'location',
                    'capacity',
                    'facility_type',
                    'status',
                    'equipment',
                ],
                [
                    'Excel Room',
                    'Imported workbook',
                    'Ground Floor',
                    16,
                    'meeting_room',
                    'available',
                    'Display;Whiteboard',
                ],
            ]
        );

        $temporary =
            tempnam(
                sys_get_temp_dir(),
                'fams-import-'
            );

        $xlsxPath =
            $temporary.
            '.xlsx';

        (new Xlsx(
            $spreadsheet
        ))->save(
            $xlsxPath
        );

        $spreadsheet
            ->disconnectWorksheets();

        try {
            $file =
                new UploadedFile(
                    $xlsxPath,
                    'facilities.xlsx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    null,
                    true
                );

            $this
                ->actingAs(
                    $this->admin()
                )
                ->post(
                    route(
                        'facilities.import.store'
                    ),
                    [
                        'import_file' => $file,
                    ]
                )
                ->assertRedirect(
                    route(
                        'facilities.index'
                    )
                );

            $this->assertDatabaseHas(
                'facilities',
                [
                    'name' => 'Excel Room',

                    'capacity' => 16,

                    'facility_type' => 'meeting_room',
                ]
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

    public function test_invalid_row_rejects_entire_batch(): void
    {
        $csv = <<<'CSV'
name,description,location,capacity,facility_type,status,equipment
Valid Room,Valid row,Main Office,10,meeting_room,available,TV
Broken Room,Invalid status,Main Office,5,meeting_room,not_a_status,TV
CSV;

        $file =
            UploadedFile::fake()
                ->createWithContent(
                    'facilities.csv',
                    $csv
                );

        $this
            ->actingAs(
                $this->admin()
            )
            ->from(
                route(
                    'facilities.import.index'
                )
            )
            ->post(
                route(
                    'facilities.import.store'
                ),
                [
                    'import_file' => $file,
                ]
            )
            ->assertRedirect(
                route(
                    'facilities.import.index'
                )
            )
            ->assertSessionHasErrors(
                'import_file'
            );

        $this->assertDatabaseMissing(
            'facilities',
            [
                'name' => 'Valid Room',
            ]
        );

        $this->assertDatabaseMissing(
            'facilities',
            [
                'name' => 'Broken Room',
            ]
        );
    }

    public function test_duplicate_existing_facility_is_rejected_without_partial_import(): void
    {
        Facility::factory()
            ->create([
                'name' => 'Existing Room',
            ]);

        $csv = <<<'CSV'
name,description,location,capacity,facility_type,status,equipment
New Room,New facility,Main Office,10,meeting_room,available,TV
Existing Room,Duplicate facility,Main Office,10,meeting_room,available,TV
CSV;

        $file =
            UploadedFile::fake()
                ->createWithContent(
                    'facilities.csv',
                    $csv
                );

        $this
            ->actingAs(
                $this->admin()
            )
            ->post(
                route(
                    'facilities.import.store'
                ),
                [
                    'import_file' => $file,
                ]
            )
            ->assertSessionHasErrors(
                'import_file'
            );

        $this->assertDatabaseMissing(
            'facilities',
            [
                'name' => 'New Room',
            ]
        );
    }

    public function test_malformed_json_is_rejected(): void
    {
        $file =
            UploadedFile::fake()
                ->createWithContent(
                    'facilities.json',
                    '{"facilities": [broken]}'
                );

        $this
            ->actingAs(
                $this->admin()
            )
            ->post(
                route(
                    'facilities.import.store'
                ),
                [
                    'import_file' => $file,
                ]
            )
            ->assertSessionHasErrors(
                'import_file'
            );

        $this->assertSame(
            0,
            Facility::count()
        );
    }

    public function test_unsupported_file_type_is_rejected(): void
    {
        $file =
            UploadedFile::fake()
                ->createWithContent(
                    'facilities.php',
                    '<?php echo "unsafe";'
                );

        $this
            ->actingAs(
                $this->admin()
            )
            ->post(
                route(
                    'facilities.import.store'
                ),
                [
                    'import_file' => $file,
                ]
            )
            ->assertSessionHasErrors(
                'import_file'
            );

        $this->assertSame(
            0,
            Facility::count()
        );
    }

    public function test_csv_template_is_downloadable(): void
    {
        $response =
            $this
                ->actingAs(
                    $this->admin()
                )
                ->get(
                    route(
                        'facilities.import.template'
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

        $content =
            $response
                ->streamedContent();

        $this->assertStringContainsString(
            'facility_type',
            $content
        );

        $this->assertStringContainsString(
            'Example Meeting Room',
            $content
        );
    }
}
