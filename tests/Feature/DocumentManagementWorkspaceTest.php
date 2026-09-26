<?php

namespace Tests\Feature;

use App\Models\ArchiveDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentManagementWorkspaceTest extends TestCase
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

    public function test_system_admin_sees_document_management_workspace(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('documents.index')
            )
            ->assertOk()
            ->assertDontSee('Controlled Library')
            ->assertSee(
                'Library Status'
            )
            ->assertSee(
                'Total Documents'
            )
            ->assertSee(
                'Needs Review'
            )
            ->assertSee(
                'Archived'
            )
            ->assertSee(
                'Document Library'
            )
            ->assertSee(
                'Upload Document'
            );
    }

    public function test_legal_officer_can_view_library_without_upload_action(): void
    {
        $legalOfficer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        $this
            ->actingAs($legalOfficer)
            ->get(
                route('documents.index')
            )
            ->assertOk()
            ->assertSee(
                'Library Status'
            )
            ->assertSee(
                'Document Library'
            )
            ->assertDontSee(
                'Upload Document'
            );
    }

    public function test_document_directory_contains_responsive_mobile_card(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        ArchiveDocument::create([
            'title' => 'Responsive Document Record',

            'category' => 'administrative',

            'confidentiality' => 'general',

            'status' => 'active',

            'version' => 1,

            'uploaded_by_email' => $admin->email,

            'is_system_generated' => false,
        ]);

        $this
            ->actingAs($admin)
            ->get(
                route('documents.index')
            )
            ->assertOk()
            ->assertSee(
                'Responsive Document Record'
            )
            ->assertSee(
                'data-document-mobile-card',
                false
            )
            ->assertSee(
                'View Details'
            );
    }

    public function test_existing_document_filters_remain_available(): void
    {
        $admin =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $this
            ->actingAs($admin)
            ->get(
                route('documents.index')
            )
            ->assertOk()
            ->assertSee('Search documents...')
            ->assertSee('data-document-filter-bar', false)
            ->assertSee(
                'All Categories'
            )
            ->assertSee(
                'All Statuses'
            );
    }
}
