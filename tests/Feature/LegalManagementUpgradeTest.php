<?php

namespace Tests\Feature;

use App\Models\LegalRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalManagementUpgradeTest extends TestCase
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

    public function test_legal_record_supports_management_workspace_fields(): void
    {
        $officer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        $record =
            LegalRecord::create([
                'title' => 'Regulatory Compliance Matter',

                'record_type' => 'requirement',

                'legal_category' => 'Regulatory Compliance',

                'jurisdiction' => 'Philippines',

                'legal_basis' => 'Internal compliance requirement',

                'status' => 'active',

                'priority' => 'high',

                'confidentiality_level' => 'confidential',

                'assigned_user_id' => $officer->id,

                'due_date' => now()
                    ->addDays(5)
                    ->toDateString(),

                'next_action_date' => now()
                    ->addDays(2)
                    ->toDateString(),

                'next_action' => 'Prepare compliance response.',
            ]);

        $this->assertSame(
            'high',
            $record->priority
        );

        $this->assertSame(
            'confidential',
            $record->confidentiality_level
        );

        $this->assertSame(
            $officer->id,
            $record->assignedOfficer->id
        );

        $this->assertSame(
            'due_soon',
            $record->deadlineState()
        );
    }

    public function test_expired_date_is_derived_even_when_record_status_is_active(): void
    {
        $record =
            LegalRecord::create([
                'title' => 'Expired Permit',

                'record_type' => 'permit',

                'status' => 'active',

                'priority' => 'medium',

                'confidentiality_level' => 'internal',

                'expiration_date' => now()
                    ->subDay()
                    ->toDateString(),
            ]);

        $this->assertSame(
            'active',
            $record->status
        );

        $this->assertSame(
            'expired',
            $record->deadlineState()
        );

        $this->assertSame(
            'Expired',
            $record->deadlineLabel()
        );
    }

    public function test_selected_officer_is_synchronized_to_responsible_email(): void
    {
        $admin =
            $this->user(
                User::ROLE_ADMIN_OFFICER
            );

        $officer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        $this
            ->actingAs($admin)
            ->post(
                route(
                    'legal.store'
                ),
                [
                    'title' => 'Assigned Legal Matter',

                    'record_type' => 'legal_case',

                    'status' => 'active',

                    'priority' => 'critical',

                    'confidentiality_level' => 'restricted',

                    'assigned_user_id' => $officer->id,

                    'responsible_officer_email' => 'incorrect@example.com',
                ]
            )
            ->assertRedirect(
                route(
                    'legal.index'
                )
            );

        $record =
            LegalRecord::where(
                'title',
                'Assigned Legal Matter'
            )->firstOrFail();

        $this->assertSame(
            $officer->id,
            $record->assigned_user_id
        );

        $this->assertSame(
            $officer->email,
            $record->responsible_officer_email
        );
    }

    public function test_legal_workspace_can_filter_by_priority_and_assignment(): void
    {
        $viewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $officer =
            $this->user(
                User::ROLE_LEGAL_OFFICER
            );

        LegalRecord::create([
            'title' => 'Critical Matter',

            'record_type' => 'legal_case',

            'status' => 'active',

            'priority' => 'critical',

            'confidentiality_level' => 'restricted',

            'assigned_user_id' => $officer->id,
        ]);

        LegalRecord::create([
            'title' => 'Routine Permit',

            'record_type' => 'permit',

            'status' => 'active',

            'priority' => 'low',

            'confidentiality_level' => 'internal',
        ]);

        $this
            ->actingAs($viewer)
            ->get(
                route(
                    'legal.index',
                    [
                        'priority' => 'critical',

                        'assigned_user_id' => $officer->id,
                    ]
                )
            )
            ->assertOk()
            ->assertSee(
                'Critical Matter'
            )
            ->assertDontSee(
                'Routine Permit'
            );
    }

    public function test_non_legal_assignment_role_is_rejected(): void
    {
        $admin =
            $this->user(
                User::ROLE_ADMIN_OFFICER
            );

        $employee =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $this
            ->actingAs($admin)
            ->post(
                route(
                    'legal.store'
                ),
                [
                    'title' => 'Invalid Assignment',

                    'record_type' => 'requirement',

                    'status' => 'active',

                    'priority' => 'medium',

                    'confidentiality_level' => 'internal',

                    'assigned_user_id' => $employee->id,
                ]
            )
            ->assertSessionHasErrors(
                'assigned_user_id'
            );

        $this->assertDatabaseMissing(
            'legal_records',
            [
                'title' => 'Invalid Assignment',
            ]
        );
    }
}
