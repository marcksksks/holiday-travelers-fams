<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\Appointment;
use App\Models\Facility;
use App\Models\LegalRecord;
use App\Models\Reservation;
use App\Models\RetentionPolicy;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class LifecycleAndAuditSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::factory()
            ->role($role)
            ->create([
                'is_active' => true,
                'force_password_change' => false,
            ]);
    }

    private function roles(): array
    {
        return [
            User::ROLE_EMPLOYEE,
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];
    }

    private function allowed(
        string $role,
        array $allowed
    ): bool {
        return in_array(
            $role,
            $allowed,
            true
        );
    }

    private function document(
        string $title,
        array $extra = []
    ): ArchiveDocument {
        return ArchiveDocument::create(
            array_merge(
                [
                    'title' => $title,
                    'category' => 'administrative',
                    'confidentiality' => 'general',
                    'status' => 'active',
                    'version' => 1,
                    'is_system_generated' => false,
                ],
                $extra
            )
        );
    }

    public function test_facility_update_and_archive_match_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $facility = Facility::factory()
                ->create([
                    'name' => "Original {$role}",
                    'status' => 'available',
                ]);

            $update = $this
                ->actingAs($actor, 'sanctum')
                ->putJson(
                    "/api/facilities/{$facility->id}",
                    [
                        'name' =>
                            "Updated {$role}",

                        'facility_type' =>
                            'meeting_room',

                        'status' =>
                            'available',

                        'capacity' =>
                            20,
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $update->assertOk();

                $this->assertSame(
                    "Updated {$role}",
                    $facility->refresh()->name
                );
            } else {
                $update->assertForbidden();

                $this->assertSame(
                    "Original {$role}",
                    $facility->refresh()->name
                );
            }

            $delete = $this
                ->actingAs($actor, 'sanctum')
                ->deleteJson(
                    "/api/facilities/{$facility->id}"
                );

            if ($this->allowed($role, $allowed)) {
                $delete->assertNoContent();

                $this->assertSame(
                    'archived',
                    $facility->refresh()->status
                );
            } else {
                $delete->assertForbidden();

                $this->assertSame(
                    'available',
                    $facility->refresh()->status
                );
            }
        }
    }

    public function test_archived_facility_visibility_respects_permission(): void
    {
        $facility = Facility::factory()
            ->create([
                'status' => 'archived',
            ]);

        $employee = $this->user(
            User::ROLE_EMPLOYEE
        );

        $manager = $this->user(
            User::ROLE_MANAGER
        );

        $this->actingAs(
            $employee,
            'sanctum'
        )
            ->getJson(
                "/api/facilities/{$facility->id}"
            )
            ->assertNotFound();

        $this->actingAs(
            $manager,
            'sanctum'
        )
            ->getJson(
                "/api/facilities/{$facility->id}"
            )
            ->assertOk();
    }

    public function test_appointment_update_and_cancel_match_role_matrix(): void
    {
        $allowed = [
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $appointment = Appointment::create([
                'visitor_name' =>
                    "Original Visitor {$role}",

                'visitor_type' =>
                    'guest',

                'date' =>
                    now()
                        ->addDays(10)
                        ->toDateString(),

                'start_time' =>
                    '09:00',

                'end_time' =>
                    '10:00',

                'status' =>
                    'scheduled',
            ]);

            $update = $this
                ->actingAs($actor, 'sanctum')
                ->putJson(
                    "/api/appointments/{$appointment->id}",
                    [
                        'visitor_name' =>
                            "Updated Visitor {$role}",

                        'visitor_type' =>
                            'guest',

                        'date' =>
                            now()
                                ->addDays(11)
                                ->toDateString(),

                        'start_time' =>
                            '10:00',

                        'end_time' =>
                            '11:00',

                        'purpose' =>
                            'Lifecycle RBAC test',
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $update->assertOk();

                $this->assertSame(
                    "Updated Visitor {$role}",
                    $appointment
                        ->refresh()
                        ->visitor_name
                );
            } else {
                $update->assertForbidden();

                $this->assertSame(
                    "Original Visitor {$role}",
                    $appointment
                        ->refresh()
                        ->visitor_name
                );
            }

            $cancel = $this
                ->actingAs($actor, 'sanctum')
                ->deleteJson(
                    "/api/appointments/{$appointment->id}"
                );

            if ($this->allowed($role, $allowed)) {
                $cancel->assertNoContent();

                $this->assertSame(
                    'cancelled',
                    $appointment
                        ->refresh()
                        ->status
                );
            } else {
                $cancel->assertForbidden();

                $this->assertSame(
                    'scheduled',
                    $appointment
                        ->refresh()
                        ->status
                );
            }
        }
    }

    public function test_non_owner_reservation_cancellation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $owner = User::factory()
                ->role(User::ROLE_EMPLOYEE)
                ->create();

            $facility = Facility::factory()
                ->create([
                    'status' => 'available',
                ]);

            $reservation = Reservation::create([
                'facility_id' =>
                    $facility->id,

                'facility_name' =>
                    $facility->name,

                'requester_email' =>
                    $owner->email,

                'requester_name' =>
                    $owner->full_name,

                'date' =>
                    now()
                        ->addDays(15)
                        ->toDateString(),

                'start_time' =>
                    '09:00',

                'end_time' =>
                    '10:00',

                'status' =>
                    'pending',
            ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->deleteJson(
                    "/api/reservations/{$reservation->id}"
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertNoContent();

                $this->assertSame(
                    'cancelled',
                    $reservation
                        ->refresh()
                        ->status
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'pending',
                    $reservation
                        ->refresh()
                        ->status
                );
            }
        }
    }

    public function test_reservation_owner_can_cancel_own_request(): void
    {
        $owner = $this->user(
            User::ROLE_EMPLOYEE
        );

        $facility = Facility::factory()
            ->create([
                'status' => 'available',
            ]);

        $reservation = Reservation::create([
            'facility_id' => $facility->id,
            'facility_name' => $facility->name,
            'requester_email' => $owner->email,
            'requester_name' => $owner->full_name,

            'date' =>
                now()
                    ->addDays(15)
                    ->toDateString(),

            'start_time' => '13:00',
            'end_time' => '14:00',
            'status' => 'pending',
        ]);

        $this->actingAs(
            $owner,
            'sanctum'
        )
            ->deleteJson(
                "/api/reservations/{$reservation->id}"
            )
            ->assertNoContent();

        $this->assertSame(
            'cancelled',
            $reservation
                ->refresh()
                ->status
        );
    }

    public function test_visitor_check_out_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_RECEPTIONIST,
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $visitor = Visitor::create([
                'full_name' =>
                    "Checkout Visitor {$role}",

                'visitor_type' =>
                    'guest',

                'is_walk_in' =>
                    true,

                'status' =>
                    'checked_in',

                'check_in_at' =>
                    now()->subMinutes(10),
            ]);

            $response = $this
                ->actingAs($actor, 'sanctum')
                ->postJson(
                    "/api/visitors/{$visitor->id}/check-out"
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertOk();

                $visitor->refresh();

                $this->assertSame(
                    'completed',
                    $visitor->status
                );

                $this->assertNotNull(
                    $visitor->check_out_at
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'checked_in',
                    $visitor
                        ->refresh()
                        ->status
                );
            }
        }
    }

    public function test_document_archive_and_restore_match_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $document = $this->document(
                "Lifecycle Document {$role}"
            );

            $archive = $this
                ->actingAs($actor)
                ->post(
                    "/documents/{$document->id}/archive"
                );

            if ($this->allowed($role, $allowed)) {
                $archive->assertStatus(302);

                $this->assertSame(
                    'archived',
                    $document
                        ->refresh()
                        ->status
                );

                $restore = $this
                    ->actingAs($actor)
                    ->post(
                        "/documents/{$document->id}/restore"
                    );

                $restore->assertStatus(302);

                $this->assertSame(
                    'active',
                    $document
                        ->refresh()
                        ->status
                );
            } else {
                $archive->assertForbidden();

                $this->assertSame(
                    'active',
                    $document
                        ->refresh()
                        ->status
                );
            }
        }
    }

    public function test_document_version_upload_matches_role_matrix(): void
    {
        Storage::fake('documents');

        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $document = $this->document(
                "Version Document {$role}"
            );

            $response = $this
                ->actingAs($actor)
                ->post(
                    "/documents/{$document->id}/version",
                    [
                        'file' =>
                            UploadedFile::fake()
                                ->create(
                                    "version-{$role}.pdf",
                                    10,
                                    'application/pdf'
                                ),

                        'version_note' =>
                            'Lifecycle security test.',
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertStatus(302);

                $this->assertSame(
                    2,
                    $document
                        ->refresh()
                        ->version
                );

                $this->assertNotNull(
                    $document->file_uri
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    1,
                    $document
                        ->refresh()
                        ->version
                );
            }
        }
    }

    public function test_legal_review_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $legal = LegalRecord::create([
                'title' =>
                    "Legal Review {$role}",

                'record_type' =>
                    'permit',

                'status' =>
                    'active',
            ]);

            $response = $this
                ->actingAs($actor)
                ->post(
                    "/legal/{$legal->id}/review",
                    [
                        'review_status' =>
                            'reviewed',

                        'legal_notes' =>
                            'Lifecycle RBAC review.',
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertStatus(302);

                $legal->refresh();

                $this->assertSame(
                    'reviewed',
                    $legal->review_status
                );

                $this->assertSame(
                    'Lifecycle RBAC review.',
                    $legal->legal_notes
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'not_reviewed',
                    $legal
                        ->refresh()
                        ->review_status
                );
            }
        }
    }

    public function test_retention_policy_creation_matches_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $before =
                RetentionPolicy::count();

            $response = $this
                ->actingAs($actor)
                ->post(
                    '/retention-policies',
                    [
                        'name' =>
                            "Policy {$role}",

                        'record_category' =>
                            'administrative',

                        'retention_years' =>
                            5,

                        'description' =>
                            'Lifecycle security test.',

                        'legal_basis' =>
                            'Internal policy test',

                        'is_active' =>
                            true,
                    ]
                );

            if ($this->allowed($role, $allowed)) {
                $response->assertStatus(302);

                $this->assertSame(
                    $before + 1,
                    RetentionPolicy::count()
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    $before,
                    RetentionPolicy::count()
                );
            }
        }
    }

    public function test_document_download_requires_valid_signature(): void
    {
        Storage::fake('documents');

        $manager = $this->user(
            User::ROLE_MANAGER
        );

        Storage::disk('documents')
            ->put(
                'archive/signed-test.txt',
                'private test content'
            );

        $document = $this->document(
            'Signed Download Test',
            [
                'file_uri' =>
                    'archive/signed-test.txt',

                'file_name' =>
                    'signed-test.txt',
            ]
        );

        $this->actingAs($manager)
            ->get(
                route(
                    'documents.download',
                    $document
                )
            )
            ->assertForbidden();

        $signedUrl =
            URL::temporarySignedRoute(
                'documents.download',
                now()->addMinutes(5),
                [
                    'document' =>
                        $document->id,
                ]
            );

        $this->actingAs($manager)
            ->get($signedUrl)
            ->assertOk();
    }

    public function test_valid_signature_does_not_bypass_source_module_rbac(): void
    {
        Storage::fake('documents');

        $legal = $this->user(
            User::ROLE_LEGAL_OFFICER
        );

        Storage::disk('documents')
            ->put(
                'archive/visitor-secret.txt',
                'visitor record'
            );

        $document = $this->document(
            'Visitor Secret',
            [
                'source_module' =>
                    'visitors',

                'file_uri' =>
                    'archive/visitor-secret.txt',

                'file_name' =>
                    'visitor-secret.txt',
            ]
        );

        $signedUrl =
            URL::temporarySignedRoute(
                'documents.download',
                now()->addMinutes(5),
                [
                    'document' =>
                        $document->id,
                ]
            );

        $this->actingAs($legal)
            ->get($signedUrl)
            ->assertStatus(302)
            ->assertSessionHasErrors('document');
    }

    public function test_valid_signature_does_not_bypass_document_management_permission(): void
    {
        Storage::fake('documents');

        $employee = $this->user(
            User::ROLE_EMPLOYEE
        );

        Storage::disk('documents')
            ->put(
                'archive/general-secret.txt',
                'general private record'
            );

        $document = $this->document(
            'General Private File',
            [
                'file_uri' =>
                    'archive/general-secret.txt',

                'file_name' =>
                    'general-secret.txt',
            ]
        );

        $signedUrl =
            URL::temporarySignedRoute(
                'documents.download',
                now()->addMinutes(5),
                [
                    'document' =>
                        $document->id,
                ]
            );

        $this->actingAs($employee)
            ->get($signedUrl)
            ->assertStatus(302)
            ->assertSessionHasErrors('document');
    }

    public function test_sensitive_lifecycle_actions_create_audit_records(): void
    {
        /*
         * Document archive audit.
         */
        $admin = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $document = $this->document(
            'Audit Document'
        );

        $this->actingAs($admin)
            ->post(
                "/documents/{$document->id}/archive"
            )
            ->assertStatus(302);

        $this->assertTrue(
            DB::table('audit_logs')
                ->where(
                    'actor_email',
                    $admin->email
                )
                ->where(
                    'record_id',
                    (string) $document->id
                )
                ->exists()
        );

        /*
         * Legal review audit.
         */
        $legalOfficer = $this->user(
            User::ROLE_LEGAL_OFFICER
        );

        $legal = LegalRecord::create([
            'title' => 'Audit Legal Record',
            'record_type' => 'permit',
            'status' => 'active',
        ]);

        $this->actingAs($legalOfficer)
            ->post(
                "/legal/{$legal->id}/review",
                [
                    'review_status' =>
                        'reviewed',

                    'legal_notes' =>
                        'Audit test.',
                ]
            )
            ->assertStatus(302);

        $this->assertTrue(
            DB::table('audit_logs')
                ->where(
                    'actor_email',
                    $legalOfficer->email
                )
                ->where(
                    'record_id',
                    (string) $legal->id
                )
                ->exists()
        );

        /*
         * Visitor checkout audit.
         */
        $receptionist = $this->user(
            User::ROLE_RECEPTIONIST
        );

        $visitor = Visitor::create([
            'full_name' =>
                'Audit Visitor',

            'visitor_type' =>
                'guest',

            'is_walk_in' =>
                true,

            'status' =>
                'checked_in',

            'check_in_at' =>
                now()->subMinutes(5),
        ]);

        $this->actingAs(
            $receptionist,
            'sanctum'
        )
            ->postJson(
                "/api/visitors/{$visitor->id}/check-out"
            )
            ->assertOk();

        $this->assertTrue(
            DB::table('audit_logs')
                ->where(
                    'actor_email',
                    $receptionist->email
                )
                ->where(
                    'record_id',
                    (string) $visitor->id
                )
                ->exists()
        );
    }
}