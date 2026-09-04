<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\RecordRetention;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DocumentAndRetentionSecurityTest extends TestCase
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
                ],
                $extra
            )
        );
    }

    private function retention(
        string $title,
        array $extra = []
    ): RecordRetention {
        $id = DB::table('record_retentions')
            ->insertGetId(
                array_merge(
                    [
                        'record_title' => $title,
                        'record_type' => 'document',
                        'status' => 'review_required',
                        'compliance_status' => 'at_risk',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    $extra
                )
            );

        return RecordRetention::findOrFail($id);
    }

    public function test_legal_officer_document_list_hides_visitor_source_documents(): void
    {
        $legal = $this->user(
            User::ROLE_LEGAL_OFFICER
        );

        $this->document(
            'General Administrative Document'
        );

        $this->document(
            'Visitor Source Document',
            [
                'source_module' => 'visitors',
            ]
        );

        $this->document(
            'Legal Source Document',
            [
                'source_module' => 'legal',
            ]
        );

        $this->document(
            'Contract Source Document',
            [
                'source_module' => 'contracts',
            ]
        );

        $response = $this
            ->actingAs($legal, 'sanctum')
            ->getJson('/api/documents');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' =>
                    'General Administrative Document',
            ])
            ->assertJsonFragment([
                'title' =>
                    'Legal Source Document',
            ])
            ->assertJsonFragment([
                'title' =>
                    'Contract Source Document',
            ])
            ->assertJsonMissing([
                'title' =>
                    'Visitor Source Document',
            ]);
    }

    public function test_legal_officer_cannot_directly_open_visitor_document(): void
    {
        $legal = $this->user(
            User::ROLE_LEGAL_OFFICER
        );

        $visitorDocument = $this->document(
            'Protected Visitor Document',
            [
                'source_module' => 'visitors',
            ]
        );

        $this->actingAs(
            $legal,
            'sanctum'
        )
            ->getJson(
                "/api/documents/{$visitorDocument->id}"
            )
            ->assertForbidden();
    }

    public function test_legal_officer_can_open_legal_and_contract_documents(): void
    {
        $legal = $this->user(
            User::ROLE_LEGAL_OFFICER
        );

        $legalDocument = $this->document(
            'Legal Department Document',
            [
                'source_module' => 'legal',
            ]
        );

        $contractDocument = $this->document(
            'Contract Department Document',
            [
                'source_module' => 'contracts',
            ]
        );

        $this->actingAs(
            $legal,
            'sanctum'
        )
            ->getJson(
                "/api/documents/{$legalDocument->id}"
            )
            ->assertOk();

        $this->actingAs(
            $legal,
            'sanctum'
        )
            ->getJson(
                "/api/documents/{$contractDocument->id}"
            )
            ->assertOk();
    }

    public function test_document_owner_without_document_permission_is_still_denied(): void
    {
        $employee = $this->user(
            User::ROLE_EMPLOYEE
        );

        $document = $this->document(
            'Confidential Employee-Owned Document',
            [
                'confidentiality' => 'confidential',
                'owner_email' => $employee->email,
                'uploaded_by_email' => $employee->email,
            ]
        );

        $this->actingAs(
            $employee,
            'sanctum'
        )
            ->getJson(
                "/api/documents/{$document->id}"
            )
            ->assertForbidden();
    }

    public function test_authorized_document_roles_can_open_confidential_documents(): void
    {
        $document = $this->document(
            'Confidential Management Document',
            [
                'confidentiality' => 'confidential',
            ]
        );

        foreach ([
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_MANAGER,
            User::ROLE_LEGAL_OFFICER,
            User::ROLE_SYS_ADMIN,
        ] as $role) {
            $actor = $this->user($role);

            $this->actingAs(
                $actor,
                'sanctum'
            )
                ->getJson(
                    "/api/documents/{$document->id}"
                )
                ->assertOk();
        }
    }

    public function test_mark_for_disposal_matches_retention_management_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        /*
         * Give notification code potential
         * approvers to discover.
         */
        $this->user(
            User::ROLE_MANAGER
        );

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $retention = $this->retention(
                "Retention Decision {$role}"
            );

            $response = $this
                ->actingAs($actor)
                ->post(
                    "/retention/{$retention->id}/decision",
                    [
                        'decision' =>
                            'mark_for_disposal',

                        'compliance_status' =>
                            'at_risk',

                        'notes' =>
                            'Security test disposal request.',
                    ]
                );

            if (
                in_array(
                    $role,
                    $allowed,
                    true
                )
            ) {
                $response->assertRedirect(
                    route('retention.index')
                );

                $retention->refresh();

                $this->assertSame(
                    'marked_for_disposal',
                    $retention->status
                );

                $this->assertSame(
                    'pending',
                    $retention->disposition_status
                );

                $this->assertSame(
                    $actor->email,
                    $retention
                        ->disposition_requested_by
                );
            } else {
                $response->assertForbidden();

                $retention->refresh();

                $this->assertSame(
                    'review_required',
                    $retention->status
                );

                $this->assertNull(
                    $retention
                        ->disposition_status
                );
            }
        }
    }

    public function test_disposal_approval_matches_approval_role_matrix(): void
    {
        $allowed = [
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $requester = $this->user(
                User::ROLE_ADMIN_OFFICER
            );

            $retention = $this->retention(
                "Approval Matrix {$role}",
                [
                    'status' =>
                        'marked_for_disposal',

                    'disposition_status' =>
                        'pending',

                    'disposition_requested_by' =>
                        $requester->email,

                    'disposition_requested_at' =>
                        now(),
                ]
            );

            $response = $this
                ->actingAs($actor)
                ->post(
                    "/retention/{$retention->id}/disposition/approve",
                    [
                        'decision_notes' =>
                            'Approval matrix test.',
                    ]
                );

            if (
                in_array(
                    $role,
                    $allowed,
                    true
                )
            ) {
                $response->assertRedirect(
                    route('retention.index')
                );

                $retention->refresh();

                $this->assertSame(
                    'approved',
                    $retention
                        ->disposition_status
                );

                $this->assertSame(
                    $actor->email,
                    $retention
                        ->disposition_decided_by
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'pending',
                    $retention
                        ->refresh()
                        ->disposition_status
                );
            }
        }
    }

    public function test_disposal_rejection_matches_approval_role_matrix(): void
    {
        $allowed = [
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach ($this->roles() as $role) {
            $actor = $this->user($role);

            $requester = $this->user(
                User::ROLE_ADMIN_OFFICER
            );

            $retention = $this->retention(
                "Rejection Matrix {$role}",
                [
                    'status' =>
                        'marked_for_disposal',

                    'compliance_status' =>
                        'at_risk',

                    'disposition_status' =>
                        'pending',

                    'disposition_requested_by' =>
                        $requester->email,

                    'disposition_requested_at' =>
                        now(),
                ]
            );

            $response = $this
                ->actingAs($actor)
                ->post(
                    "/retention/{$retention->id}/disposition/reject",
                    [
                        'decision_notes' =>
                            'Rejected during RBAC test.',
                    ]
                );

            if (
                in_array(
                    $role,
                    $allowed,
                    true
                )
            ) {
                $response->assertRedirect(
                    route('retention.index')
                );

                $retention->refresh();

                $this->assertSame(
                    'rejected',
                    $retention
                        ->disposition_status
                );

                $this->assertSame(
                    'review_required',
                    $retention->status
                );

                $this->assertSame(
                    'at_risk',
                    $retention
                        ->compliance_status
                );
            } else {
                $response->assertForbidden();

                $this->assertSame(
                    'pending',
                    $retention
                        ->refresh()
                        ->disposition_status
                );
            }
        }
    }

    public function test_disposal_requester_cannot_approve_own_request(): void
    {
        /*
         * Sys Admin has both manageRetention and
         * approveRetentionDisposal, making it the
         * correct role for testing the self-approval
         * prohibition.
         */
        $admin = $this->user(
            User::ROLE_SYS_ADMIN
        );

        $retention = $this->retention(
            'Self Approval Protection',
            [
                'status' =>
                    'marked_for_disposal',

                'disposition_status' =>
                    'pending',

                'disposition_requested_by' =>
                    $admin->email,

                'disposition_requested_at' =>
                    now(),
            ]
        );

        $this->actingAs($admin)
            ->postJson(
                "/retention/{$retention->id}/disposition/approve",
                [
                    'decision_notes' =>
                        'Attempting self approval.',
                ]
            )
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'disposition'
            );

        $this->assertSame(
            'pending',
            $retention
                ->refresh()
                ->disposition_status
        );
    }

    public function test_disposal_requester_cannot_reject_own_request(): void
    {
        $admin = $this->user(
            User::ROLE_SYS_ADMIN
        );

        $retention = $this->retention(
            'Self Rejection Protection',
            [
                'status' =>
                    'marked_for_disposal',

                'disposition_status' =>
                    'pending',

                'disposition_requested_by' =>
                    $admin->email,

                'disposition_requested_at' =>
                    now(),
            ]
        );

        $this->actingAs($admin)
            ->postJson(
                "/retention/{$retention->id}/disposition/reject",
                [
                    'decision_notes' =>
                        'Attempting self rejection.',
                ]
            )
            ->assertStatus(422)
            ->assertJsonValidationErrors(
                'disposition'
            );

        $this->assertSame(
            'pending',
            $retention
                ->refresh()
                ->disposition_status
        );
    }

    public function test_disposal_approval_does_not_delete_linked_document(): void
    {
        $manager = $this->user(
            User::ROLE_MANAGER
        );

        $requester = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $document = $this->document(
            'Document Pending Disposal',
            [
                'status' => 'needs_review',
                'file_uri' =>
                    'archive/test-document.pdf',
                'file_name' =>
                    'test-document.pdf',
            ]
        );

        $retention = $this->retention(
            'Document Pending Disposal',
            [
                'record_id' =>
                    $document->id,

                'status' =>
                    'marked_for_disposal',

                'disposition_status' =>
                    'pending',

                'disposition_requested_by' =>
                    $requester->email,

                'disposition_requested_at' =>
                    now(),
            ]
        );

        $this->actingAs($manager)
            ->post(
                "/retention/{$retention->id}/disposition/approve",
                [
                    'decision_notes' =>
                        'Approved without permanent deletion.',
                ]
            )
            ->assertRedirect(
                route('retention.index')
            );

        $retention->refresh();
        $document->refresh();

        $this->assertSame(
            'approved',
            $retention->disposition_status
        );

        $this->assertSame(
            'needs_review',
            $document->status
        );

        $this->assertSame(
            'archive/test-document.pdf',
            $document->file_uri
        );

        $this->assertDatabaseHas(
            'archive_documents',
            [
                'id' => $document->id,
            ]
        );
    }

    public function test_disposal_rejection_returns_linked_document_for_review(): void
    {
        $manager = $this->user(
            User::ROLE_MANAGER
        );

        $requester = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $document = $this->document(
            'Rejected Disposal Document',
            [
                'status' =>
                    'needs_review',
            ]
        );

        $retention = $this->retention(
            'Rejected Disposal Document',
            [
                'record_id' =>
                    $document->id,

                'status' =>
                    'marked_for_disposal',

                'compliance_status' =>
                    'at_risk',

                'disposition_status' =>
                    'pending',

                'disposition_requested_by' =>
                    $requester->email,

                'disposition_requested_at' =>
                    now(),
            ]
        );

        $this->actingAs($manager)
            ->post(
                "/retention/{$retention->id}/disposition/reject",
                [
                    'decision_notes' =>
                        'Retention period needs another review.',
                ]
            )
            ->assertRedirect(
                route('retention.index')
            );

        $retention->refresh();
        $document->refresh();

        $this->assertSame(
            'rejected',
            $retention->disposition_status
        );

        $this->assertSame(
            'review_required',
            $retention->status
        );

        $this->assertSame(
            'at_risk',
            $retention->compliance_status
        );

        $this->assertSame(
            'needs_review',
            $document->status
        );
    }
}