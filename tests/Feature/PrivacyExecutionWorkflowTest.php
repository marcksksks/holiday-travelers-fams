<?php

namespace Tests\Feature;

use App\Http\Middleware\RequirePrivilegedMfa;
use App\Models\AuditLog;
use App\Models\PrivacyRequest;
use App\Models\User;
use App\Services\PrivacyService;
use App\Support\Rbac;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PrivacyExecutionWorkflowTest extends TestCase
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
        string $role,
        array $attributes = []
    ): User {
        return User::factory()
            ->role($role)
            ->create(
                array_merge(
                    [
                        'is_active' => true,
                        'force_password_change' => false,
                    ],
                    $attributes
                )
            );
    }

    private function approvedRequest(
        User $subject,
        User $reviewer,
        string $type
    ): PrivacyRequest {
        $privacy =
            app(
                PrivacyService::class
            );

        $request =
            $privacy->submitRequest(
                $subject,
                $type,
                'Verified privacy execution request.',
                true
            );

        $privacy->startReview(
            $request,
            $reviewer
        );

        return $privacy->decideRequest(
            $request,
            $reviewer,
            PrivacyRequest::STATUS_APPROVED,
            'Approved for controlled execution.'
        );
    }

    public function test_execute_privacy_permission_is_system_admin_only(): void
    {
        foreach (
            array_keys(
                User::ROLES
            ) as $role
        ) {
            $this->assertSame(
                $role === User::ROLE_SYS_ADMIN,
                Rbac::can(
                    'executePrivacy',
                    $role
                )
            );
        }
    }

    public function test_pending_request_cannot_be_executed(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $executor =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $request =
            app(
                PrivacyService::class
            )->submitRequest(
                $subject,
                PrivacyRequest::TYPE_BLOCKING,
                null,
                true
            );

        $this->expectException(
            ValidationException::class
        );

        app(
            PrivacyService::class
        )->executeRequest(
            $request,
            $executor
        );
    }

    public function test_reviewer_and_executor_must_be_different_users(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $request =
            $this->approvedRequest(
                $subject,
                $reviewer,
                PrivacyRequest::TYPE_BLOCKING
            );

        try {
            app(
                PrivacyService::class
            )->executeRequest(
                $request,
                $reviewer
            );

            $this->fail(
                'Expected separation-of-duties validation failure.'
            );
        } catch (
            ValidationException $exception
        ) {
            $this->assertArrayHasKey(
                'privacy_request',
                $exception->errors()
            );
        }

        $this->assertSame(
            PrivacyRequest::STATUS_APPROVED,
            $request->fresh()->status
        );
    }

    public function test_blocking_restricts_account_and_revokes_credentials(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE,
                [
                    'email' => 'blocking.subject@example.com',
                ]
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $executor =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        DB::table(
            'sessions'
        )->insert([
            'id' => 'privacy-block-session',

            'user_id' => $subject->id,

            'ip_address' => '127.0.0.1',

            'user_agent' => 'Privacy Test',

            'payload' => 'test',

            'last_activity' => now()->timestamp,
        ]);

        $subject
            ->createToken(
                'privacy-test'
            );

        DB::table(
            'password_reset_tokens'
        )->insert([
            'email' => $subject->email,

            'token' => 'hashed-test-token',

            'created_at' => now(),
        ]);

        $request =
            $this->approvedRequest(
                $subject,
                $reviewer,
                PrivacyRequest::TYPE_BLOCKING
            );

        $completed =
            app(
                PrivacyService::class
            )->executeRequest(
                $request,
                $executor
            );

        $freshSubject =
            $subject->fresh();

        $this->assertFalse(
            $freshSubject->is_active
        );

        $this->assertNotNull(
            $freshSubject
                ->privacy_processing_restricted_at
        );

        $this->assertNull(
            $freshSubject
                ->privacy_anonymized_at
        );

        $this->assertSame(
            PrivacyRequest::STATUS_COMPLETED,
            $completed->status
        );

        $this->assertSame(
            $executor->id,
            $completed->executed_by_user_id
        );

        $this->assertNotNull(
            $completed->completed_at
        );

        $this->assertDatabaseMissing(
            'sessions',
            [
                'user_id' => $subject->id,
            ]
        );

        $this->assertDatabaseMissing(
            'personal_access_tokens',
            [
                'tokenable_id' => $subject->id,
            ]
        );

        $this->assertDatabaseMissing(
            'password_reset_tokens',
            [
                'email' => 'blocking.subject@example.com',
            ]
        );
    }

    public function test_consent_withdrawal_marks_active_consent_without_deleting_evidence(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $executor =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $consent =
            app(
                PrivacyService::class
            )->recordForUser(
                $reviewer,
                $subject,
                'optional_updates',
                'consent',
                PrivacyService::NOTICE_VERSION,
                true,
                true,
                'settings'
            );

        $request =
            $this->approvedRequest(
                $subject,
                $reviewer,
                PrivacyRequest::TYPE_WITHDRAW_CONSENT
            );

        app(
            PrivacyService::class
        )->executeRequest(
            $request,
            $executor
        );

        $this->assertNotNull(
            $consent
                ->fresh()
                ->withdrawn_at
        );

        $this->assertDatabaseHas(
            'privacy_consents',
            [
                'id' => $consent->id,

                'granted' => true,
            ]
        );

        $this->assertSame(
            PrivacyRequest::STATUS_COMPLETED,
            $request->fresh()->status
        );
    }

    public function test_erasure_anonymizes_account_and_preserves_audit_record(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE,
                [
                    'full_name' => 'Privacy Subject',

                    'email' => 'privacy.subject@example.com',

                    'department' => 'Operations',

                    'job_title' => 'Coordinator',

                    'phone' => '09123456789',
                ]
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $executor =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $historicalAudit =
            AuditLog::create([
                'actor_email' => $subject->email,

                'actor_role' => $subject->app_role,

                'action' => 'historical_action',

                'module' => 'privacy_test',

                'record_label' => 'Action by Privacy Subject',

                'record_id' => 'TEST-1',

                'details' => 'privacy.subject@example.com completed an action.',

                'created_at' => now(),
            ]);

        DB::table(
            'sessions'
        )->insert([
            'id' => 'privacy-erasure-session',

            'user_id' => $subject->id,

            'ip_address' => '127.0.0.1',

            'user_agent' => 'Privacy Test',

            'payload' => 'test',

            'last_activity' => now()->timestamp,
        ]);

        $subject
            ->createToken(
                'privacy-erasure'
            );

        DB::table(
            'password_reset_tokens'
        )->insert([
            'email' => $subject->email,

            'token' => 'hashed-erasure-token',

            'created_at' => now(),
        ]);

        $request =
            $this->approvedRequest(
                $subject,
                $reviewer,
                PrivacyRequest::TYPE_ERASURE
            );

        $completed =
            app(
                PrivacyService::class
            )->executeRequest(
                $request,
                $executor
            );

        $freshSubject =
            $subject->fresh();

        $this->assertSame(
            "Anonymized User #{$subject->id}",
            $freshSubject->full_name
        );

        $this->assertSame(
            "anonymized-{$subject->id}@privacy.invalid",
            $freshSubject->email
        );

        $this->assertNull(
            $freshSubject->department
        );

        $this->assertNull(
            $freshSubject->job_title
        );

        $this->assertNull(
            $freshSubject->phone
        );

        $this->assertFalse(
            $freshSubject->is_active
        );

        $this->assertNotNull(
            $freshSubject
                ->privacy_processing_restricted_at
        );

        $this->assertNotNull(
            $freshSubject
                ->privacy_anonymized_at
        );

        $this->assertSame(
            PrivacyRequest::STATUS_COMPLETED,
            $completed->status
        );

        $this->assertNull(
            $completed->details
        );

        $historicalAudit =
            $historicalAudit->fresh();

        $this->assertSame(
            $freshSubject->email,
            $historicalAudit->actor_email
        );

        $this->assertStringNotContainsString(
            'Privacy Subject',
            (string) $historicalAudit->record_label
        );

        $this->assertStringNotContainsString(
            'privacy.subject@example.com',
            (string) $historicalAudit->details
        );

        $this->assertDatabaseMissing(
            'sessions',
            [
                'user_id' => $subject->id,
            ]
        );

        $this->assertDatabaseMissing(
            'personal_access_tokens',
            [
                'tokenable_id' => $subject->id,
            ]
        );

        $this->assertDatabaseMissing(
            'password_reset_tokens',
            [
                'email' => 'privacy.subject@example.com',
            ]
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'action' => 'privacy_execution',

                'record_id' => (string) $request->id,
            ]
        );
    }

    public function test_web_execution_requires_password_and_explicit_confirmation(): void
    {
        $subject =
            $this->user(
                User::ROLE_EMPLOYEE
            );

        $reviewer =
            $this->user(
                User::ROLE_MANAGER
            );

        $executor =
            $this->user(
                User::ROLE_SYS_ADMIN
            );

        $request =
            $this->approvedRequest(
                $subject,
                $reviewer,
                PrivacyRequest::TYPE_BLOCKING
            );

        $this
            ->actingAs(
                $executor
            )
            ->from(
                route(
                    'privacy-requests.index'
                )
            )
            ->post(
                route(
                    'privacy-requests.execute',
                    $request
                ),
                []
            )
            ->assertRedirect(
                route(
                    'privacy-requests.index'
                )
            )
            ->assertSessionHasErrors([
                'current_password',
                'confirm_execution',
            ]);

        $this->assertSame(
            PrivacyRequest::STATUS_APPROVED,
            $request->fresh()->status
        );
    }
}
