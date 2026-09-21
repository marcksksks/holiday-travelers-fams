<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\AuditLog;
use App\Models\DocumentContainer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class DocumentWorkflowTest extends TestCase
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
                    'history' => [],
                ],
                $extra
            )
        );
    }

    private function container(
        string $name,
        string $path,
        ?string $module = null
    ): DocumentContainer {
        return DocumentContainer::create([
            'name' => $name,
            'slug' => strtolower(
                str_replace(' ', '-', $name)
            ),
            'path' => $path,
            'module' => $module,
            'is_system' => false,
        ]);
    }

    public function test_document_move_updates_container_history_and_audit(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $source = $this->container(
            'Move Source',
            'administrative/move-source'
        );

        $target = $this->container(
            'Move Target',
            'administrative/move-target'
        );

        $document = $this->document(
            'Move Workflow Document',
            [
                'container_id' => $source->id,
            ]
        );

        $response = $this
            ->actingAs($actor)
            ->post(
                route(
                    'documents.move',
                    $document
                ),
                [
                    'container_id' => $target->id,
                ]
            );

        $response
            ->assertRedirect(
                route(
                    'documents.show',
                    $document
                )
            )
            ->assertSessionHas(
                'status',
                'Document moved successfully.'
            );

        $document->refresh();

        $this->assertSame(
            $target->id,
            $document->container_id
        );

        $history = $document->history;

        $this->assertNotEmpty($history);

        $entry = $history[
            array_key_last($history)
        ];

        $this->assertSame(
            'move',
            $entry['action']
        );

        $this->assertSame(
            $actor->email,
            $entry['by']
        );

        $this->assertSame(
            'Moved from Move Source to Move Target.',
            $entry['note']
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $actor->email,
                'actor_role' => $actor->app_role,
                'action' => 'move',
                'module' => 'documents',
                'record_label' => 'Document - Move Workflow Document',
                'details' => 'Move Source -> Move Target',
            ]
        );
    }

    public function test_user_without_document_management_permission_cannot_move_document(): void
    {
        $actor = $this->user(
            User::ROLE_MANAGER
        );

        $source = $this->container(
            'Restricted Source',
            'administrative/restricted-source'
        );

        $target = $this->container(
            'Restricted Target',
            'administrative/restricted-target'
        );

        $document = $this->document(
            'Unauthorized Move Document',
            [
                'container_id' => $source->id,
            ]
        );

        $this
            ->actingAs($actor)
            ->post(
                route(
                    'documents.move',
                    $document
                ),
                [
                    'container_id' => $target->id,
                ]
            )
            ->assertForbidden();

        $this->assertSame(
            $source->id,
            $document
                ->refresh()
                ->container_id
        );

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'action' => 'move',
                'module' => 'documents',
                'record_label' => 'Document - Unauthorized Move Document',
            ]
        );
    }

    public function test_system_generated_document_cannot_be_moved_manually(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $source = $this->container(
            'System Source',
            'administrative/system-source'
        );

        $target = $this->container(
            'System Target',
            'administrative/system-target'
        );

        $document = $this->document(
            'System Generated Move Document',
            [
                'container_id' => $source->id,
                'is_system_generated' => true,
            ]
        );

        $this
            ->actingAs($actor)
            ->post(
                route(
                    'documents.move',
                    $document
                ),
                [
                    'container_id' => $target->id,
                ]
            )
            ->assertForbidden();

        $this->assertSame(
            $source->id,
            $document
                ->refresh()
                ->container_id
        );

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'action' => 'move',
                'module' => 'documents',
                'record_label' => 'Document - System Generated Move Document',
            ]
        );
    }

    public function test_document_move_enforces_destination_container_permission(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        Gate::define(
            'viewVisitors',
            fn (User $user): bool => false
        );

        $this->assertTrue(
            $actor->can('manageDocuments')
        );

        $this->assertFalse(
            $actor->can('viewVisitors')
        );

        $source = $this->container(
            'Administrative Source',
            'administrative/permission-source'
        );

        $protectedTarget = $this->container(
            'Protected Visitor Folder',
            'visitors/protected',
            'visitors'
        );

        $document = $this->document(
            'Protected Destination Document',
            [
                'container_id' => $source->id,
            ]
        );

        $this
            ->actingAs($actor)
            ->post(
                route(
                    'documents.move',
                    $document
                ),
                [
                    'container_id' => $protectedTarget->id,
                ]
            )
            ->assertForbidden();

        $this->assertSame(
            $source->id,
            $document
                ->refresh()
                ->container_id
        );

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'action' => 'move',
                'module' => 'documents',
                'record_label' => 'Document - Protected Destination Document',
            ]
        );
    }

    public function test_bulk_archive_updates_applicable_documents_and_skips_archived_documents(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $first = $this->document(
            'Bulk Archive First'
        );

        $second = $this->document(
            'Bulk Archive Second',
            [
                'status' => 'needs_review',
            ]
        );

        $alreadyArchived = $this->document(
            'Bulk Archive Already Archived',
            [
                'status' => 'archived',
            ]
        );

        $response = $this
            ->actingAs($actor)
            ->from(
                route('documents.index')
            )
            ->post(
                route(
                    'documents.bulk-action'
                ),
                [
                    'action' => 'archive',
                    'document_ids' => [
                        $first->id,
                        $second->id,
                        $alreadyArchived->id,
                    ],
                ]
            );

        $response
            ->assertRedirect(
                route('documents.index')
            )
            ->assertSessionHas(
                'status',
                '2 documents updated. 1 skipped because the selected action was not applicable.'
            );

        $this->assertSame(
            'archived',
            $first->refresh()->status
        );

        $this->assertSame(
            'archived',
            $second->refresh()->status
        );

        $this->assertSame(
            'archived',
            $alreadyArchived
                ->refresh()
                ->status
        );

        foreach (
            [$first, $second] as $document
        ) {
            $history =
                $document->history;

            $entry = $history[
                array_key_last($history)
            ];

            $this->assertSame(
                'archive',
                $entry['action']
            );

            $this->assertSame(
                $actor->email,
                $entry['by']
            );
        }

        $this->assertSame(
            [],
            $alreadyArchived->history
        );

        $this->assertSame(
            2,
            AuditLog::query()
                ->where(
                    'module',
                    'documents'
                )
                ->where(
                    'action',
                    'archive'
                )
                ->whereIn(
                    'record_label',
                    [
                        'Document - Bulk Archive First',
                        'Document - Bulk Archive Second',
                    ]
                )
                ->count()
        );

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'action' => 'archive',
                'module' => 'documents',
                'record_label' => 'Document - Bulk Archive Already Archived',
            ]
        );
    }

    public function test_bulk_restore_updates_archived_documents_and_skips_active_documents(): void
    {
        $actor = $this->user(
            User::ROLE_SYS_ADMIN
        );

        $first = $this->document(
            'Bulk Restore First',
            [
                'status' => 'archived',
            ]
        );

        $second = $this->document(
            'Bulk Restore Second',
            [
                'status' => 'archived',
            ]
        );

        $alreadyActive = $this->document(
            'Bulk Restore Already Active'
        );

        $response = $this
            ->actingAs($actor)
            ->from(
                route('documents.index')
            )
            ->post(
                route(
                    'documents.bulk-action'
                ),
                [
                    'action' => 'restore',
                    'document_ids' => [
                        $first->id,
                        $second->id,
                        $alreadyActive->id,
                    ],
                ]
            );

        $response
            ->assertRedirect(
                route('documents.index')
            )
            ->assertSessionHas(
                'status',
                '2 documents updated. 1 skipped because the selected action was not applicable.'
            );

        $this->assertSame(
            'active',
            $first->refresh()->status
        );

        $this->assertSame(
            'active',
            $second->refresh()->status
        );

        $this->assertSame(
            'active',
            $alreadyActive
                ->refresh()
                ->status
        );

        foreach (
            [$first, $second] as $document
        ) {
            $history =
                $document->history;

            $entry = $history[
                array_key_last($history)
            ];

            $this->assertSame(
                'restore',
                $entry['action']
            );

            $this->assertSame(
                $actor->email,
                $entry['by']
            );
        }

        $this->assertSame(
            [],
            $alreadyActive->history
        );

        $this->assertSame(
            2,
            AuditLog::query()
                ->where(
                    'module',
                    'documents'
                )
                ->where(
                    'action',
                    'restore'
                )
                ->whereIn(
                    'record_label',
                    [
                        'Document - Bulk Restore First',
                        'Document - Bulk Restore Second',
                    ]
                )
                ->count()
        );

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'action' => 'restore',
                'module' => 'documents',
                'record_label' => 'Document - Bulk Restore Already Active',
            ]
        );
    }

    public function test_bulk_action_rejects_removed_legacy_actions(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $document = $this->document(
            'Legacy Bulk Action Document'
        );

        foreach (
            [
                'move',
                'needs_review',
                'active',
            ] as $action
        ) {
            $this
                ->actingAs($actor)
                ->postJson(
                    route(
                        'documents.bulk-action'
                    ),
                    [
                        'action' => $action,
                        'document_ids' => [
                            $document->id,
                        ],
                    ]
                )
                ->assertUnprocessable()
                ->assertJsonValidationErrors(
                    'action'
                );

            $this->assertSame(
                'active',
                $document
                    ->refresh()
                    ->status
            );
        }

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'module' => 'documents',
                'record_label' => 'Document - Legacy Bulk Action Document',
            ]
        );
    }

    public function test_bulk_action_rejects_selection_containing_inaccessible_document(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        Gate::define(
            'viewVisitors',
            fn (User $user): bool => false
        );

        $this->assertTrue(
            $actor->can('manageDocuments')
        );

        $this->assertFalse(
            $actor->can('viewVisitors')
        );

        $accessible = $this->document(
            'Accessible Bulk Document'
        );

        $inaccessible = $this->document(
            'Visitor Bulk Document',
            [
                'source_module' => 'visitors',
            ]
        );

        $this
            ->actingAs($actor)
            ->post(
                route(
                    'documents.bulk-action'
                ),
                [
                    'action' => 'archive',
                    'document_ids' => [
                        $accessible->id,
                        $inaccessible->id,
                    ],
                ]
            )
            ->assertForbidden();

        $this->assertSame(
            'active',
            $accessible
                ->refresh()
                ->status
        );

        $this->assertSame(
            'active',
            $inaccessible
                ->refresh()
                ->status
        );

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'module' => 'documents',
                'record_label' => 'Document - Accessible Bulk Document',
            ]
        );

        $this->assertDatabaseMissing(
            'audit_logs',
            [
                'module' => 'documents',
                'record_label' => 'Document - Visitor Bulk Document',
            ]
        );
    }
}
