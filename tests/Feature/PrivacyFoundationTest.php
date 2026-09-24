<?php

namespace Tests\Feature;

use App\Models\PrivacyRequest;
use App\Models\User;
use App\Models\Visitor;
use App\Services\PrivacyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PrivacyFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_consent_processing_can_record_notice_acknowledgement_without_false_consent(): void
    {
        $user =
            User::factory()
                ->create([
                    'force_password_change' => false,
                ]);

        $consent =
            app(
                PrivacyService::class
            )->recordForUser(
                $user,
                $user,
                'account_privacy',
                'contract',
                '2026-09-24',
                false,
                null,
                'settings'
            );

        $this->assertFalse(
            $consent->consent_required
        );

        $this->assertNull(
            $consent->granted
        );

        $this->assertNotNull(
            $consent->acknowledged_at
        );

        $this->assertDatabaseHas(
            'privacy_consents',
            [
                'id' => $consent->id,
                'user_id' => $user->id,
                'purpose' => 'account_privacy',
                'lawful_basis' => 'contract',
                'consent_required' => false,
                'granted' => null,
            ]
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'module' => 'privacy',
                'record_id' => (string) $consent->id,
                'record_label' => 'Privacy notice - account_privacy',
            ]
        );
    }

    public function test_consent_based_processing_requires_an_explicit_decision(): void
    {
        $user =
            User::factory()
                ->create([
                    'force_password_change' => false,
                ]);

        $this->expectException(
            ValidationException::class
        );

        app(
            PrivacyService::class
        )->recordForUser(
            $user,
            $user,
            'optional_processing',
            'consent',
            '2026-09-24',
            true,
            null,
            'settings'
        );
    }

    public function test_visitor_consent_can_be_recorded_by_authorized_staff_actor(): void
    {
        $actor =
            User::factory()
                ->role(
                    User::ROLE_RECEPTIONIST
                )
                ->create([
                    'force_password_change' => false,
                ]);

        $visitor =
            Visitor::create([
                'full_name' => 'Privacy Visitor',

                'visitor_type' => 'guest',

                'is_walk_in' => true,
            ]);

        $consent =
            app(
                PrivacyService::class
            )->recordForVisitor(
                $actor,
                $visitor,
                'visitor_management',
                'consent',
                '2026-09-24',
                true,
                true,
                'visitor_desk'
            );

        $this->assertTrue(
            $consent->consent_required
        );

        $this->assertTrue(
            $consent->granted
        );

        $this->assertNotNull(
            $consent->granted_at
        );

        $this->assertDatabaseHas(
            'privacy_consents',
            [
                'visitor_id' => $visitor->id,

                'recorded_by_user_id' => $actor->id,

                'purpose' => 'visitor_management',

                'lawful_basis' => 'consent',

                'granted' => true,
            ]
        );
    }

    public function test_privacy_request_starts_pending_and_is_audited(): void
    {
        $user =
            User::factory()
                ->create([
                    'force_password_change' => false,
                ]);

        $privacyRequest =
            app(
                PrivacyService::class
            )->submitRequest(
                $user,
                PrivacyRequest::TYPE_ERASURE,
                'Please review personal data that is no longer required.',
                true
            );

        $this->assertSame(
            PrivacyRequest::STATUS_PENDING,
            $privacyRequest->status
        );

        $this->assertNotNull(
            $privacyRequest->identity_verified_at
        );

        $this->assertNotNull(
            $privacyRequest->submitted_at
        );

        $this->assertDatabaseHas(
            'privacy_requests',
            [
                'id' => $privacyRequest->id,

                'user_id' => $user->id,

                'type' => PrivacyRequest::TYPE_ERASURE,

                'status' => PrivacyRequest::STATUS_PENDING,
            ]
        );

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'module' => 'privacy',

                'record_id' => (string) $privacyRequest->id,
            ]
        );
    }

    public function test_duplicate_unresolved_privacy_request_is_rejected(): void
    {
        $user =
            User::factory()
                ->create([
                    'force_password_change' => false,
                ]);

        $service =
            app(
                PrivacyService::class
            );

        $service->submitRequest(
            $user,
            PrivacyRequest::TYPE_BLOCKING,
            null,
            true
        );

        try {
            $service->submitRequest(
                $user,
                PrivacyRequest::TYPE_BLOCKING,
                null,
                true
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'type',
                $exception->errors()
            );

            $this->assertDatabaseCount(
                'privacy_requests',
                1
            );

            return;
        }

        $this->fail(
            'Duplicate unresolved privacy request was accepted.'
        );
    }

    public function test_completed_request_allows_a_later_request_of_same_type(): void
    {
        $user =
            User::factory()
                ->create([
                    'force_password_change' => false,
                ]);

        $service =
            app(
                PrivacyService::class
            );

        $first =
            $service->submitRequest(
                $user,
                PrivacyRequest::TYPE_WITHDRAW_CONSENT,
                null,
                true
            );

        $first->update([
            'status' => PrivacyRequest::STATUS_COMPLETED,

            'completed_at' => now(),
        ]);

        $second =
            $service->submitRequest(
                $user,
                PrivacyRequest::TYPE_WITHDRAW_CONSENT,
                null,
                true
            );

        $this->assertNotSame(
            $first->id,
            $second->id
        );

        $this->assertDatabaseCount(
            'privacy_requests',
            2
        );
    }
}
