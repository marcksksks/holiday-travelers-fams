<?php

namespace Tests\Feature;

use App\Models\RecordRetention;
use App\Models\RetentionPolicy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetentionWorkflowTest extends TestCase
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

    private function retention(
        string $title,
        array $attributes = []
    ): RecordRetention {
        return RecordRetention::create(
            array_merge(
                [
                    'record_title' => $title,
                    'record_type' => 'document',
                    'status' => 'review_required',
                    'compliance_status' => 'at_risk',
                    'start_date' =>
                        today()
                            ->subYear()
                            ->toDateString(),
                    'review_date' =>
                        today()
                            ->subDay()
                            ->toDateString(),
                ],
                $attributes
            )
        );
    }

    private function policy(
        string $name,
        array $attributes = []
    ): RetentionPolicy {
        return RetentionPolicy::create(
            array_merge(
                [
                    'name' => $name,
                    'record_category' => 'administrative',
                    'retention_years' => 5,
                    'description' => 'Retention workflow test policy.',
                    'legal_basis' => 'Internal records policy.',
                    'is_active' => true,
                ],
                $attributes
            )
        );
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

    public function test_retain_decision_updates_retention_and_creates_audit_entry(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $retention = $this->retention(
            'Corporate Records Retention Review'
        );

        $reviewDate =
            today()
                ->addDays(90)
                ->toDateString();

        $response = $this
            ->actingAs($actor)
            ->post(
                route(
                    'retention.decision',
                    $retention
                ),
                [
                    'decision' => 'retain',
                    'review_date' => $reviewDate,
                    'compliance_status' => 'compliant',
                    'notes' =>
                        'Record remains operationally required.',
                ]
            );

        $response->assertRedirect(
            route('retention.index')
        );

        $retention->refresh();

        $this->assertSame(
            'retained',
            $retention->status
        );

        $this->assertSame(
            'compliant',
            $retention->compliance_status
        );

        $this->assertSame(
            $reviewDate,
            $retention->review_date->toDateString()
        );

        $this->assertSame(
            $actor->email,
            $retention->last_action_by
        );

        $this->assertSame(
            'Record remains operationally required.',
            $retention->notes
        );

        $this->assertNotNull(
            $retention->last_action_at
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $actor->email,
                'action' => 'retention_decision',
                'module' => 'retention',
                'record_id' => $retention->id,
            ]
        );
    }

    public function test_extend_decision_sets_extended_lifecycle_and_future_review_date(): void
    {
        $actor = $this->user(
            User::ROLE_SYS_ADMIN
        );

        $retention = $this->retention(
            'Extended Contract Retention'
        );

        $reviewDate =
            today()
                ->addYear()
                ->toDateString();

        $this->actingAs($actor)
            ->post(
                route(
                    'retention.decision',
                    $retention
                ),
                [
                    'decision' => 'extend',
                    'review_date' => $reviewDate,
                    'compliance_status' => 'at_risk',
                    'notes' =>
                        'Retention extended pending contract closure.',
                ]
            )
            ->assertRedirect(
                route('retention.index')
            );

        $retention->refresh();

        $this->assertSame(
            'extended',
            $retention->status
        );

        $this->assertSame(
            $reviewDate,
            $retention->review_date->toDateString()
        );

        $this->assertSame(
            'at_risk',
            $retention->compliance_status
        );

        $this->assertSame(
            $actor->email,
            $retention->last_action_by
        );
    }

    public function test_retain_and_extend_require_a_future_review_date(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        foreach (
            ['retain', 'extend']
            as $decision
        ) {
            $missingDateRetention =
                $this->retention(
                    "Missing Date {$decision}"
                );

            $reviewUrl = route(
                'retention.review',
                $missingDateRetention
            );

            $this
                ->actingAs($actor)
                ->from($reviewUrl)
                ->post(
                    route(
                        'retention.decision',
                        $missingDateRetention
                    ),
                    [
                        'decision' => $decision,
                        'compliance_status' => 'compliant',
                        'notes' =>
                            'Testing required review date.',
                    ]
                )
                ->assertRedirect($reviewUrl)
                ->assertSessionHasErrors(
                    'review_date'
                );

            $this->assertSame(
                'review_required',
                $missingDateRetention
                    ->refresh()
                    ->status
            );


            $pastDateRetention =
                $this->retention(
                    "Past Date {$decision}"
                );

            $pastReviewUrl = route(
                'retention.review',
                $pastDateRetention
            );

            $this
                ->actingAs($actor)
                ->from($pastReviewUrl)
                ->post(
                    route(
                        'retention.decision',
                        $pastDateRetention
                    ),
                    [
                        'decision' => $decision,
                        'review_date' =>
                            today()
                                ->subDay()
                                ->toDateString(),
                        'compliance_status' =>
                            'compliant',
                        'notes' =>
                            'Testing non-future review date.',
                    ]
                )
                ->assertRedirect(
                    $pastReviewUrl
                )
                ->assertSessionHasErrors(
                    'review_date'
                );

            $this->assertSame(
                'review_required',
                $pastDateRetention
                    ->refresh()
                    ->status
            );
        }
    }

    public function test_archive_decision_changes_lifecycle_without_creating_disposal_request(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $existingReviewDate =
            today()
                ->addDays(10)
                ->toDateString();

        $retention = $this->retention(
            'Archive Eligible Record',
            [
                'review_date' =>
                    $existingReviewDate,
            ]
        );

        $this->actingAs($actor)
            ->post(
                route(
                    'retention.decision',
                    $retention
                ),
                [
                    'decision' => 'archive',
                    'compliance_status' => 'compliant',
                    'notes' =>
                        'Record lifecycle is complete.',
                ]
            )
            ->assertRedirect(
                route('retention.index')
            );

        $retention->refresh();

        $this->assertSame(
            'archived',
            $retention->status
        );

        $this->assertSame(
            $existingReviewDate,
            $retention->review_date->toDateString()
        );

        $this->assertNull(
            $retention->disposition_status
        );

        $this->assertNull(
            $retention->disposition_requested_by
        );

        $this->assertNull(
            $retention->disposition_requested_at
        );
    }

    public function test_mark_for_disposal_creates_pending_controlled_disposition_request(): void
    {
        /*
         * Provide an authorized approver so the
         * notification workflow has a valid recipient.
         */
        $this->user(
            User::ROLE_MANAGER
        );

        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $retention = $this->retention(
            'Expired Administrative File'
        );

        $this->actingAs($actor)
            ->post(
                route(
                    'retention.decision',
                    $retention
                ),
                [
                    'decision' =>
                        'mark_for_disposal',

                    'compliance_status' =>
                        'at_risk',

                    'notes' =>
                        'Retention period expired and no legal hold remains.',
                ]
            )
            ->assertRedirect(
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
            $retention->disposition_requested_by
        );

        $this->assertNotNull(
            $retention->disposition_requested_at
        );

        $this->assertSame(
            'Retention period expired and no legal hold remains.',
            $retention->disposition_reason
        );

        $this->assertNull(
            $retention->disposition_decided_by
        );

        $this->assertNull(
            $retention->disposition_decided_at
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'actor_email' => $actor->email,
                'action' => 'retention_decision',
                'module' => 'retention',
                'record_id' => $retention->id,
            ]
        );
    }

    public function test_policy_creation_redirects_back_to_policies_workspace(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $response = $this
            ->actingAs($actor)
            ->post(
                route(
                    'retention-policies.store'
                ),
                [
                    'name' =>
                        'Operational Records Policy',

                    'record_category' =>
                        'operational',

                    'retention_years' =>
                        4,

                    'description' =>
                        'Operational records retention policy.',

                    'legal_basis' =>
                        'Internal operational policy.',

                    'is_active' =>
                        true,
                ]
            );

        $response->assertRedirect(
            route(
                'retention.index',
                [
                    'tab' => 'policies',
                ]
            )
        );

        $this->assertDatabaseHas(
            'retention_policies',
            [
                'name' =>
                    'Operational Records Policy',

                'record_category' =>
                    'operational',

                'retention_years' =>
                    4,

                'is_active' =>
                    true,
            ]
        );
    }

    public function test_policy_update_matches_retention_management_role_matrix(): void
    {
        $allowed = [
            User::ROLE_ADMIN_OFFICER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach (
            $this->roles()
            as $role
        ) {
            $actor = $this->user($role);

            $policy = $this->policy(
                "Update Matrix {$role}"
            );

            $response = $this
                ->actingAs($actor)
                ->put(
                    route(
                        'retention-policies.update',
                        $policy
                    ),
                    [
                        'name' =>
                            "Updated Policy {$role}",

                        'record_category' =>
                            'contract',

                        'retention_years' =>
                            7,

                        'description' =>
                            'Updated during workflow security test.',

                        'legal_basis' =>
                            'Updated legal basis.',

                        'is_active' =>
                            false,
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
                    route(
                        'retention.index',
                        [
                            'tab' =>
                                'policies',
                        ]
                    )
                );

                $policy->refresh();

                $this->assertSame(
                    "Updated Policy {$role}",
                    $policy->name
                );

                $this->assertSame(
                    'contract',
                    $policy->record_category
                );

                $this->assertSame(
                    7,
                    $policy->retention_years
                );

                $this->assertFalse(
                    $policy->is_active
                );
            } else {
                $response->assertForbidden();

                $policy->refresh();

                $this->assertSame(
                    "Update Matrix {$role}",
                    $policy->name
                );

                $this->assertSame(
                    'administrative',
                    $policy->record_category
                );

                $this->assertSame(
                    5,
                    $policy->retention_years
                );

                $this->assertTrue(
                    $policy->is_active
                );
            }
        }
    }

    public function test_policy_update_rejects_retention_period_above_100_years(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        $policy = $this->policy(
            'Retention Period Validation Policy'
        );

        $policiesUrl = route(
            'retention.index',
            [
                'tab' => 'policies',
            ]
        );

        $this
            ->actingAs($actor)
            ->from($policiesUrl)
            ->put(
                route(
                    'retention-policies.update',
                    $policy
                ),
                [
                    'name' =>
                        $policy->name,

                    'record_category' =>
                        $policy->record_category,

                    'retention_years' =>
                        101,

                    'description' =>
                        $policy->description,

                    'legal_basis' =>
                        $policy->legal_basis,

                    'is_active' =>
                        true,
                ]
            )
            ->assertRedirect(
                $policiesUrl
            )
            ->assertSessionHasErrors(
                'retention_years'
            );

        $policy->refresh();

        $this->assertSame(
            5,
            $policy->retention_years
        );
    }

    public function test_pending_disposal_request_cannot_be_overridden_by_another_retention_decision(): void
    {
        $actor = $this->user(
            User::ROLE_ADMIN_OFFICER
        );

        foreach (
            ['retain', 'extend', 'archive']
            as $decision
        ) {
            $retention = $this->retention(
                "Pending Disposal {$decision}",
                [
                    'status' =>
                        'marked_for_disposal',

                    'disposition_status' =>
                        'pending',

                    'disposition_requested_by' =>
                        'requester@example.test',

                    'disposition_requested_at' =>
                        now(),

                    'disposition_reason' =>
                        'Pending authorized disposition review.',
                ]
            );

            $originalReviewDate =
                $retention
                    ->review_date
                    ->toDateString();

            $this
                ->actingAs($actor)
                ->postJson(
                    route(
                        'retention.decision',
                        $retention
                    ),
                    [
                        'decision' =>
                            $decision,

                        'review_date' =>
                            today()
                                ->addYear()
                                ->toDateString(),

                        'compliance_status' =>
                            'compliant',

                        'notes' =>
                            'Attempting to override pending disposal.',
                    ]
                )
                ->assertStatus(422)
                ->assertJsonValidationErrors(
                    'disposition'
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
                'requester@example.test',
                $retention->disposition_requested_by
            );

            $this->assertSame(
                'Pending authorized disposition review.',
                $retention->disposition_reason
            );

            $this->assertSame(
                $originalReviewDate,
                $retention
                    ->review_date
                    ->toDateString()
            );

            $this->assertSame(
                'at_risk',
                $retention->compliance_status
            );
        }
    }
}