<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePrivilegedMfa;
use App\Models\PrivacyRequest;
use App\Models\User;
use App\Services\PrivacyService;
use App\Support\Rbac;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(
            RequirePrivilegedMfa::class
        );
    }

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

    private function privacyRequest(
        User $subject,
        string $type =
            PrivacyRequest::TYPE_ERASURE
    ): PrivacyRequest {
        return app(
            PrivacyService::class
        )->submitRequest(
            $subject,
            $type,
            'Please review the personal data associated with this account.',
            true
        );
    }

    public function test_manage_privacy_permission_is_limited_to_manager_and_system_admin(): void
    {
        $allowed = [
            User::ROLE_MANAGER,
            User::ROLE_SYS_ADMIN,
        ];

        foreach (
            array_keys(
                User::ROLES
            ) as $role
        ) {
            $this->assertSame(
                in_array(
                    $role,
                    $allowed,
                    true
                ),
                Rbac::can(
                    'managePrivacy',
                    $role
                )
            );
        }
    }

    public function test_privacy_workspace_access_matches_reviewer_roles(): void
    {
        foreach (
            array_keys(
                User::ROLES
            ) as $role
        ) {
            $user =
                $this->user($role);

            $response =
                $this
                    ->actingAs($user)
                    ->get(
                        route(
                            'privacy-requests.index'
                        )
                    );

            if (
                in_array(
                    $role,
                    [
                        User::ROLE_MANAGER,
                        User::ROLE_SYS_ADMIN,
                    ],
                    true
                )
            ) {
                $response
                    ->assertOk()
                    ->assertSee(
                        'Privacy Request Reviews'
                    );
            } else {
                $response
                    ->assertForbidden();
            }
        }
    }

    public function test_manager_can_start_verified_request_review(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $privacyRequest =
            $this->privacyRequest(
                $subject
            );

        $this
            ->actingAs($reviewer)
            ->post(
                route(
                    'privacy-requests.start-review',
                    $privacyRequest
                )
            )
            ->assertRedirect(
                route(
                    'privacy-requests.index'
                )
            );

        $fresh =
            $privacyRequest->fresh();

        $this->assertSame(
            PrivacyRequest::STATUS_UNDER_REVIEW,
            $fresh->status
        );

        $this->assertSame(
            $reviewer->id,
            $fresh->reviewed_by_user_id
        );

        $this->assertNull(
            $fresh->reviewed_at
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'module' => 'privacy',

                'action' => 'privacy_review_started',

                'record_id' => (string) $privacyRequest->id,
            ]
        );
    }

    public function test_reviewer_cannot_review_own_request(): void
    {
        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $privacyRequest =
            $this->privacyRequest(
                $reviewer
            );

        $this
            ->actingAs($reviewer)
            ->from(
                route(
                    'privacy-requests.index'
                )
            )
            ->post(
                route(
                    'privacy-requests.start-review',
                    $privacyRequest
                )
            )
            ->assertRedirect(
                route(
                    'privacy-requests.index'
                )
            )
            ->assertSessionHasErrors(
                'privacy_request'
            );

        $this->assertSame(
            PrivacyRequest::STATUS_PENDING,
            $privacyRequest
                ->fresh()
                ->status
        );
    }

    public function test_approved_request_is_not_marked_completed(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $privacyRequest =
            $this->privacyRequest(
                $subject
            );

        app(
            PrivacyService::class
        )->startReview(
            $privacyRequest,
            $reviewer
        );

        $this
            ->actingAs($reviewer)
            ->post(
                route(
                    'privacy-requests.decision',
                    $privacyRequest
                ),
                [
                    'decision' => PrivacyRequest::STATUS_APPROVED,

                    'decision_reason' => 'The verified request may proceed to controlled execution.',
                ]
            )
            ->assertRedirect(
                route(
                    'privacy-requests.index'
                )
            );

        $fresh =
            $privacyRequest->fresh();

        $this->assertSame(
            PrivacyRequest::STATUS_APPROVED,
            $fresh->status
        );

        $this->assertNotNull(
            $fresh->reviewed_at
        );

        $this->assertNull(
            $fresh->completed_at
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'module' => 'privacy',

                'action' => 'privacy_decision',

                'record_id' => (string) $privacyRequest->id,
            ]
        );
    }

    public function test_partial_approval_requires_retention_basis(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $privacyRequest =
            $this->privacyRequest(
                $subject
            );

        app(
            PrivacyService::class
        )->startReview(
            $privacyRequest,
            $reviewer
        );

        $this
            ->actingAs($reviewer)
            ->from(
                route(
                    'privacy-requests.index'
                )
            )
            ->post(
                route(
                    'privacy-requests.decision',
                    $privacyRequest
                ),
                [
                    'decision' => PrivacyRequest::STATUS_PARTIALLY_APPROVED,

                    'decision_reason' => 'Some information may proceed to controlled execution.',
                ]
            )
            ->assertRedirect(
                route(
                    'privacy-requests.index'
                )
            )
            ->assertSessionHasErrors(
                'retention_basis'
            );

        $this->assertSame(
            PrivacyRequest::STATUS_UNDER_REVIEW,
            $privacyRequest
                ->fresh()
                ->status
        );
    }

    public function test_denial_records_retention_basis(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $privacyRequest =
            $this->privacyRequest(
                $subject,
                PrivacyRequest::TYPE_BLOCKING
            );

        app(
            PrivacyService::class
        )->startReview(
            $privacyRequest,
            $reviewer
        );

        $this
            ->actingAs($reviewer)
            ->post(
                route(
                    'privacy-requests.decision',
                    $privacyRequest
                ),
                [
                    'decision' => PrivacyRequest::STATUS_DENIED,

                    'decision_reason' => 'The requested restriction cannot be fully applied.',

                    'retention_basis' => 'The record remains subject to an existing records-retention requirement.',
                ]
            )
            ->assertRedirect(
                route(
                    'privacy-requests.index'
                )
            );

        $fresh =
            $privacyRequest->fresh();

        $this->assertSame(
            PrivacyRequest::STATUS_DENIED,
            $fresh->status
        );

        $this->assertSame(
            'The record remains subject to an existing records-retention requirement.',
            $fresh->retention_basis
        );
    }

    public function test_pending_request_cannot_skip_directly_to_decision(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $privacyRequest =
            $this->privacyRequest(
                $subject
            );

        $this
            ->actingAs($reviewer)
            ->from(
                route(
                    'privacy-requests.index'
                )
            )
            ->post(
                route(
                    'privacy-requests.decision',
                    $privacyRequest
                ),
                [
                    'decision' => PrivacyRequest::STATUS_APPROVED,

                    'decision_reason' => 'Attempted direct approval.',
                ]
            )
            ->assertRedirect(
                route(
                    'privacy-requests.index'
                )
            )
            ->assertSessionHasErrors(
                'privacy_request'
            );

        $this->assertSame(
            PrivacyRequest::STATUS_PENDING,
            $privacyRequest
                ->fresh()
                ->status
        );
    }
}
